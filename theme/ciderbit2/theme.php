<?php
namespace theme\ciderbit2;

class Theme {
  public static function preRun(\system\Component $component) {
    // jquery library
    $component->addJs('http://ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js');
    // jquery ajax form submission plugin
    $component->addJs(\system\Theme::getPath('js/jquery.form.js'));
    // ciderbit object
    $component->addJs(\system\Theme::getPath('js/jquery.ciderbit.js'));
  }
  
  public static function onRun(\system\Component $component) {
    // bootstrap
    $component->addJs(\system\Theme::getPath('bootstrap/js/bootstrap.min.js'));
    $component->addCss(\system\Theme::getPath('bootstrap/css/bootstrap.min.css'));
    // $component->addCss(\system\Theme::getPath() . 'bootstrap/css/bootstrap-theme.min.css');

    // jquery ui
    $component->addJs(\system\Theme::getPath('js/jquery-ui-1.10.4/js/jquery-ui-1.10.4.custom.min.js'));
    $component->addCss(\system\Theme::getPath('js/jquery-ui-1.10.4/css/flick/jquery-ui-1.10.4.custom.css'));
    
    // jquery file upload
    $component->addCss(\system\Theme::getPath('js/jquery-file-upload-9.5.6/css/jquery.fileupload.css'));
    $component->addCss(\system\Theme::getPath('js/jquery-file-upload-9.5.6/css/jquery.fileupload-ui.css'));
    
    // custom
    $component->addJs(\system\Theme::getPath('js/ciderbit.js'));
    $component->addCss(\system\Theme::getPath('css/theme.css'));
  }
}