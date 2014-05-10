<?php
namespace theme\brickbit2;

use system\Main;
use system\Component;

class Theme {
  public static function preRun(Component $component) {
    // jquery library
    $component->addJs(Main::themePathRel('js/jquery-ui-1.10.4/js/jquery-1.10.2.js'));
    // jquery ajax form submission plugin
    $component->addJs(Main::themePathRel('js/jquery.form.js'));
    
    // brickbit object
    $component->addJs(Main::themePathRel('js/jquery.brickbit.js'));
    // bootstrap
    $component->addJs(Main::themePathRel('bootstrap/js/bootstrap.min.js'));
    $component->addCss(Main::themePathRel('bootstrap/css/bootstrap.min.css'));

    // jquery ui
    $component->addJs(Main::themePathRel('js/jquery-ui-1.10.4/js/jquery-ui-1.10.4.custom.min.js'));
    $component->addCss(Main::themePathRel('js/jquery-ui-1.10.4/css/flick/jquery-ui-1.10.4.custom.css'));
    
    // custom
    $component->addJs(Main::themePathRel('js/brickbit.js'));
    $component->addCss(Main::themePathRel('css/theme.css'));
  }
  
  public static function onRun(Component $component) {

  }
}