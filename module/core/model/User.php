<?php
namespace module\core\model;

use \system\Main;

class User {
  public static function getUrl(\system\model\RecordsetInterface $recordset) {
    return Main::getUrl("user/{$recordset->id}");
  }
  
  public static function getEditUrl(\system\model\RecordsetInterface $recordset) {
    return Main::getUrl("user/{$recordset->id}/edit");
  }
  
  public static function getDeleteUrl(\system\model\RecordsetInterface $recordset) {
    return Main::getUrl("user/{$recordset->id}/delete");
  }
}
