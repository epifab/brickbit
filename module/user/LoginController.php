<?php
namespace module\user;

use system\Main;
use system\exceptions\LoginError;
use system\Component;
use system\utils\Lang;
use system\utils\Login;

class LoginController extends Component {
  public static function accessLogin($urlArgs, $user) {
    return $user->anonymous;
  }
  
  public static function accessLoginControl($urlArgs, $user) {
    return $user->anonymous;
  }
  
  public static function accessLogout($urlArgs, $user) {
    return !$user->anonymous;
  }
  
  public function runLogin() {
    $this->setMainTemplate('login-form');
    $this->datamodel['page']['bodyClass'] = 'signin';

    $this->setPageTitle(Lang::translate("Login"));
    
    $user = Login::getLoggedUser();
    
    if ($user->anonymous && !empty($_REQUEST['login'])) {
      try {
        $user = Login::login($_REQUEST['login']);
      }
      catch (LoginError $ex) {
        Main::pushMessage($ex->getMessage(), 'warning');
      }
    }
    
    if (!$user->anonymous) {
      $this->setMainTemplate('notify');
      $this->datamodel['message'] = array(
        'title' => Lang::translate('Logged in'),
        'body' => Lang::translate('<p>Welcome @name!</p>', array('@name' => $user->full_name))
      );
      return \system\RESPONSE_TYPE_NOTIFY;  
    }
    else {
      return \system\RESPONSE_TYPE_FORM;
    }
  }
  
  public function runLoginControl() {
    $this->setMainTemplate('login-control');
    return \system\RESPONSE_TYPE_READ;
  }
  
  public function runLogout() {
    Login::logout();
    
    $this->setMainTemplate('notify');
    $this->datamodel['message'] = array(
      'title' => Lang::translate('Logged out'),
      'body' => Lang::translate('You have been logged out.')
    );
    return \system\RESPONSE_TYPE_NOTIFY;
  }
}