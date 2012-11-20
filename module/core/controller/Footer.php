<?php
namespace module\core\controller;

use system\logic\Component;
use system\logic\EditComponent;
use system\InternalErrorException;
use system\Login;

/**
 * Component Footer.
 * XMCA PHP Generator 0.1 - Auto generated code
 * @author episoft
 */
class Footer extends Component {
	public static function checkPermission($args) {
		// Accesso sempre consentito
		return true;
	}
	
	protected function getName() {
		return "Footer";
	}
	
	protected function getTemplate() {
		return "layout/Footer";
	}
	
	public function onProcess() {
		$this->datamodel["url"] = \array_key_exists("url", $this->request) ? $this->request["url"] : '';
		
		$this->datamodel["menuItems"] = Page::getPages();
	
		return Component::RESPONSE_TYPE_READ;
	}
}
?>