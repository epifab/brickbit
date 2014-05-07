<?php
namespace module\user;

use system\Main;
use system\model2\RecordsetInterface;

class UserTableApi {
  /**
   * User edit URL
   * @param \system\model2\RecordsetInterface $recordset User recordset
   * @return string Edit URL
   */
  public static function getUrl(RecordsetInterface $recordset) {
    return Main::getPathVirtual("user/{$recordset->id}");
  }
  
  /**
   * User edit URL
   * @param \system\model2\RecordsetInterface $recordset User recordset
   * @return string Edit URL
   */
  public static function getEditUrl(RecordsetInterface $recordset) {
    return Main::getPathVirtual("user/{$recordset->id}/edit");
  }
  
  /**
   * User delete URL
   * @param \system\model2\RecordsetInterface $recordset User recordset
   * @return string Delete URL
   */
  public static function getDeleteUrl(RecordsetInterface $recordset) {
    return Main::getPathVirtual("user/{$recordset->id}/delete");
  }
  
  /**
   * User permissions
   * @param RecordsetInterface $user User recordset
   * @return array Permissions
   */
  public static function getPermissions(RecordsetInterface $user) {
    if ($user->getExtra('permissions', false) === false) {
      $permissions = array();
      foreach ($user->roles as $userRole) {
        foreach ($userRole->role->permissions as $rolePermission) {
          $permissions[$rolePermission->permission->id] = $rolePermission->permission;
        }
      }
      $user->setExtra('permissions', $permissions);
    }
    return $user->getExtra('permissions');
  }
}
