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
	
	public function runAdd() {
		$rsb = new RecordsetBuilder('node');
		$rsb->usingAll();
		$recordset = $rsb->newRecordset();
		
		$recordset->type = 'page';
		
		$this->datamodel["node"] = $recordset;

//		$this->setMainTemplate('edit-content-page');
//		$this->datamodel['errors'] = array();
//		return Component::RESPONSE_TYPE_FORM;
		$this->setMainTemplate('edit-content-page');

		if (\array_key_exists("node", $this->getRequestData())) {
			$errors = array();
			$posted = \module\core\Core::loadRSFormData($recordset, $errors, array("urn"));
			$posted = $posted && $recordset->checkKey("urn_key", $errors);

			foreach (\config\settings()->LANGUAGES as $lang) {
				$posted = \module\core\Core::loadRSFormData($recordset, $errors, array(
					"text_" . $lang . ".urn",
					"text_" . $lang . ".title",
					"text_" . $lang . ".subtitle",
					"text_" . $lang . ".body",
					"text_" . $lang . ".preview"
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
				if (!($recordset->__get('text_' . \config\settings()->DEFAULT_LANG)->title)) {
					$errors['text_' . \config\settings()->DEFAULT_LANG . '.title'] = \system\Lang::translate("Please insert title for the website default language.");
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
						\system\Utils::getParam("record_mode.read_mode", $_REQUEST["node"], array('default' => null)),
						\system\Utils::getParam("record_mode.edit_mode", $_REQUEST["node"], array('default' => null)),
						\system\Utils::getParam("record_mode.delete_mode", $_REQUEST["node"], array('default' => null))
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
		} else {
			return \system\logic\Component::RESPONSE_TYPE_FORM;
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