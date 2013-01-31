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
	
	public static function accessCreate($urlArgs, $request, $userId) {
		$rsb = new RecordsetBuilder('user');
		$rsb->using('*');
		$user = $rsb->selectFirstBy('id', $userId);
		return $user && $user->superuser;
	}
	
	public static function accessRED($action, $id) {
		$rsb = new RecordsetBuilder('node');
		$rsb->addFilter(new FilterClause($rsb->id, '=', $id));
		
		if ($rsb->countRecords() > 0) {
			switch ($action) {
				case "READ":
					$rsb->addReadModeFilters();
					break;
				case "EDIT":
					$rsb->addEditModeFilters();
					break;
				case "DELETE":
					$rsb->addDeleteModeFilters();
					break;
			}
			return $rsb->countRecords() > 0;
		}
		return true;
	}
	
	public static function accessRead($urlArgs, $request, $userId) {
		return self::accessRED("READ", $urlArgs[0]);
	}

	public static function accessEdit($urlArgs, $request, $userId) {
		return self::accessRED("EDIT", $urlArgs[0]);
	}

	public static function accessDelete($urlArgs, $request, $userId) {
		return self::accessRED("DELETE", $urlArgs[0]);
	}

	public static function accessReadByUrn($urlArgs, $request, $userId) {
		list($urn) = $urlArgs;
		
		$rsb = new RecordsetBuilder('node');
		$rsb->using("text.urn");
		$rsb->addFilter(new FilterClause($rsb->text->urn, '=', $urn));
		
		if ($rsb->countRecords() > 0) {
			$rsb->addReadModeFilters();
			return $rsb->countRecords() > 0;
		}
		return true;
	}

	protected function onInit() {
		switch ($this->getRequestType()) {
			case "MAIN-PANELS":
				$this->setOutlineWrapperTemplate('outline-wrapper-main-panels');
				break;
			case "MAIN":
				$this->setOutlineWrapperTemplate('outline-wrapper-main');
				break;
			case "PAGE-PANELS":
				$this->setOutlineWrapperTemplate('outline-wrapper-page-panels');
				break;
			case "PAGE":
			default:
				$this->setOutlineWrapperTemplate(null);
				break;
		}
		$this->setOutlineTemplate('outline');
		$this->addTemplate('header', 'header');
		$this->addTemplate('footer', 'footer');
//		$this->addTemplate('sidebar', 'sidebar');

		$mm = array();
		
		$rsb = new RecordsetBuilder("node");
		$rsb->using(
			'id', 'type', 'read_url', 'text.title'
		);
		$rsb->addFilter(new FilterClause($rsb->type, '=', 'page'));
		$rsb->addFilter(new FilterClause($rsb->text->title, 'IS_NOT_NULL'));
		$rsb->addReadModeFilters();
		
		$rs = $rsb->select();
		foreach ($rs as $r) {
			$mm[] = array(
				'id' => $r->id,
				'url' => $r->read_url,
				'title' => $r->text->title
			);
		}
		
		$this->datamodel['page']['mainMenu'] = $mm;
		
		$this->addJs(\system\logic\Module::getAbsPath('core', 'js') . 'core.js');
		$this->addCss(\system\Theme::getThemePath() . 'css/upload-jquery/jquery.fileupload-ui.css');
	}

	protected function editForm($node, $errors=array()) {
		$this->datamodel["node"] = $node;
		$this->datamodel["errors"] = $errors;
		$this->setMainTemplate("edit-node-form");
		return \system\logic\Component::RESPONSE_TYPE_FORM;
	}
	
	protected function notify($title="", $message="") {
		$this->datamodel["title"] = $title;
		$this->datamodel["message"] = $message;
		$this->setMainTemplate('notify');
		return \system\logic\Component::RESPONSE_TYPE_NOTIFY;
	}
	
	protected function error($title="", $message="") {
		$this->datamodel["title"] = $title;
		$this->datamodel["message"] = $message;
		$this->setMainTemplate('error');
		return \system\logic\Component::RESPONSE_TYPE_ERROR;
	}
	
	protected function read($rs) {
		$this->datamodel['node'] = $rs;
		$this->setMainTemplate('node');
		if ($rs->text->title) {
			$this->setPageTitle($rs->text->title);
		}
		return Component::RESPONSE_TYPE_READ;
	}
	
	public static function getTypes() {
		return array(
			'page', 'article'
		);
	}
	
	public function runSearch() {
			
	}
	
	public function runNotFound() {
		$this->setPageTitle(\system\Lang::translate('Page not found'));
		$this->setMainTemplate('notify');
		$this->datamodel['message'] = \system\Lang::translate('The page you were looking was not found.');
		return Component::RESPONSE_TYPE_NOTIFY;
	}
	
	public function runAdd2Node() {
		return $this->runAdd($this->getUrlArg(0), $this->getUrlArg(1));
	}
	
	public function runAdd($parentId=null, $type=null) {
		if (!$type) {
			$type = $this->getUrlArg(0);
		}
		
		$parentNode = null;
		if ($parentId) {
			$prsb = new RecordsetBuilder('node');
			$prsb->using("*");
			$parentNode = $prsb->selectFirstBy("id", $parentId);
			if (!$parentNode) {
				throw new \system\InternalErrorException(\system\Lang::translate('The node you were looking for was not found.'));
			}
		}
		
		$rsb = new RecordsetBuilder('node');
		$rsb->usingAll();
		$recordset = $rsb->newRecordset();
		
		if (!\in_array($type, $this->getTypes())) {
			throw new \system\InternalErrorException(\system\Lang::translate('Invalid node type <em>@type</em>', array('@type' => $this->getUrlArg(0))));
		}
		
		$this->setPageTitle(\t('Create new @name content', array('@name' => $type)));
		
		$recordset->type = $this->getUrlArg(0);
		
		if (\array_key_exists("node", $this->getRequestData())) {
			$errors = array();
			$posted = true;

			$postedTexts = array();
			
			foreach (\config\settings()->LANGUAGES as $lang) {
				$req = $this->getRequestData();

				$rs = $recordset->__get('text_' . $lang);

				if (\array_key_exists('text_' . $lang . '.enable', $req['node'])) {
					$posted = \module\core\Core::loadRSFormData($recordset, $errors, array(
						"text_" . $lang . ".urn",
						"text_" . $lang . ".description",
						"text_" . $lang . ".title",
						"text_" . $lang . ".subtitle",
						"text_" . $lang . ".body",
						"text_" . $lang . ".preview"
					)) && $posted;

					if (!$rs->urn) {
						$errors["text_" . $lang . ".urn"] = \system\Lang::translate("Please insert <em>@name</em>.", array('@name' => 'URN'));
						$posted = false;
					}
					if (!$rs->title) {
						$errors["text_" . $lang . ".title"] = \system\Lang::translate("Please insert <em>@name</em>.", array('@name' => 'title'));
						$posted = false;
					}
					$rs->lang = $lang;
					
					$postedTexts[] = $lang;
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

					if (!$parentNode) {
						$recordset->parent_id = null;
						$recordset->ldel = 1 + $da->executeScalar("SELECT MAX(rdel) FROM node", __FILE__, __LINE__);
					} else {
						$recordset->parent_id = $parentNode->id;
						$recordset->ldel = $parentNode->rdel;
					}
					$recordset->rdel = $recordset->ldel + 1;

					if ($parentNode) {
						$da->executeUpdate("UPDATE node SET ldel = ldel + 2 WHERE ldel > " . $recordset->rdel, __FILE__, __LINE__);
						$da->executeUpdate("UPDATE node SET rdel = rdel + 2 WHERE rdel >= " . $recordset->ldel, __FILE__, __LINE__);
					}

					$recordset->sort_index = 1 + $da->executeScalar(
						"SELECT MAX(sort_index) FROM node"
						. " WHERE " . ($parentNode ? "parent_id = " . $parentNode->id : "parent_id IS NULL")
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
					
					foreach ($postedTexts as $lang) {
						// Inserisco tutti i nuovi testi
						$textRs = $recordset->__get('text_' . $lang);
						$textRs->node_id = $recordset->id;
						$textRs->create();
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
		$rsb = new RecordsetBuilder('node');
		$rsb->usingAll();
		$rs = $rsb->selectFirstBy('id', $this->getUrlArg(0));
		if (!$rs) {
			return $this->runNotFound();
		}
		return $this->read($rs);
	}
	
	public function runReadByUrn() {
		$rsb = new RecordsetBuilder('node');
		$rsb->usingAll();
		$rs = $rsb->selectFirstBy('text.urn', $this->getUrlArg(0));
		if (!$rs) {
			return $this->runNotFound();
		}
		return $this->read($rs);
	}
	
	public function runEdit() {
		$rsb = new RecordsetBuilder('node');
		$rsb->usingAll();
		$recordset = $rsb->selectFirstBy("id", $this->getUrlArg(0));
		if (!$recordset) {
			throw new \system\InternalErrorException(\system\Lang::translate('The content you were looking for was not found.'));
		}
		
		if ($recordset->text->title) {
			$this->setPageTitle(\system\Lang::translate('@name | edit', array('@name' => $recordset->text->title)));
		} else {
			$this->setPageTitle(\system\Lang::translate('Edit content'));
		}
		
		if (\array_key_exists("node", $this->getRequestData())) {
			$errors = array();
			$posted = true;
			
			$postedTexts = array();
			
			foreach (\config\settings()->LANGUAGES as $lang) {
				$req = $this->getRequestData();

				$rs = $recordset->__get('text_' . $lang);

				if (\array_key_exists('text_' . $lang . '.enable', $req['node'])) {
					$posted = \module\core\Core::loadRSFormData($recordset, $errors, array(
						"text_" . $lang . ".urn",
						"text_" . $lang . ".title",
						"text_" . $lang . ".subtitle",
						"text_" . $lang . ".body",
						"text_" . $lang . ".preview"
					)) && $posted;

					if (!$rs->urn) {
						$errors["text_" . $lang . ".urn"] = \system\Lang::translate("Please insert <em>@name</em>.", array('@name' => 'URN'));
						$posted = false;
					}
					if (!$rs->title) {
						$errors["text_" . $lang . ".title"] = \system\Lang::translate("Please insert <em>@name</em>.", array('@name' => 'title'));
						$posted = false;
					}
					$rs->lang = $lang;
					
					$postedTexts[] = $lang;
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
					$recordset->update($read, $edit, $delete);

					foreach ($recordset->texts as $t) {
						// Deleting pre-existent text
						$t->delete();
					}
					
					foreach ($postedTexts as $lang) {
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
	
	public function runDelete() {
		
	}
}
?>