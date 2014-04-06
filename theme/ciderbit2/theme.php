<?php
namespace theme\ciderbit2;

use system\Main;

class Theme {
  public static function preRun(\system\Component $component) {
    // jquery library
    $component->addJs('http://ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js');
    // jquery ajax form submission plugin
    $component->addJs(Main::themePathRel('js/jquery.form.js'));
    // ciderbit object
    $component->addJs(Main::themePathRel('js/jquery.ciderbit.js'));
  }
  
  public static function onRun(\system\Component $component) {
    // bootstrap
    $component->addJs(Main::themePathRel('bootstrap/js/bootstrap.min.js'));
    $component->addCss(Main::themePathRel('bootstrap/css/bootstrap.min.css'));
    // $component->addCss(Main::themePathRel() . 'bootstrap/css/bootstrap-theme.min.css');

    // jquery ui
    $component->addJs(Main::themePathRel('js/jquery-ui-1.10.4/js/jquery-ui-1.10.4.custom.min.js'));
    $component->addCss(Main::themePathRel('js/jquery-ui-1.10.4/css/flick/jquery-ui-1.10.4.custom.css'));
    
    // jquery file upload
    $component->addCss(Main::themePathRel('js/jquery-file-upload-9.5.6/css/jquery.fileupload.css'));
    $component->addCss(Main::themePathRel('js/jquery-file-upload-9.5.6/css/jquery.fileupload-ui.css'));
    
    // custom
    $component->addJs(Main::themePathRel('js/ciderbit.js'));
    $component->addCss(Main::themePathRel('css/theme.css'));
  }
}