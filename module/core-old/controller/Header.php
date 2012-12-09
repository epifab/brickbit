<?php
namespace module\core\controller;

use system\logic\Component;
use system\logic\EditComponent;
use system\InternalErrorException;
use system\Login;

/**
 * Component Header.
 * XMCA PHP Generator 0.1 - Auto generated code
 * @author episoft
 */
class Header extends \system\logic\Component {
	public static function checkPermission($args) {
		// Accesso sempre consentito
		return true;
	}
	
	protected function getName() {
		return "Header";
	}
	
	protected function getTemplate() {
		return "layout/Header";
	}
	
	public function onProcess() {
		$this->datamodel["url"] = $this->request["url"];
		
		$this->datamodel["menuItems"] = Page::getPages();
	
		return \system\logic\Component::RESPONSE_TYPE_READ;
	}
}
?>