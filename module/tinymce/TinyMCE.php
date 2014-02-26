<?php
namespace module\tinymce;

class TinyMCE extends \system\Module {
	public static function onRun(\system\Component $component) {
		$component->addJs(\system\Module::getAbsPath('tinymce', '3.5.4.1/jscripts/tiny_mce/jquery.tinymce.js'));
		$component->addJs(\system\Module::getAbsPath('tinymce', 'main.js'));
	}

}
