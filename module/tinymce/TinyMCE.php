<?php
namespace module\tinymce;

class TinyMCE extends \system\logic\Module {
	public static function onRun(\system\logic\Component $component) {
		$component->addJs(\system\logic\Module::getAbsPath('tinymce', '3.5.4.1') . 'jscripts/tiny_mce/jquery.tinymce.js');
		$component->addJs(\system\logic\Module::getAbsPath('tinymce') . 'main.js');
	}

}
?>