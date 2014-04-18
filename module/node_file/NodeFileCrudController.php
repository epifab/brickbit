<?php
namespace module\node_file;

use module\crud\CrudController;
use module\node\NodeCrudController;
use system\model2\RecordsetInterface;

use system\model2\Table;

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
        $t = $this->nodeFileTable();
        $t->addFilters(
          $t->filter('node_id', $nodeId),
          $t->filter('node_index', $nodeIndex),
          $t->filter('virtual_name', $virtualName)
        );
        return array('node_file' => $t->selectFirst());
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
    list($nodeId, $nodeIndex, $fileId) = $this->getUrlArgs();
    
    $t = $this->nodeFileTable();
    
    $nodeFile = $t->selectFirst($t->filterGroup('AND')->addClauses(
      $t->filter('node_id', $nodeId),
      $t->filter('node_index', $nodeIndex),
      $t->filter('file_id', $fileId)
    ));
    
    if (empty($nodeFile)) {
      throw new PageNotFound();
    }
    
    $nodeFile->delete();
    
    echo json_encode(array('files' => array($nodeFile->virtual_name => true)));
    
    return null;
  }
}