<?php
namespace module\node_file;

use system\Component;
use module\node\NodeCrudController;
use module\node\NodeRecordsetCache;

class TinymceNodeFileController extends Component {
  public static function accessPlugin($urlArgs, $user) {
    return NodeCrudController::accessEdit(array($urlArgs[0]), $user);
  }

  public function runPlugin() {
    $node = NodeRecordsetCache::getInstance()->loadById($this->getUrlArg(0));
    $this->datamodel['node'] = $node;
    $this->setOutlineWrapperTemplate(null);
    $this->setOutlineTemplate(null);
    $this->setMainTemplate('node-file-plugin');
    return \system\RESPONSE_TYPE_READ;
  }
}