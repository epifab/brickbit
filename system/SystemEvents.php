<?php
namespace system;

class SystemEvents {
  
  /**
   * Raises the whatchdog event.
   * This is used to log messages.
   * @param string $code Typically the module where the event happened
   * @param string $message Watchdog message
   * @param array $messageArgs Message arguments
   * @param int $level Log level
   */
  public static function watchdog($code, $message, array $messageArgs, $level = system\LOG_NOTICE) {
    Main::invokeMethodAll('watchdog', $code, $message, $messageArgs, $level);
  }

  /**
   * Raises the onRun event.
   * This is fired before the main component is processed.
   * Please note also that <em>preRun</em> and <em>onRun</em> methods are also 
   *  fired on the active theme respectively before and after this event.
   * @param Component $component
   */
  public static function onRun(Component $component) {
    Main::invokeMethodAll('onRun', $component);
  }
  
  /**
   * Raises the dateFormat event.
   * @return array Array of date formats
   */
  public static function dateFormat() {
    return Main::invokeStaticMethodAllMerge('dateFormat', false);
  }
  
  /**
   * Raises the dateTimeFormat event.
   * @return array Array of date time formats
   */
  public static function dateTimeFormat() {
    return Main::invokeStaticMethodAllMerge('dateTimeFormat', false);
  }
  
  /**
   * Raises the timeFormat event.
   * @return array Array of time formats
   */
  public static function timeFormat() {
    return Main::invokeStaticMethodAllMerge('timeFormat', false);
  }
}