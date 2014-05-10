<?php
namespace module\brickbit;

class BrickbitModule {
  
  /**
   * Implements controller event preprocessTemplate()
   */
  public static function preprocessTemplate() {
    
  }

  /**
   * Implements controller event onRun()
   */
  public static function onRun(\system\Component $component) {
    $component->addTemplate('website-logo', 'header');
    $component->addTemplate('footer', 'footer');
    //$component->addTemplate('sidebar', 'sidebar');
  }
}