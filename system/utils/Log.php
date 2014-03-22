<?php
namespace system\utils;

use system\model2\Table;

class Log {
  private static $debugInfo = array();
  
  /**
   * Create a log
   * @param string $code Typically the name of the module which has raised the
   *  log. Can be any string.
   * @param string $body Message body
   * @param array $args Message arguments
   * @param int $level Log level. Please use one of 
   *  LOG_DEBUG, LOG_INFO, LOG_WARNING, LOG_ERROR
   * @return int Log id
   */
  public static function create($code, $body, array $args = array(), $level = \system\LOG_WARNING) {
    $table = Table::loadTable('log');
    $table->import('*');
    $rs = $table->newRecordset();
    
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
    self::$debugInfo[] = array(
      'message' => $body,
      'args' => $args,
    );
  }
  
  /**
   * Get debug info as a string
   * @return string Debug info
   */
  public static function getDebug() {
    return self::$debugInfo;
  }
}
