<?php
namespace theme\ciderbit;

class Theme {
  public static function onRun(\system\Component $component) {
    $component->addJs(\system\Theme::getPath() . 'bootstrap/js/bootstrap.min.js');
    
    $component->addCss(\system\Theme::getPath() . 'bootstrap/css/bootstrap.min.css');
    $component->addCss(\system\Theme::getPath() . 'bootstrap/css/bootstrap-responsive.min.css');
    
    $component->addCss('http://ajax.googleapis.com/ajax/libs/jqueryui/1.9.0/themes/flick/jquery-ui.css');

    $component->addCss(\system\Theme::getPath() . 'css/layout.css');
  }
}