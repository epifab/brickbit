<?php
namespace module\tinymce;

use system\Component;
use system\Main;

class TinyMCEModule {
	public static function onRun(Component $component) {
		$component->addJs(Main::modulePathRel('tinymce', '4.0.18/tinymce.min.js'));
		$component->addJs(Main::modulePathRel('tinymce', 'main.js'));

    $defaultProfile = array(
      'theme' => 'modern',
      'skin' => 'light',
      'content_css' => Main::themePathRel('css/tinymce-bootstrap.css'),
      'selector' => 'textarea.wysiwyg',
      'plugins' => array(
        'advlist autolink lists link image charmap print preview anchor',
        'searchreplace visualblocks code fullscreen',
        'insertdatetime media table contextmenu paste'
      ),
      'toolbar' => 'insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image',
      'menubar' => '',
      'convert_urls' => false
    );

    $profiles = array();

    foreach (Main::moduleImplements('tinymceProfile') as $module) {
      foreach (\call_user_func($module) as $profileName => $profile) {
        $profiles[$profileName] = $profile + $defaultProfile;
      }
    }

    foreach ($profiles as $profileName => $profile) {
      foreach (Main::moduleImplements('tinymceProfileAlter') as $module) {
        // Allows profile alteration
        \call_user_func_array($module, array($profileName, &$profiles[$profileName]));
      }
    }

    $component->addJsData('tinymce', $profiles);
	}
}
