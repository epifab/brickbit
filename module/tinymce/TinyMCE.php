<?php
namespace module\tinymce;

class TinyMCE extends \system\Module {
	public static function onRun(\system\Component $component) {
		$component->addJs(\system\Module::getAbsPath('tinymce', '4.0.18/tinymce.min.js'));
		$component->addJs(\system\Module::getAbsPath('tinymce', 'main.js'));
	}
}
