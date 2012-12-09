<?php
namespace module\core\controller;

use system\logic\Component;
use system\logic\EditComponent;
use system\InternalErrorException;
use system\Login as LoginLib;

/**
 * Component Login.
 * XMCA PHP Generator 0.1 - Auto generated code
 * @author episoft
 */
class Login extends EditComponent {
	public static function checkPermission($args) {
		// Accesso sempre consentito
		return true;
	}
	
	protected function getName() {
		return "Login";
	}
	
	protected function getTemplateForm() {
		return "Login";
	}
	
	protected function getTemplateNotify() {
		return "layout/Success";
	}
	
	public function onProcess() {
		$login = LoginLib::getInstance();

		$this->setPageTitle("Login");

		if ($login->isAnonymous()) {
			if (\array_key_exists("login_form", $_REQUEST)) {
				try {
					$login = LoginLib::getPostedLogin();
				} catch (\system\LoginException $ex) {
					$this->datamodel["errorMessage"] = $ex->getMessage();
					return \system\logic\Component::RESPONSE_TYPE_FORM;
				}
			} else {
				return \system\logic\Component::RESPONSE_TYPE_FORM;
			}
		} else if (\array_key_exists("logout", $_REQUEST)) {
			$login->logout();
			$this->datamodel["successTitle"] = "Logout effettuato";
			return \system\logic\Component::RESPONSE_TYPE_NOTIFY;
		}
		
		$this->datamodel["successTitle"] = "Login effettuato";
		$this->datamodel["successMessage"] = "<p>Benvenuto " . $login->getUser()->getRead("full_name") . "!</p>";
		return \system\logic\Component::RESPONSE_TYPE_NOTIFY;
	}
}
?>