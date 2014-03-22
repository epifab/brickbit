<?php
namespace module\core\model;

use \system\Main;
use \system\model2\RecordsetInterface;

class User {
  /**
   * User edit URL
   * @param \system\model2\RecordsetInterface $recordset User recordset
   * @return string Edit URL
   */
  public static function getUrl(RecordsetInterface $recordset) {
    return Main::getUrl("user/{$recordset->id}");
  }
  
  /**
   * User edit URL
   * @param \system\model2\RecordsetInterface $recordset User recordset
   * @return string Edit URL
   */
  public static function getEditUrl(RecordsetInterface $recordset) {
    return Main::getUrl("user/{$recordset->id}/edit");
  }
  
  /**
   * User delete URL
   * @param \system\model2\RecordsetInterface $recordset User recordset
   * @return string Delete URL
   */
  public static function getDeleteUrl(RecordsetInterface $recordset) {
    return Main::getUrl("user/{$recordset->id}/delete");
  }
}
