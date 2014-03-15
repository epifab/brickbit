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

class User extends Edit {
  ///<editor-fold defaultstate="collapsed" desc="Access methods">
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
  ///</editor-fold>
  
  private function getUserBuilder() {
    $userBuilder = new \system\model\RecordsetBuilder('user');
    $userBuilder->using('*');
    return $userBuilder;
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
      return \system\RESPONSE_TYPE_NOTIFY;  
    }
    else {
      return \system\RESPONSE_TYPE_FORM;
    }
  }
  
  public function runLogout() {
    \system\utils\Login::logout();
    
    $this->setMainTemplate('notify');
    $this->datamodel['message'] = array(
      'title' => \system\utils\Lang::translate('Logged out'),
      'body' => \system\utils\Lang::translate('You have been logged out.')
    );
    return \system\RESPONSE_TYPE_NOTIFY;
  }
  
  public function runList() {
    $userBuilder = $this->getUserBuilder();
    $users = $userBuilder->select();
    $this->datamodel['users'] = $users;
    $this->setPageTitle(\cb\t('Users'));
    $this->setMainTemplate('users');
    return \system\RESPONSE_TYPE_READ;
  }
  
  private function read($user) {
    $this->setPageTitle($user->full_name);
    $this->datamodel['u'] = $user;
    $this->setMainTemplate('user');
    return \system\RESPONSE_TYPE_READ;
  }
  
  public function runRead() {
    return $this->read($this->getUserBuilder()->selectFirstBy(array('id' => $this->getUrlArg(0))));
  }
  
  public function runDelete() {
    throw new \system\exceptions\UnderDevelopment();
  }

  ///<editor-fold defaultstate="collapsed" desc="Editing stuff">
  /**
   * Edit actions (add, add to a node, edit.
   * @return array List of available actions
   */
  public function getEditActions() {
    return array('Add', 'Register', 'Edit');
  }
  
  protected function getEditRecordsets() {
    $user = null;
    switch ($this->getAction()) {
      case 'Add':
      case 'Register':
        $user = $this->getUserBuilder()->newRecordset();
        break;

      case 'Edit':
        $user = $this->getUserBuilder()->selectFirstBy(array('id' => $this->getUrlArg(0)));
        break;
    }
    $recordsets = array('user' => $user);

    return $recordsets;
  }

  protected function getFormId() {
    switch ($this->getAction()) {
      case 'Register':
        return 'user-register-form';
        break;
      case 'Add':
        return 'user-create-form';
        break;
      case 'Edit':
      default:
        return 'user-update-form';
        break;
    }
  }

  protected function getFormTemplate() {
    switch ($this->getAction()) {
      case 'Register':
        return 'user-register';
        break;
      default:
        return 'user-edit';
        break;
    }
  }
  
  public function submitRegister() {
    throw new \system\exceptions\UnderDevelopment();
    $this->getForm()->getRecordset('user')->save();
  }
  
  public function submitAdd() {
    throw new \system\exceptions\UnderDevelopment();
    $this->getForm()->getRecordset('user')->save();
  }
  
  public function submitEdit() {
    $form = $this->getForm();
    
    $user = $form->getRecordset('user');
    
    $pw1 = $form->getInputValue('password');
    $pw2 = $form->getInputValue('password2');
    
    if (!empty($pw1) || !empty($pw2)) {
      if (empty($pw1)) {
        // New password not sent
        $form->setValidationError('password', \cb\t('Please enter a valid password'));
      }
      else if (empty($pw2)) {
        // Confirmation not sent
        $form->setValidationError('password2', \cb\t('Please confirm the new password'));
      }
      elseif ($pw1 != $pw2) {
        // Passwords do not match
        $form->setValidationError('password2', \cb\t('Passwords do not match'));
      }
      else {
        $user->password = \md5($pw1);
      }
      
      if ($form->countValidationErrors()) {
        throw new \system\exceptions\ValidationError('Invalid password');
      }
    }
    
    $user->last_upd_date_time = \time();
    $user->save();
    
    if ($user->id == \system\utils\Login::getLoggedUserId()) {
      // In case the user has changed email or password we refresh the login 
      //  with the new data
      \system\utils\Login::forceLogin($user);
    }
    
    // Displays the user profile page
    return $this->read($user);
  }
  ///</editor-fold>
}
