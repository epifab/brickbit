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
	private static function accessRED($action, $id, $user) {
		$rsb = new RecordsetBuilder('node');
		$rsb->addFilter(new FilterClause($rsb->id, '=', $id));
		
		if ($rsb->countRecords() > 0) {
			switch ($action) {
				case "READ":
					$rsb->addReadModeFilters($user);
					break;
				case "EDIT":
					$rsb->addEditModeFilters($user);
					break;
				case "DELETE":
					$rsb->addDeleteModeFilters($user);
					break;
			}
			return $rsb->countRecords() > 0;
		}
		return true;
	}

	///<editor-fold defaultstate="collapsed" desc="Access methods">
	public static function accessAdd($urlArgs, $request, $user) {
		$nodeTypes = \system\Cache::nodeTypes();
		
		if (isset($nodeTypes[$urlArgs[0]])) {
			throw new \system\InternalErrorException('Invalid node type.');
		}
		if (!\in_array($urlArgs[0], $nodeTypes['#'])) {
			return false;
		}
		
		// only superuser is able to add nodes to the root
		return $user && $user->superuser;
	}
	
	public static function accessAdd2Node($urlArgs, $request, $user) {
		$nodeTypes = \system\Cache::nodeTypes();
		
		if (isset($nodeTypes[$urlArgs[1]])) {
			throw new \system\InternalErrorException('Invalid node type.');
		}
		
		// get the parent node
		$rsb = new RecordsetBuilder('node');
		$rsb->using('type');
		$rsb->addFilter(new FilterClause($rsb->id, '=', $urlArgs[0]));
		$rsb->addEditModeFilters($user); // Check if the logged user has sufficient permissions to edit the parent node
		$parentNode = $rsb->selectFirst();
		
		if (!$parentNode) {
			return false;
		}
		// edit permissions ok
		
		// just need to check that is allowed to add the node
		if (!\in_array($urlArgs[1], @$nodeTypes[$parentNode->type]['children'])) {
			return false;
		}
		return true;
	}
	
	public static function accessRead($urlArgs, $request, $user) {
		return self::accessRED("READ", $urlArgs[0], $user);
	}

	public static function accessEdit($urlArgs, $request, $user) {
		return self::accessRED("EDIT", $urlArgs[0], $user);
	}

	public static function accessDelete($urlArgs, $request, $user) {
		return self::accessRED("DELETE", $urlArgs[0], $user);
	}

	public static function accessReadByUrn($urlArgs, $request, $user) {
		list($urn) = $urlArgs;
		
		$rsb = new RecordsetBuilder('node');
		$rsb->using("text.urn");
		$rsb->addFilter(new FilterClause($rsb->text->urn, '=', $urn));
		
		if ($rsb->countRecords() > 0) {
			$rsb->addReadModeFilters($user);
			return $rsb->countRecords() > 0;
		}
		return true;
	}
	///</editor-fold>

	protected function onInit() {
		
	}

	protected function form($recordset, $errors=array()) {
		$nodeTypes = \system\Cache::nodeTypes();
		
		$this->datamodel['fileKeys'] = $nodeTypes[$recordset->type]['files'];
		$this->datamodel['recordset'] = $recordset;
		$this->datamodel['errors'] = $errors;
		$this->setMainTemplate('edit-node-form');
		// plupload
		$this->addJs(\system\logic\Module::getAbsPath('core', 'js') . 'plupload/js/plupload.full.js');
		$this->addJs(\system\logic\Module::getAbsPath('core', 'js') . 'plupload/js/jquery.plupload.queue/jquery.plupload.queue.js');
		// css
//		$this->addCss(\system\logic\Module::getAbsPath('core', 'js') . 'plupload/js/jquery.ui.plupload/css/jquery.ui.plupload.css');
		$this->addCss(\system\logic\Module::getAbsPath('core', 'js') . 'plupload/js/jquery.plupload.queue/css/jquery.plupload.queue.css');
		return \system\logic\Component::RESPONSE_TYPE_FORM;
	}
	
	protected function submit($recordset) {
		$da = \system\model\DataLayerCore::getInstance();
		$da->beginTransaction();
		
		try {
			$temp = $recordset->temp;

			$recordset->temp = false;

			$request = $this->getRequestData();
			if (\system\Login::getLoggedUser()->superuser) {
				$read = \cb\array_item("record_mode.read_mode", $request['recordset'], array('default' => null));
				$edit = \cb\array_item("record_mode.edit_mode", $request['recordset'], array('default' => null));
				$delete = \cb\array_item("record_mode.delete_mode", $request['recordset'], array('default' => null));
			} else {
				$read = null;
				$edit = null;
				$delete = null;
			}

			// saving the recordset
			$recordset->save($read, $edit, $delete);

			foreach ($recordset->texts as $t) {
				// deleting old texts
				$t->delete();
			}

			foreach ($postedTexts as $lang) {
				// saving new texts
				$textRs = $recordset->__get('text_' . $lang);
				$textRs->node_id = $recordset->id;
				$textRs->create();
			}

			$da->commitTransaction();

			if ($temp) {
				\system\Utils::unsetSession('core', 'temp_node_id');
			}

			return $this->notify();
		}
		
		catch (\Exception $ex) {
			$da->rollbackTransaction();
			throw $ex;
		}
	}
	
	protected function notify($title="Operation completed", $body="Operation completed") {
		$this->datamodel['message'] = array(
			'title' => $title,
			'body' => $body
		);
		$this->setMainTemplate('notify');
		return \system\logic\Component::RESPONSE_TYPE_NOTIFY;
	}
	
	protected function error($title="", $body="") {
		$this->datamodel['message'] = array(
			'title' => $title,
			'body' => $body
		);
		$this->setMainTemplate('notify');
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

	protected function getTempNode($type=null, $parentId=null) {
		$parentNode = null;
		if ($parentId) {
			$prsb = new RecordsetBuilder('node');
			$prsb->using("*");
			$parentNode = $prsb->selectFirstBy(array('id' => $parentId));
			if (!$parentNode) {
				throw new \system\InternalErrorException('The node you were looking for was not found.');
			}
		}
		
		$rsb = new RecordsetBuilder('node');
		$rsb->usingAll();
		
		$recordset = null;

		// always handle with a temporary node
		$node_id = \system\Utils::getSession('core', 'temp_node_id', null);
		if ($node_id) {
			$recordset = $rsb->selectFirstBy(array('id' => $node_id));
			if (!$recordset->temp) {
				$recordset = null;
			} else if ($recordset->record_mode->owner_id != \system\Login::getLoggedUserId()) {
				$recordset = null;
			} else if ($recordset->type != $type) {
				// delete the previous temp content
				$recordset->delete();
				$recordset = null;
			}
		}

		if (!$recordset) {
			$da = \system\model\DataLayerCore::getInstance();
			$da->beginTransaction();
			
			try {
				$recordset = $rsb->newRecordset();
				$recordset->temp = true;
				$recordset->type = $type;

				if (!$parentNode) {
					$recordset->parent_id = null;
					$recordset->ldel = 1 + $da->executeScalar("SELECT MAX(rdel) FROM node", __FILE__, __LINE__);
				} else {
					$recordset->parent_id = $parentNode->id;
					$recordset->ldel = $parentNode->rdel;
				}
				$recordset->rdel = $recordset->ldel + 1;

				if ($parentNode) {
					$offset = $recordset->rdel - $recordset->ldel + 1;
					$da->executeUpdate("UPDATE node SET ldel = ldel + " . $offset . " WHERE ldel > " . $recordset->rdel, __FILE__, __LINE__);
					$da->executeUpdate("UPDATE node SET rdel = rdel + " . $offset . " WHERE rdel >= " . $recordset->ldel, __FILE__, __LINE__);
				}

				$recordset->sort_index = 1 + $da->executeScalar(
					"SELECT MAX(sort_index) FROM node"
					. " WHERE " . ($parentNode ? "parent_id = " . $parentNode->id : "parent_id IS NULL")
						, __FILE__, __LINE__);

				$recordset->save(
					// default record mode options
					\system\model\RecordMode::MODE_SU_OWNER,
					\system\model\RecordMode::MODE_SU_OWNER,
					\system\model\RecordMode::MODE_SU_OWNER
				);
				
				$da->commitTransaction();
				
				\system\Utils::setSession('core', 'temp_node_id', $recordset->id);
			}
			
			catch (\Exception $ex) {
				$da->rollbackTransaction();
				throw $ex;
			}
		}
		return $recordset;
	}

//	protected function edit($recordset, $parentNode=null) {
//		
//		if (\array_key_exists("recordset", $this->getRequestData())) {
//
//			$errors = array();
//			$posted = true;
//
//			$postedTexts = array();
//			
//			foreach (\config\settings()->LANGUAGES as $lang) {
//				$req = $this->getRequestData();
//
//				$rs = $recordset->__get('text_' . $lang);
//
//				if (\array_key_exists('text_' . $lang . '.enable', $req['node'])) {
//					$posted = \module\core\Utils::loadRSFormData($recordset, $errors, array(
//						"text_" . $lang . ".urn",
//						"text_" . $lang . ".description",
//						"text_" . $lang . ".title",
//						"text_" . $lang . ".subtitle",
//						"text_" . $lang . ".body",
//						"text_" . $lang . ".preview"
//					)) && $posted;
//
//					if (!$rs->urn) {
//						$errors["text_" . $lang . ".urn"] = \system\Lang::translate("Please insert <em>@name</em>.", array('@name' => 'URN'));
//						$posted = false;
//					}
//					if (!$rs->title) {
//						$errors["text_" . $lang . ".title"] = \system\Lang::translate("Please insert <em>@name</em>.", array('@name' => 'title'));
//						$posted = false;
//					}
//					$rs->lang = $lang;
//					
//					$postedTexts[] = $lang;
//				}
//			}
//
//			if (!$posted) {
//				return $this->editForm($recordset, $errors);
//			}
//
//			else {
//				try {
//					$da = \system\model\DataLayerCore::getInstance();
//					$da->beginTransaction();
//
//					$temp = $recordset->temp;
//					
//					if ($temp) {
//						$recordset->temp = false;
//						if (!$parentNode) {
//							$recordset->parent_id = null;
//							$recordset->ldel = 1 + $da->executeScalar("SELECT MAX(rdel) FROM node", __FILE__, __LINE__);
//						} else {
//							$recordset->parent_id = $parentNode->id;
//							$recordset->ldel = $parentNode->rdel;
//						}
//						$recordset->rdel = $recordset->ldel + 1;
//					
//						if ($parentNode) {
//							$offset = $recordset->rdel - $recordset->ldel + 1;
//							$da->executeUpdate("UPDATE node SET ldel = ldel + " . $offset . " WHERE ldel > " . $recordset->rdel, __FILE__, __LINE__);
//							$da->executeUpdate("UPDATE node SET rdel = rdel + " . $offset . " WHERE rdel >= " . $recordset->ldel, __FILE__, __LINE__);
//						}
//
//						$recordset->sort_index = 1 + $da->executeScalar(
//							"SELECT MAX(sort_index) FROM node"
//							. " WHERE " . ($parentNode ? "parent_id = " . $parentNode->id : "parent_id IS NULL")
//								, __FILE__, __LINE__);
//					}
//					
//					$request = $this->getRequestData();
//					if (\system\Login::getLoggedUser()->superuser) {
//						$read = \cb\array_item("record_mode.read_mode", $request['recordset'], array('default' => null));
//						$edit = \cb\array_item("record_mode.edit_mode", $request['recordset'], array('default' => null));
//						$delete = \cb\array_item("record_mode.delete_mode", $request['recordset'], array('default' => null));
//					} else {
//						$read = null;
//						$edit = null;
//						$delete = null;
//					}
//					
//					// saving the recordset
//					$recordset->save($read, $edit, $delete);
//					
//					foreach ($recordset->texts as $t) {
//						// deleting old texts
//						$t->delete();
//					}
//					
//					foreach ($postedTexts as $lang) {
//						// saving new texts
//						$textRs = $recordset->__get('text_' . $lang);
//						$textRs->node_id = $recordset->id;
//						$textRs->create();
//					}
//
//					$da->commitTransaction();
//					
//					if ($temp) {
//						\system\Utils::unsetSession('core', 'temp_node_id');
//					}
//					
//					return $this->notify();
//
//				} catch (\Exception $ex) {
//					$da->rollbackTransaction();
//					throw $ex;
//				}
//			}
//		} else {
//			return $this->editForm($recordset);
//		}
//	}
	
	///<editor-fold defaultstate="collapsed" desc="Run action methods">
	public function runSearch() {
			
	}
	
	public function runRead() {
		$rsb = new RecordsetBuilder('node');
		$rsb->usingAll();
		$rs = $rsb->selectFirstBy(array('id' => $this->getUrlArg(0)));
		if (!$rs) {
			return $this->runNotFound();
		}
		return $this->read($rs);
	}
	
	public function runReadByUrn() {
		$rsb = new RecordsetBuilder('node');
		$rsb->usingAll();
		$rs = $rsb->selectFirstBy(array('text.urn' => $this->getUrlArg(0)));
		if (!$rs) {
			return $this->runNotFound();
		}
		return $this->read($rs);
	}

	public function runNotFound() {
		$this->setPageTitle(\system\Lang::translate('Page not found'));
		$this->setMainTemplate('notify');
		$this->datamodel['message'] = \system\Lang::translate('The page you were looking was not found.');
		return Component::RESPONSE_TYPE_NOTIFY;
	}
	
	public function runAdd2Node() {
		$recordset = $this->getTempNode($this->getUrlArg(1), $this->getUrlArg(0));
		return $this->formAdd($recordset);
	}
	
	public function runAdd() {
		$recordset = $this->getTempNode($this->getUrlArg(0));
		return $this->formAdd($recordset);
	}
	
	public function formAdd($recordset, $formErrors=array()) {
		return $this->form($recordset, $formErrors);
	}
	
	public function submitAdd($recordset) {
		return $this->submit($recordset);
	}
	
	public function runEdit() {
		$rsb = new RecordsetBuilder('node');
		$rsb->usingAll();
		$recordset = $rsb->selectFirstBy(array('id' => $this->getUrlArg(0)));
		if (!$recordset) {
			throw new \system\InternalErrorException('The content you were looking for was not found.');
		}
		
		if ($recordset->text->title) {
			$this->setPageTitle(\system\Lang::translate('@name | edit', array('@name' => $recordset->text->title)));
		} else {
			$this->setPageTitle(\system\Lang::translate('Edit content'));
		}
		
		return $this->formEdit($recordset);
	}
	
	public function formEdit($recordset, $formErrors=array()) {
		return $this->form($recordset, $formErrors);
	}
	
	public function submitEdit($recordset) {
		return $this->submit($recordset);
	}
	
	public function runDelete() {
		$rsb = new RecordsetBuilder('node');
		$rs = $rsb->selectFirstBy(array('id' => $this->getUrlArg(0)));
		if ($rs) $rs->delete();
		return $this->notify();
	}
	///</editor-fold>
}
?>