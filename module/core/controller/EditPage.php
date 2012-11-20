<?php
namespace module\core\controller;

use system\logic\Component;
use system\logic\EditComponent;
use system\InternalErrorException;
use module\core\model\XmcaRecordMode;

/**
 * Component EditPage.
 * XMCA PHP Generator 0.1 - Auto generated code
 * @author episoft
 */
class EditPage extends EditComponent {
	
	public static function checkPermission($args) {
		if (!\array_key_exists("id", $args)) {
			// Creazione nuova pagina
			return true;
		}
		else {
			// Modifica pagina esistente
			$pageBuilder = new \module\core\model\XmcaPage();
			$pageBuilder->using("id");
			$pageBuilder->setFilter(new \system\model\FilterClause($pageBuilder->id, "=", $args["id"]));
			if ($pageBuilder->countRecords()) {
				$pageBuilder->addEditModeFilters();
				return $pageBuilder->countRecords() == 1;				
			}
			return true;
		}
	}

	private static function movePage($recordset, $move) {
		if ($move > 0) {
			$up = false;
		} else if ($move < 0) {
			$up = true;
		} else {
			return \system\logic\Component::RESPONSE_TYPE_NOTIFY;
		}

		$dl = \system\model\DataLayerCore::getInstance();

		$query = 
			"SELECT COUNT(*)"
			. " FROM xmca_page"
			. " WHERE sort_index " . ($up ? "<=" : ">=") . " " . $recordset->sort_index;
		if ($dl->executeScalar($query, __FILE__, __LINE__) == 1) {
			// non c'è nessun item da spostare
			return \system\logic\Component::RESPONSE_TYPE_NOTIFY;
		}

		$dl->beginTransaction();
		try {
			$newSortIndex = $recordset->sort_index + $move;

			$query = 
				"UPDATE xmca_page SET sort_index = sort_index " . ($up ? "+" : "-") . " 1"
				. " WHERE sort_index " . ($up ? ">=" : "<=") . " " . $newSortIndex
				. " AND sort_index " . ($up ? "<" : ">") . " " . $recordset->sort_index;
			$dl->executeUpdate($query, __FILE__, __LINE__);

			$recordset->sort_index = $newSortIndex;

			$recordset->update();

			$dl->commitTransaction();

			return \system\logic\Component::RESPONSE_TYPE_NOTIFY;

		} catch (\Exception $ex) {
			$dl->rollbackTransaction();
			throw $ex;
		}
	}
	
	protected function getName() {
		return "EditPage";
	}
	
	protected function getTemplateForm() {
		return "EditPage";
	}
	
	protected function getTemplateNotify() {
		return "layout/Success";
	}
	
	public function onProcess() {
		$tmpBuilder = new \module\core\model\XmcaPageStyle();
		$tmpBuilder->using(
			"code",
			"description"
		);
		$tmpBuilder->setSort(new \system\model\SortClause($tmpBuilder->code, "ASC"));
		$this->datamodel["pageStyles"] = $tmpBuilder->select();
		
		$tmpBuilder = new \module\core\model\XmcaGroup();
		$tmpBuilder->using(
			"id",
			"name"
		);
		$tmpBuilder->setSort(new \system\model\SortClause($tmpBuilder->name, "ASC"));
		$tmpBuilder->setFilter(new \system\model\FilterClause($tmpBuilder->id, "<>", \module\core\model\XmcaGroup::ADMINS_GROUP_ID));
		$this->datamodel["groups"] = $tmpBuilder->select();
		
		$pageBuilder = new \module\core\model\XmcaPage();
		$pageBuilder->useAllKeys();
		$pageBuilder->using(
			"url",
			"title",
			"body",
			"style.code",
			"sort_index",
			"content_sorting",
			"content_paging",
			"content_filters"
		);
		$pageTexts = new \module\core\model\XmcaPageText();
		$pageTexts->using("lang_id", "page_id");
		$pageBuilder->setHasManyRelationBuilder("texts", $pageTexts);
		
		foreach (\config\settings()->LANGUAGES as $lang) {
			$pageBuilder->using(
				"text_" . $lang . ".title",
				"text_" . $lang . ".body"
			);
		}
		
		if (!\array_key_exists("id", $_REQUEST)) {
			$recordset = $pageBuilder->newRecordset();
		}
		else {
			$recordset = $pageBuilder->selectFirstBy("id", $_REQUEST["id"]);
			if (\is_null($recordset)) {
				throw new ValidationException("Id pagina non valido");
			}
			$this->setPageTitle("Modifica " . $recordset->getRead("title"));
			
			if (\array_key_exists("move", $_REQUEST)) {
				return $this->movePage($recordset, (int)$_REQUEST["move"]);
			}
		}
		
		$errors = array();
		$posted = 
			$this->loadData($recordset, $errors, array(
				"url",
				"style_code",
				"content_sorting",
				"content_paging",
				"content_filters"
			)) && $this->checkKey($recordset, $errors, "url_key")
			&& $this->checkHasOneRelation($recordset, $errors, "style");
		
		foreach (\config\settings()->LANGUAGES as $lang) {
			
			$posted = $this->loadData($recordset, $errors, array(
				"text_" . $lang . ".lang_id",
				"text_" . $lang . ".title",
				"text_" . $lang . ".body"
			)) && $posted;
			
			// Controllo che non ci siano versioni senza titolo ma con descrizione
			if (empty($recordset->__get("text_" . $lang)->title) && !empty($recordset->__get("text_" . $lang)->body)) {
				// Titolo vuoto, testo non vuoto
				$errors["text_" . $lang . ".title"] = "Nessun titolo specificato";
				$posted = false;
			}
		}
		
		if ($posted) {
			// Controllo titolo lingua principale
			if (!$recordset->__get('text_' . \config\settings()->DEFAULT_LANG)->getRead('title')) {
				$errors['text_' . \config\settings()->DEFAULT_LANG . '.title'] = "Il titolo della pagina nella lingua di default non pu&ograve; essere lasciato vuoto";
				$posted = false;
			}
		}
		
		if (!$posted) {
			$this->datamodel["recordset"] = $recordset;
			$this->datamodel["errors"] = $errors;
			return Component::RESPONSE_TYPE_FORM;
		}
		
		else {
			$da = \system\model\DataLayerCore::getInstance();
			$da->beginTransaction();
			
			try {
				if ($recordset->id) {
					foreach ($recordset->texts as $t) {
						// Cancello tutti i testi precedenti
						$t->delete();
					}
					if ($recordset->record_mode->owner_id == \system\Login::getLoggedUserId()) {
						// Permetto la modifica del record mode soltanto all'owner della pagina
						$recordset->update(
							$_REQUEST["recordset"]["record_mode.read_mode"],
							$_REQUEST["recordset"]["record_mode.edit_mode"],
							$_REQUEST["recordset"]["record_mode.group_id"]
						);
					} else {
						$recordset->update();
					}
				} else {
					// Calcolo l'indice di ordinamento
					$recordset->sort_index = 1 + $da->executeScalar("SELECT MAX(sort_index) FROM xmca_page", __FILE__, __LINE__);
					// Salvo il record mode
					$recordset->create(
						$_REQUEST["recordset"]["record_mode.read_mode"],
						$_REQUEST["recordset"]["record_mode.edit_mode"],
						$_REQUEST["recordset"]["record_mode.group_id"]
					);
				}
				
				foreach (\config\settings()->LANGUAGES as $lang) {
					// Inserisco tutti i nuovi testi
					$textRs = $recordset->__get('text_' . $lang);
					if ($textRs->getRead('title')) {
						$textRs->page_id = $recordset->id;
						$textRs->create();
					}
				}
				
				$da->commitTransaction();
				return Component::RESPONSE_TYPE_NOTIFY;
				
			} catch (\Exception $ex) {
				$da->rollbackTransaction();
				throw $ex;
			}
		}
	}
}
?>