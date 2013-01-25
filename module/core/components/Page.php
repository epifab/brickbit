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
		
		$rsb = new RecordsetBuilder('node');
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
	
	public static function accessAdd($urlArgs, $args, $userId) {
		return true;
	}
	
	protected function onInit() {
		switch ($this->getRequestType()) {
			case "MAIN-PANELS":
				$this->setOutlineTemplateWrapper('outline-wrapper-main-panels');
				break;
			case "MAIN":
				$this->setOutlineTemplateWrapper('outline-wrapper-main');
				break;
			case "PAGE-PANELS":
				$this->setOutlineTemplateWrapper('outline-wrapper-page-panels');
				break;
			case "PAGE":
			default:
				$this->setOutlineTemplateWrapper(null);
				break;
		}
		$this->setOutlineTemplate('outline');
		$this->addTemplate('header', 'header');
		$this->addTemplate('footer', 'footer');
		$this->addTemplate('sidebar', 'sidebar');
		$this->datamodel['page']['mainMenu'] = array();
		$this->addJs(\system\logic\Module::getAbsPath('core', 'js') . 'core.js');
	}

	public function runRead() {
		list($pageUrn) = $this->getUrlArgs();

		$rsb = new RecordsetBuilder('node');
		$rsb->usingAll();
		$this->datamodel['node'] = $rsb->selectFirstBy('urn', $pageUrn);
		
		$this->setMainTemplate('content-page');
		
		return \system\logic\Component::RESPONSE_TYPE_READ;
	}
	
	private function editForm($node, $errors=array()) {
		$this->datamodel["node"] = $node;
		$this->datamodel["errors"] = $errors;
		$this->setMainTemplate("edit-node-form");
		return \system\logic\Component::RESPONSE_TYPE_FORM;
	}
	
	private function notify($title="", $message="") {
		$this->datamodel["title"] = $title;
		$this->datamodel["message"] = $message;
		$this->setMainTemplate('notify');
		return \system\logic\Component::RESPONSE_TYPE_NOTIFY;
	}
	
	private function error($title="", $message="") {
		$this->datamodel["title"] = $title;
		$this->datamodel["message"] = $message;
		$this->setMainTemplate('error');
		return \system\logic\Component::RESPONSE_TYPE_ERROR;
	}
	
	public function runAdd() {
		$rsb = new RecordsetBuilder('node');
		$rsb->usingAll();
		$recordset = $rsb->newRecordset();
		
		$recordset->type = 'page';
		
		if (\array_key_exists("node", $this->getRequestData())) {
			$errors = array();
			$posted = true;
			
			foreach (\config\settings()->LANGUAGES as $lang) {
				$posted = \module\core\Core::loadRSFormData($recordset, $errors, array(
					"text_" . $lang . ".urn",
					"text_" . $lang . ".title",
					"text_" . $lang . ".subtitle",
					"text_" . $lang . ".body",
					"text_" . $lang . ".preview"
				)) && $posted;

				$rs = $recordset->__get('text_' . $lang);
				
				if (!$rs->urn && ($rs->title || $rs->subtitle || $rs->body || $rs->preview)) {
					$errors["text_" . $lang . ".urn"] = \system\Lang::translate("Please insert a URN.");
					$posted = false;
				}
			}

			// Controllo titolo lingua principale
			if (!($recordset->__get('text_' . \config\settings()->DEFAULT_LANG)->urn)) {
				$errors['text_' . \config\settings()->DEFAULT_LANG . '.title'] = \system\Lang::translate("Please insert a URN for the default language.");
				$posted = false;
			}

			if (!$posted) {
				return $this->editForm($recordset, $errors);
			}

			else {
				try {
					$da = \system\model\DataLayerCore::getInstance();
					$da->beginTransaction();

					$parent = null;

					$recordset->type = "page";

					if (!$parent) {
						$recordset->parent_id = null;
						$recordset->ldel = 1 + $da->executeScalar("SELECT MAX(rdel) FROM node", __FILE__, __LINE__);
					} else {
						$recordset->parent_id = $parent->id;
						$recordset->ldel = $parent->rdel;
					}
					$recordset->rdel = $recordset->ldel + 1;

					$da->executeUpdate("UPDATE node SET l_del = l_del + 2 WHERE l_del > " . $data["max_rdel"], __FILE__, __LINE__);
					$da->executeUpdate("UPDATE node SET r_del = r_del + 2 WHERE r_del >= " . $data["max_rdel"], __FILE__, __LINE__);

					if ($parent) {
						$parent->rdel = $parent->rdel + 2;
					}

					$recordset->sort_index = $data = $da->executeRow(
						"SELECT MAX(sort_index) FROM node"
						. " WHERE " . ($parent ? "parent_id = " . $parent->id : "parent_id IS NULL")
							, __FILE__, __LINE__);

					$recordset->sort_index = 1 + $data["max_sort_index"];
					
					$request = $this->getRequestData();
					if (\system\Login::getLoggedUser()->superuser) {
						$read = \system\Utils::getParam("record_mode.read_mode", $request["node"], array('default' => null));
						$edit = \system\Utils::getParam("record_mode.edit_mode", $request["node"], array('default' => null));
						$delete = \system\Utils::getParam("record_mode.delete_mode", $request["node"], array('default' => null));
					} else {
						$read = null;
						$edit = null;
						$delete = null;
					}

					// Salvo il record mode
					$recordset->create($read, $edit, $delete);
					
					foreach (\config\settings()->LANGUAGES as $lang) {
						// Inserisco tutti i nuovi testi
						$textRs = $recordset->__get('text_' . $lang);
						if ($textRs->urn) {
							$textRs->node_id = $recordset->id;
							$textRs->create();
						}
					}

					$da->commitTransaction();
					
					return $this->notify();

				} catch (\Exception $ex) {
					$da->rollbackTransaction();
					throw $ex;
				}
			}
		} else {
			return $this->editForm($recordset);
		}
	}
	
	public function runEdit() {
		list($pageUrn) = $this->getUrlArgs();
		
		$rsb = new RecordsetBuilder('node');
		$rsb->usingAll();
		$recordset = $rsb->selectFirstBy($pageUrn);
		
		$this->setMainTemplate('content-edit-page');
		
		if (\array_key_exists("node", $this->getRequestData())) {
			$errors = array();
			$posted = \module\core\Core::loadRSFormData($recordset, $errors, array("urn"));
			$posted = $posted && $recordset->checkKey("urn_key", $errors);

			foreach (\config\settings()->LANGUAGES as $lang) {
				$posted = \module\core\Core::loadRSFormData($recordset, $errors, array(
					"text_" . $lang . ".lang",
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
							\system\Utils::getParam("record_mode.read_mode", $_REQUEST["node"], array('default' => null)),
							\system\Utils::getParam("record_mode.edit_mode", $_REQUEST["node"], array('default' => null)),
							\system\Utils::getParam("record_mode.delete_mode", $_REQUEST["node"], array('default' => null)),
							\system\Utils::getParam("record_mode.role_id", $_REQUEST["node"], array('default' => null))
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