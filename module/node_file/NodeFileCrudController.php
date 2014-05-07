<?php
namespace module\node_file;

use module\crud\CrudController;
use module\node\NodeCrudController;
use system\model2\RecordsetInterface;
use system\exceptions\PageNotFound;

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
        $nodeFile = NodeFileRecordsetCache::getInstance()->loadById($this->getUrlArg(2));
        if (empty($nodeFile)) {
          throw new PageNotFound();
        }
        return array('node_file' => NodeFileRecordsetCache::getInstance()->loadById($this->getUrlArg(2)));
        break;
    }
  }

  protected function getFormId() {
    return 'node-file-form';
  }

  protected function getFormTemplate() {
    return 'edit-node-file';
  }

  protected function submitUpdate() {
    $form = $this->getForm();

    $nodeFile = $form->getRecordset('node_file');

    $nodeFile->save();
  }

  public function runDelete() {
    $nodeFile = NodeFileRecordsetCache::getInstance()->loadById($this->getUrlArg(2));

    if (empty($nodeFile)) {
      throw new PageNotFound();
    }

    $nodeFile->delete();

    echo json_encode(array('files' => array($nodeFile->virtual_name => true)));

    return null;
  }
}