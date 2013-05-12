<?php
namespace module\core\components;

use \system\logic\Component;
use \system\model\Recordset;
use \system\model\RecordsetBuilder;
use \system\model\FilterClause;
use \system\model\FilterClauseGroup;
use \system\model\LimitClause;
use \system\model\SortClause;
use \system\model\SortClauseGroup;

// Inherits onInit method
class User extends Page {
	public static function accessRED($userId, $loggedUser) {
		return $loggedUser->superuser || $loggedUser->id == $userId;
	}
	
	public static function accessAdd($urlArgs, $request, $user) {
		return $user->superuser;
	}
	
	public static function accessRead($urlArgs, $request, $user) {
		return self::accessRED($urlArgs[0], $user);
	}
	
	public static function accessEdit($urlArgs, $request, $user) {
		return self::accessRED($urlArgs[0], $user);
	}
	
	public static function accessDelete($urlArgs, $request, $user) {
		return self::accessRED($urlArgs[0], $user);
	}
	
	public static function accessLogin($urlArgs, $request, $user) {
		return $user->anonymous;
	}
	
	public static function accessLogout($urlArgs, $request, $user) {
		return !$user->anonymous;
	}
	
	public static function accessRegister($urlArgs, $request, $user) {
		return $user->anonymous;
	}
	
	public static function accessAddRole($urlArgs, $request, $user) {
		return $user->superuser;
	}
	
	public static function accessDeleteRole($urlArgs, $request, $user) {
		return $user->superuser;
	}
	
	public static function accessList($urlArgs, $request, $user) {
		return $user->superuser;
	}
	
	public function runLogin() {
		$this->setMainTemplate('login-form');

		$this->setPageTitle(\system\Lang::translate("Login"));
		
		$user = \system\Login::getLoggedUser();
		
		if ($user->anonymous && \array_key_exists("login_form", $_REQUEST)) {
			try {
				$user = \system\Login::login();
			} catch (\system\LoginException $ex) {
				$this->datamodel['message'] = $ex->getMessage();
			}
		}
		
		if (!$user->anonymous) {
			$this->setMainTemplate('notify');
			$this->datamodel['message'] = array(
				'title' => \system\Lang::translate('Logged in'),
				'body' => \system\Lang::translate('<p>Welcome @name!</p>', array('@name' => $user->full_name))
			);
			return \system\logic\Component::RESPONSE_TYPE_NOTIFY;	
		}
		else {
			return \system\logic\Component::RESPONSE_TYPE_FORM;
		}
	}
	
	public function runLogout() {
		\system\Login::logout();
		
		$this->setMainTemplate('notify');
		$this->datamodel["message"] = array(
			'title' => \system\Lang::translate('Logged out'),
			'body' => \system\Lang::translate('You have been logged out.')
		);
		return Component::RESPONSE_TYPE_NOTIFY;
	}
	
	public function runRegister() {
		$this->setPageTitle(\cb\t('Under development'));
		$this->setMainTemplate('developing');
		return \system\logic\Component::RESPONSE_TYPE_READ;
	}
	
	public function runList() {
		$this->setPageTitle(\cb\t('Under development'));
		$this->setMainTemplate('developing');
		return \system\logic\Component::RESPONSE_TYPE_READ;
	}
	
	public function runAdd() {
		$this->setPageTitle(\cb\t('Under development'));
		$this->setMainTemplate('developing');
		return \system\logic\Component::RESPONSE_TYPE_READ;
	}
	
	public function runRead() {
		$this->setPageTitle(\cb\t('Under development'));
		$this->setMainTemplate('developing');
		return \system\logic\Component::RESPONSE_TYPE_READ;
	}
	
	public function runEdit() {
		$this->setPageTitle(\cb\t('Under development'));
		$this->setMainTemplate('developing');
		return \system\logic\Component::RESPONSE_TYPE_READ;
	}
	
	public function runDelete() {
		$this->setPageTitle(\cb\t('Under development'));
		$this->setMainTemplate('developing');
		return \system\logic\Component::RESPONSE_TYPE_READ;
	}
}
?>