<?php
namespace module\admin;

use system\Main;

class AdminModule {
  /**
   * Implements controller event watchdog()
   */
  public static function watchdog($code, $message, $args, $level) {
    static $levelIndexes = array();
    if (!isset($levelIndexes[$level])) {
      $levelIndexes[$level] = 0;
    }

    $class = '';
    switch ($level) {
      case \system\LOG_NOTICE:
        $class = 'success';
        break;
      case \system\LOG_WARNING:
        $class = 'warning';
        break;
      case \system\LOG_ERROR:
        $class = 'danger';
        break;
      default:
        $class = 'info';
        break;
    }
    
//    Log::create($code, $message, $args, $level);
    
    \error_log(\cb\t($message, $args));
    
    Main::pushMessage(\cb\t($message, $args), $class);
    
    $levelIndexes[$level]++;
  }
}