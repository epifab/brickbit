<?php
namespace system;

use system\Component;

class ThemeApi {
  /**
   * This event is fired just before the onRun controller event.
   * @param Component $component
   */
  public static function preRun(Component $component) {
    Main::raiseThemeEvent('preRun', $component);
  }
  
  /**
   * This event is fired just after the onRun controller event.
   * @param Component $component
   */
  public static function onRun(Component $component) {
    Main::raiseThemeEvent('onRun', $component);
  }
}
