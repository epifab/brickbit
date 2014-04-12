<?php
namespace module\user;

use system\exceptions\UnderDevelopment;
use system\exceptions\ValidationError;
use system\model2\Table;
use system\utils\Lang;
use system\utils\Login;
use module\crud\CrudController;

class UserCrudController extends CrudController {
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
  
  private function getUserTable() {
    $table = Table::loadTable('user');
    $table->import('*');
    return $table;
  }
  
  public function runList() {
    $table = $this->getUserTable();
    $users = $table->select();
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
    $table = $this->getUserTable();
    return $this->read($table->selectFirst($table->filter('id', $this->getUrlArg(0))));
  }
  
  public function runDelete() {
    throw new UnderDevelopment();
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
    $table = $this->getUserTable();
    switch ($this->getAction()) {
      case 'Add':
      case 'Register':
        $user = $table->newRecordset();
        $this->setPageTitle(Lang::translate('Add user'));
        break;

      case 'Edit':
        $user = $table->selectFirst($table->filter('id', $this->getUrlArg(0)));
        $this->setPageTitle(Lang::translate('Edit @name', array('@name' => $user->full_name)));
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
    throw new UnderDevelopment();
    $this->getForm()->getRecordset('user')->save();
  }
  
  public function submitAdd() {
    $form = $this->getForm();
    
    $user = $form->getRecordset('user');
    
    $pw1 = $form->getInputValue('password');
    $pw2 = $form->getInputValue('password2');
    
    $user->password = \md5($user->password);
      
    $user->ins_date_time = \time();
    $user->last_upd_date_time = \time();
    $user->save();
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
        throw new ValidationError('Invalid password');
      }
    }
    
    $user->last_upd_date_time = \time();
    $user->save();
    
    if ($user->id == Login::getLoggedUserId()) {
      // In case the user has changed email or password we refresh the login 
      //  with the new data
      Login::forceLogin($user);
    }
    
    // Displays the user profile page
    return $this->read($user);
  }
  ///</editor-fold>
}
