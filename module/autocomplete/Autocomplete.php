<?php
namespace module\autocomplete;

use system\Main;

class Autocomplete {
  public static function widgetsMap() {
    return array(
      'autocomplete' => Main::moduleNamespace('autocomplete') . 'Widget'
    );
  }
  
  public static function autocompleteUsers(array $recordsets) {
    $arr = array('data' => array());
    foreach ($recordsets as $recordset) {
      if ($recordset instanceof \system\model\RecordsetInterface) {
        $arr['data'][] = array(
          'id' => $recordset->id,
          'full_name' => $recordset->full_name
        );
      }
    }
    return \json_encode($arr);
  }
  
  public static function autocompleteNodes(array $recordsets) {
    $arr = array('data' => array());
    foreach ($recordsets as $recordset) {
      if ($recordset instanceof \system\model\RecordsetInterface) {
        $arr['data'][] = array(
          'id' => $recordset->id,
          'title' => $recordset->title,
          'data' => 
            '<div><img src="' . $recordset->image->url . '"'
            . ' alt="' . \cb\plaintext($recordset->title) . '"/>'
            . $recordset->title
            . '</div>'
        );
      }
    }
    return \json_encode($arr);
  }
}
?>