<?php
namespace system\utils;

class Log {
  private static $debugInfo = array();
  
  /**
   * Create a log
   * @param string $code
   * @param string $body
   * @param array $args
   * @param int $level
   * @return int Log id
   */
  public static function create($code, $body, $args = array(), $level = \system\LOG_WARNING) {
    $builder = new \system\model\RecordsetBuilder('log');
    $builder->using("*");
    $rs = $builder->newRecordset();
    
    $rs->url = $_SERVER['REQUEST_URI'];
    $rs->code = $code;
    $rs->body = \cb\t($body, $args);
    $rs->level = $level;
    $rs->trace = \system\utils\Utils::backtraceInfo(\array_slice(\debug_backtrace(), 0, -1));
    $rs->date_time_request = \time();
    $rs->user_id = \system\utils\Login::getLoggedUserId();
    $rs->ip_address = \system\utils\HTMLHelpers::getIpAddress();
    
    $rs->create();
    return $rs->id;
  }
  
  /**
   * Add debug info
   * @param string $body Message
   * @param array $args Message arguments
   */
  public static function debug($body, $args = array()) {
    self::$debugInfo[] = \cb\t($body, $args);
  }
  
  /**
   * Get debug info as a string
   * @return string Debug info
   */
  public static function getDebug() {
    return \implode("\n<br/>", self::$debugInfo);
  }
}
