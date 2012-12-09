<?php
namespace module\core\components;

class Page extends \system\logic\Component {
	public function getTemplate() {
		return 'test';
	}
	
	public static function accessRead($urlArgs, $userId=null) {
		return true;
	}
	public static function accessHeader($urlArgs, $userId=null) {
		
	}
	public static function accessEdit($urlArgs, $userId=null) {
		$page = new RecordsetBuilder("page");
		$page->using("*", "contents.*", "contents.image.file1");
	}
}
?>