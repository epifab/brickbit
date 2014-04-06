<?php
namespace module\tinymce;

use system\Component;
use system\Main;

class TinyMCE {
	public static function onRun(Component $component) {
		$component->addJs(Main::modulePathRel('tinymce', '4.0.18/tinymce.min.js'));
		$component->addJs(Main::modulePathRel('tinymce', 'main.js'));
	}
}
