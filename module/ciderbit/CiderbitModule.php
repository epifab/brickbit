<?php
namespace module\ciderbit;

class CiderbitModule {
  
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