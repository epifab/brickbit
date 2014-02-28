<?php
namespace module\autocomplete\components;

class Node extends \system\Component {
  public function runNodes() {
    $q = $_REQUEST['q'];
    $rsb = new \system\model\RecordsetBuilder('node');
    $rsb->using('*');
    
    $rsb->setLimit(new \system\model\LimitClause(30));
    
    $rs = $rsb->selectBy(array('text.title' => $q));
    
    if (!empty($rs)) {
      echo \module\autocomplete\Autocomplete::autocompleteNodes($rs);
    } else {
      echo \json_encode(array());
    }
    return NULL;
  }
  
  public function runUsers() {
    $q = $_REQUEST['q'];
    $rsb = new \system\model\RecordsetBuilder('user');
    $rsb->using('*');
    
    $rsb->setLimit(new \system\model\LimitClause(30));

    $rs = $rsb->selectBy(array('full_name' => $q));
    
    if (!empty($rs)) {
      echo \module\autocomplete\Autocomplete::autocompleteNodes($rs);
    } else {
      echo \json_encode(array());
    }
    return NULL;
  }
}