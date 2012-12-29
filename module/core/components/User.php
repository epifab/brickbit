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
	public static function access($action, $urlArgs, $request, $userId) {
		
	}
	
	public static function accessLogin($urlArgs, $request, $userId) {
		return !$userId;
	}
	
	public static function accessLogout($urlArgs, $request, $userId) {
		return (bool)$userId;
	}
	
	public function runLogin() {
		$this->setMainTemplate('login-form');

		$this->setPageTitle(\system\Lang::translate("Login"));
		
		$user = \system\Login::getLoggedUser();
		
		if (!$user && \array_key_exists("login_form", $_REQUEST)) {
			try {
				$user = \system\Login::login();
			} catch (\system\LoginException $ex) {
				$this->datamodel["errorMessage"] = $ex->getMessage();
			}
		}
		
		if ($user) {
			$this->setMainTemplate('notify');
			$this->datamodel["message"] = array(
				'title' => \system\Lang::translate('Logged in'),
				'body' => \system\Lang::translate('<p>Welcome @name!</p>', array('@name' => $user->getRead('full_name')))
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
}
?>