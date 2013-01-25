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

class Node extends \system\logic\Component {
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
//		$this->addCss('http://blueimp.github.com/jQuery-Image-Gallery/css/jquery.image-gallery.min.css');
		$this->addCss(\system\Theme::getThemePath() . 'css/upload-jquery/jquery.fileupload-ui.css');
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

	
	public function runSearch() {
			
	}
	
	public function runAdd() {
		$rsb = new RecordsetBuilder('node');
		$rsb->usingAll();
		$recordset = $rsb->newRecordset();
		
		switch ($this->getUrlArg(0)) {
			case "page":
			case "article":
				break;
			default:
				throw new \system\InternalErrorException(\system\Lang::translate('Invalid node type <em>@type</em>', array('@type' => $this->getUrlArg(0))));
		}
		
		$recordset->type = $this->getUrlArg(0);
		
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
				} else if ($rs->urn) {
					$rs->lang = $lang;
				}
			}

			// Controllo titolo lingua principale
			if (!($recordset->__get('text_' . \config\settings()->DEFAULT_LANG)->urn)) {
				$errors['text_' . \config\settings()->DEFAULT_LANG . '.urn'] = \system\Lang::translate("Please insert a URN for the default language.");
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

					if (!$parent) {
						$recordset->parent_id = null;
						$recordset->ldel = 1 + $da->executeScalar("SELECT MAX(rdel) FROM node", __FILE__, __LINE__);
					} else {
						$recordset->parent_id = $parent->id;
						$recordset->ldel = $parent->rdel;
					}
					$recordset->rdel = $recordset->ldel + 1;

					$da->executeUpdate("UPDATE node SET ldel = ldel + 2 WHERE ldel > " . $recordset->rdel, __FILE__, __LINE__);
					$da->executeUpdate("UPDATE node SET rdel = rdel + 2 WHERE rdel >= " . $recordset->ldel, __FILE__, __LINE__);

					if ($parent) {
						$parent->rdel = $parent->rdel + 2;
					}

					$recordset->sort_index = 1 + $da->executeScalar(
						"SELECT MAX(sort_index) FROM node"
						. " WHERE " . ($parent ? "parent_id = " . $parent->id : "parent_id IS NULL")
							, __FILE__, __LINE__);
					
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
	
	public function runRead() {

	}
	
	public function runUpdate() {
		
	}
	
	public function runDelete() {
		
	}
}
?>