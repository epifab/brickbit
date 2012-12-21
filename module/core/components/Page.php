<?php
namespace module\core\components;

use \system\logic\Component;
use \system\model\Recordset;
use \system\model\RecordsetBuilder;
use \system\model\FilterClause;
use \system\model\FilterClauseGroup;
use \system\model\LimitClause;
use \system\model\SortClause;
use \system\model\SortClauseGroup;

class Page extends Component {

	public static function access($action, $urlArgs, $args, $userId) {
		if (empty($urlArgs)) {
			return true;
		}
		
		list($pageUrn) = $urlArgs;
		
		$rsb = new RecordsetBuilder('content');
		$rsb->using('urn', 'type');
		$rsb->setFilter(new FilterClauseGroup(
			new FilterClause($rsb->urn, '=', $pageUrn),
			'AND',
			new FilterClause($rsb->type, '=', 'page')
		));
		
		if ($rsb->countRecords() > 0) {
			switch ($action) {
				case "Edit":
					$rsb->addEditModeFilters();
					break;
				case "Delete":
					$rsb->addDeleteModeFilters();
					break;
				default:
					$rsb->addReadModeFilters();
					break;
			}
			return $rsb->countRecords() > 0;
		}
		return true;
	}
	
	public static function accessCreate($action, $urlArgs, $args, $userId) {
		
	}
	
	protected function init() {
		$this->datamodel['page']['mainMenu'] = array();
	}

	public function onRead() {
		$this->setOutlineTemplate('content-page');
		
		list($pageUrn) = $this->getUrlArgs();

		$rsb = new RecordsetBuilder('node');
		$rsb->usingAll();
		$this->datamodel['node'] = $rsb->selectFirstBy('urn', $pageUrn);
		
		return Component::RESPONSE_TYPE_READ;
	}
	
	public function onCreate() {
		$rsb = new RecordsetBuilder('node');
		$rsb->usingAll();
		$recordset = $rsb->selectFirstBy($pageUrn);

		return Component::RESPONSE_TYPE_FORM;

		if (\array_key_exists("recordset", $this->getRequestData())) {
			$errors = array();
			$posted = \module\core\Core::loadRSFormData($recordset, $errors, array("urn"));
			$posted = $posted && $recordset->checkKey("urn_key", $errors);

			foreach (\config\settings()->LANGUAGES as $lang) {
				$posted = \module\core\Core::loadRSFormData($recordset, $errors, array(
					"text_" . $lang . ".lang_id",
					"text_" . $lang . ".title",
					"text_" . $lang . ".body"
				)) && $posted;

				// Controllo che non ci siano versioni senza titolo ma con descrizione
				if (empty($recordset->__get("text_" . $lang)->title) && !empty($recordset->__get("text_" . $lang)->body)) {
					// Titolo vuoto, testo non vuoto
					$errors["text_" . $lang . ".title"] = \system\Lang::translate("Please insert title.");
					$posted = false;
				}
			}

			if ($posted) {
				// Controllo titolo lingua principale
				if (!$recordset->__get('text_' . \config\settings()->DEFAULT_LANG)->getRead('title')) {
					$errors['text_' . \config\settings()->DEFAULT_LANG . '.title'] = \system\Lang::translate("Please insert title.");
					$posted = false;
				}
			}

			if (!$posted) {
				$this->datamodel["node"] = $recordset;
				$this->datamodel["errors"] = $errors;
				return \system\logic\Component::RESPONSE_TYPE_FORM;
			}

			else {
				$da = \system\model\DataLayerCore::getInstance();
				$da->beginTransaction();

				$recordset->type = "page";
				// Calcolo l'indice di ordinamento
				$recordset->sort_index = 1 + $da->executeScalar("SELECT MAX(sort_index) FROM node WHERE type = 'page'", __FILE__, __LINE__);

				try {
					// Salvo il record mode
					$recordset->create(
						\system\Utils::getParam($_REQUEST["recordset"], "record_mode.read_mode", array('default' => null)),
						\system\Utils::getParam($_REQUEST["recordset"], "record_mode.edit_mode", array('default' => null)),
						\system\Utils::getParam($_REQUEST["recordset"], "record_mode.delete_mode", array('default' => null)),
						\system\Utils::getParam($_REQUEST["recordset"], "record_mode.role_id", array('default' => null))
					);
					
					foreach (\config\settings()->LANGUAGES as $lang) {
						// Inserisco tutti i nuovi testi
						$textRs = $recordset->__get('text_' . $lang);
						if ($textRs->getRead('title')) {
							$textRs->node_id = $recordset->id;
							$textRs->create();
						}
					}

					$da->commitTransaction();
					
					return \system\logic\Component::RESPONSE_TYPE_NOTIFY;

				} catch (\Exception $ex) {
					$da->rollbackTransaction();
					throw $ex;
				}
			}
		}
	}
	
	public function onEdit() {
		list($pageUrn) = $this->getUrlArgs();
		
		$rsb = new RecordsetBuilder('node');
		$rsb->usingAll();
		$recordset = $rsb->selectFirstBy($pageUrn);
		
		$this->setMainTemplate('content-edit-page');
		
		if (\array_key_exists("recordset", $this->getRequestData())) {
			$errors = array();
			$posted = \module\core\Core::loadRSFormData($recordset, $errors, array("urn"));
			$posted = $posted && $recordset->checkKey("urn_key", $errors);

			foreach (\config\settings()->LANGUAGES as $lang) {
				$posted = \module\core\Core::loadRSFormData($recordset, $errors, array(
					"text_" . $lang . ".lang_id",
					"text_" . $lang . ".title",
					"text_" . $lang . ".body"
				)) && $posted;

				// Controllo che non ci siano versioni senza titolo ma con descrizione
				if (empty($recordset->__get("text_" . $lang)->title) && !empty($recordset->__get("text_" . $lang)->body)) {
					// Titolo vuoto, testo non vuoto
					$errors["text_" . $lang . ".title"] = \system\Lang::translate("Please insert title.");
					$posted = false;
				}
			}

			if ($posted) {
				// Controllo titolo lingua principale
				if (!$recordset->__get('text_' . \config\settings()->DEFAULT_LANG)->getRead('title')) {
					$errors['text_' . \config\settings()->DEFAULT_LANG . '.title'] = \system\Lang::translate("Please insert title.");
					$posted = false;
				}
			}

			if (!$posted) {
				$this->datamodel["node"] = $recordset;
				$this->datamodel["errors"] = $errors;
				return \system\logic\Component::RESPONSE_TYPE_FORM;
			}

			else {
				$da = \system\model\DataLayerCore::getInstance();
				$da->beginTransaction();

				$recordset->type = "page";
				try {
					foreach ($recordset->texts as $t) {
						// Cancello tutti i testi precedenti
						$t->delete();
					}
					if ($recordset->record_mode->owner_id == \system\Login::getLoggedUserId()) {
						// Permetto la modifica del record mode soltanto all'owner della pagina
						$recordset->update(
							\system\Utils::getParam($_REQUEST["recordset"], "record_mode.read_mode", array('default' => null)),
							\system\Utils::getParam($_REQUEST["recordset"], "record_mode.edit_mode", array('default' => null)),
							\system\Utils::getParam($_REQUEST["recordset"], "record_mode.delete_mode", array('default' => null)),
							\system\Utils::getParam($_REQUEST["recordset"], "record_mode.role_id", array('default' => null))
						);
					} else {
						$recordset->update();
					}

					foreach (\config\settings()->LANGUAGES as $lang) {
						// Inserisco tutti i nuovi testi
						$textRs = $recordset->__get('text_' . $lang);
						if ($textRs->getRead('title')) {
							$textRs->node_id = $recordset->id;
							$textRs->create();
						}
					}

					$da->commitTransaction();
					return \system\logic\Component::RESPONSE_TYPE_NOTIFY;

				} catch (\Exception $ex) {
					$da->rollbackTransaction();
					throw $ex;
				}
			}
		}
	}
}
?>