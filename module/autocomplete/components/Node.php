<?php
namespace module\autocomplete\components;

class Node extends \system\Component {
  public function runNodes() {
    $q = $_REQUEST['q'];
    $rsb = new \system\model\RecordsetBuilder('node');
    $rsb->using('*', 'text.*', 'image.*');
    
    $rsb->setLimit(new \system\model\LimitClause(30));
    
    // Select some nodes
    $rs = $rsb->selectBy(array('name' => $q));
    
    if (!empty($rs)) {
      echo \module\autocomplete\Autocomplete::autocompleteNodes($rs);
    } else {
      echo \json_encode(array());
    }
    return NULL;
  }
  
  public function runUsers() {
    $q = $_REQUEST['q'];
    $rsb = new \system\model\RecordsetBuilder('node');
    $rsb->usingAll();
    $rs = $rsb->selectBy(array('name' => $q));
    if (!empty($rs)) {
      echo \module\autocomplete\Autocomplete::autocompleteNodes($rs);
    } else {
      echo \json_encode(array());
    }
    return NULL;
  }
}