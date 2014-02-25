<?php
namespace module\core\controller;

class EditUser extends Edit {
  public static function accessEdit($urlArgs, $request, $user) {
    return $user->superuser || $user->id == $urlArgs[0];
  }
  
  public static function accessDelete($urlArgs, $request, $user) {
    return $user->superuser || $user->id == $urlArgs[0];
  }
  
  public function runDelete() {
    $rsb = new \system\model\RecordsetBuilder('user');
    $rs = $rsb->selectFirstBy($this->getUrlArg(0));
    if (empty($rs)) {
      throw new \system\exceptions\PageNotFound();
    }
    $rs->delete();
  }
  
  public static function accessAdd($urlArgs, $request, $user) {
    return $user->superuser;
  }
  
  public static function accessRegister($urlArgs, $request, $user) {
    return true;
  }

  public function getEditActions() {
    return array('Register', 'Add', 'Update');
  }
  
  public function getEditRecordsets() {
    $rsb = new \system\model\RecordsetBuilder('user');
    $rsb->using('*');
    switch ($this->getAction()) {
      case 'Register':
      case 'Add':
        return $rsb->newRecordset();
        break;
      case 'Update':
        return $rsb->selectFirstBy($this->getUrlArg(1));
        break;
    }
  }
  
  public function getFormId() {
    switch ($this->getAction()) {
      case 'Register':
        return 'user-register-form';
        break;
      case 'Add':
        return 'user-add-form';
        break;
      case 'Update':
        return 'user-update-form';
        break;
    }
  }
  
  public function getFormTemplate() {
    switch ($this->getAction()) {
      case 'Register':
        return 'user-register';
        break;
      case 'Add':
        return 'user-add';
        break;
      case 'Update':
        return 'user-edit';
        break;
    }
  }
  
  public function getUserGroupsOnRegister() {
    return array();
  }
  
//  public function submitRegister($form, $rs) {
//    $rs->save();
//    $rsb = new \system\model\RecordsetBuilder('user_group');
//    $rsb->using('*');
//    foreach ($this->getUserGroupsOnRegister() as $groupId) {
//      $ug = $rsb->newRecordset();
//      $ug->user_id = $rs->id;
//      $ug->group_id = $groupId;
//      $ug->add();
//    }
//    $this->setMainTemplate('user-register-submit');
//    return \system\Component::RESPONSE_TYPE_NOTIFY;
//  }
//  
//  public function submitAdd($form, $rs) {
//    $rs->save();
//    $this->setMainTemplate('user-add-submit');
//    return \system\Component::RESPONSE_TYPE_NOTIFY;
//  }
//  
//  public function submitUpdate($form, $rs) {
//    $rs->save();
//    $this->setMainTemplate('user-update-submit');
//    return \system\Component::RESPONSE_TYPE_NOTIFY;
//  }
}
