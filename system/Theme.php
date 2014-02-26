<?php
namespace system;

class Theme {
  private static $theme;
  
//  /**
//   * @var \system\ThemeInterface Theme object
//   */
//  private static $themeObject;
//  
//  /**
//   * @return \system\ThemeInterface Theme object (may return null if the class misses)
//   */
//  private static function getThemeObject() {
//    if (empty(self::$themeObject)) {
//      $className = '\\theme\\' . self::getTheme() . '\\Theme';
//      if (\class_exists($className) && \in_array('\\system\\ThemeInterface', \class_implements($className))) {
//        self::$themeObject = new $className();
//      }
//    }
//    return self::$themeObject;
//  }
  
  public static function getTheme() {
    if (\is_null(self::$theme)) {
      self::setTheme(\config\settings()->DEFAULT_THEME);
    }
    return self::$theme;
  }
  
  public static function setTheme($theme) {
    if (\in_array($theme, \config\settings()->THEMES)) {
      self::$theme = $theme;
    } else {
      throw new \system\exceptions\InternalError('Theme <em>@name</em> not found.', array('@name' => $theme));
    }
  }
  
  public static function getPath($path = '') {
    return \config\settings()->BASE_DIR . 'theme/' . self::getTheme() . '/' . $path;
  }
  
  public static function getAbsPath($path = '') {
    return \config\settings()->BASE_DIR_ABS . 'theme/' . self::getTheme() . '/' . $path;
  }
  
  public static function preRun(\system\Component $compponent) {
    self::themeEvent('preRun', $compponent);
  }
  
  public static function onRun(\system\Component $component) {
    self::themeEvent('onRun', $component);
  }
  
  /**
   * Raise a theme event.
   * Basically, it searches a method called $event on the current theme class
   *  running it (if found)
   * @param string $event Event
   */
  private static function themeEvent($event) {
    $args = \func_get_args();
    array_shift($args);
    $cname = '\theme\\' . self::getTheme() . '\\Theme';
    if (\class_exists($cname) && \is_callable($cname . '::' . $event)) {
      \call_user_func_array($cname . '::' . $event, $args);
    }
  }
}
