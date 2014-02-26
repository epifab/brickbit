<?php
namespace module\core\controller;

use \system\Component;
use \system\model\Recordset;
use \system\model\RecordsetBuilder;
use \system\model\FilterClause;
use \system\model\FilterClauseGroup;
use \system\model\LimitClause;
use \system\model\SortClause;
use \system\model\SortClauseGroup;

// Inherits onInit method
class User extends Page {
  private static function accessRED($action, $userId, $loggedUser) {
    return $loggedUser->superuser || $loggedUser->id == $userId;
  }
  
  public static function accessAdd($urlArgs, $user) {
    return $user->superuser;
  }
  
  public static function accessRead($urlArgs, $user) {
    return self::accessRED('read', $urlArgs[0], $user);
  }
  
  public static function accessEdit($urlArgs, $user) {
    return self::accessRED('edit', $urlArgs[0], $user);
  }
  
  public static function accessDelete($urlArgs, $user) {
    return self::accessRED('delete', $urlArgs[0], $user);
  }
  
  public static function accessLogin($urlArgs, $user) {
    return $user->anonymous;
  }
  
  public static function accessLogout($urlArgs, $user) {
    return !$user->anonymous;
  }
  
  public static function accessRegister($urlArgs, $user) {
    return $user->anonymous;
  }
  
  public static function accessAddRole($urlArgs, $user) {
    return $user->superuser;
  }
  
  public static function accessDeleteRole($urlArgs, $user) {
    return $user->superuser;
  }
  
  public static function accessList($urlArgs, $user) {
    return $user->superuser;
  }
  
  public function runLogin() {
    $this->setMainTemplate('login-form');
    $this->datamodel['page']['bodyClass'] = 'signin';

    $this->setPageTitle(\system\utils\Lang::translate("Login"));
    
    $user = \system\utils\Login::getLoggedUser();
    
    if ($user->anonymous && !empty($_REQUEST['login'])) {
      try {
        $user = \system\utils\Login::login($_REQUEST['login']);
      }
      catch (\system\exceptions\LoginError $ex) {
        $this->addMessage($ex->getMessage(), 'warning');
      }
    }
    
    if (!$user->anonymous) {
      $this->setMainTemplate('notify');
      $this->datamodel['message'] = array(
        'title' => \system\utils\Lang::translate('Logged in'),
        'body' => \system\utils\Lang::translate('<p>Welcome @name!</p>', array('@name' => $user->full_name))
      );
      return \system\Component::RESPONSE_TYPE_NOTIFY;  
    }
    else {
      return \system\Component::RESPONSE_TYPE_FORM;
    }
  }
  
  public function runLogout() {
    \system\utils\Login::logout();
    
    $this->setMainTemplate('notify');
    $this->datamodel['message'] = array(
      'title' => \system\utils\Lang::translate('Logged out'),
      'body' => \system\utils\Lang::translate('You have been logged out.')
    );
    return Component::RESPONSE_TYPE_NOTIFY;
  }
  
  public function runRegister() {
    $this->setPageTitle(\cb\t('Under development'));
    $this->setMainTemplate('501');
    return \system\Component::RESPONSE_TYPE_READ;
  }
  
  public function runList() {
    $this->setPageTitle(\cb\t('Under development'));
    $this->setMainTemplate('501');
    return \system\Component::RESPONSE_TYPE_READ;
  }
  
  public function runAdd() {
    $this->setPageTitle(\cb\t('Under development'));
    $this->setMainTemplate('501');
    return \system\Component::RESPONSE_TYPE_READ;
  }
  
  public function runRead() {
    throw new \system\exceptions\InternalError('Test');
    $this->setPageTitle(\cb\t('Under development'));
    $this->setMainTemplate('501');
    return \system\Component::RESPONSE_TYPE_READ;
  }
  
  public function runEdit() {
    $this->setPageTitle(\cb\t('Under development'));
    $this->setMainTemplate('501');
    return \system\Component::RESPONSE_TYPE_READ;
  }
  
  public function runDelete() {
    $this->setPageTitle(\cb\t('Under development'));
    $this->setMainTemplate('501');
    return \system\Component::RESPONSE_TYPE_READ;
  }
}
