<?php
namespace module\node_file;

use module\crud\CrudController;
use module\node\NodeCrudController;
use system\model2\RecordsetInterface;

class NodeFileCrudController extends CrudController {
  public static function accessUpdate($urlArgs, RecordsetInterface $user) {
    return NodeCrudController::accessEdit(array($urlArgs[0]), $user);
  }
  
  public static function accessDelete($urlArgs, RecordsetInterface $user) {
    return NodeCrudController::accessEdit(array($urlArgs[0]), $user);
  }
  
  protected function getEditActions() {
    return array('Update');
  }
  
  protected function getEditRecordsets() {
    switch ($this->getAction()) {
      case 'Update':
        list($nodeId, $nodeIndex, $virtualName) = $this->getUrlArgs();
        return array('node_file' => NodeFileRecordsetCache::getInstance()->loadByUrlInfo(
          $nodeId, $nodeIndex, $virtualName
        ));
        break;
    }
  }

  protected function getFormId() {
    return 'node-file-form';
  }

  protected function getFormTemplate() {
    return 'edit-node-file';
  }
  
  public function runDelete() {
    $nodeFile = array('node_file' => NodeFileRecordsetCache::getInstance()->loadById($this->getUrlArg(2)));
    
    if (empty($nodeFile)) {
      throw new PageNotFound();
    }
    
    $nodeFile->delete();
    
    echo json_encode(array('files' => array($nodeFile->virtual_name => true)));
    
    return null;
  }
}