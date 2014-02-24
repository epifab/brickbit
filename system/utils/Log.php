<?php
namespace system\utils;

class Log {
  private static $debugInfo = array();
  
  private static $messages = null;
  
  /**
   * Get messages
   * @return array Messages
   */
  public static function getMessages() {
    self::loadMessages();
    return self::$messages;
  }
  
  /**
   * Consume a message (FIFO queue)
   * @return array Message (or NULL if the queue is empty)
   */
  public static function popMessage() {
    self::loadMessages();
    if (!empty(self::$messages)) {
      $first = \array_shift(self::$messages);
      self::updateMessages();
      return $first;
    }
    else {
      return null;
    }
  }
  
  /**
   * Push a message (FIFO queue)
   * @param array $message Message
   */
  public static function pushMessage($message) {
    self::loadMessages();
    self::$messages[] = $message;
    self::updateMessages();
  }
  
  /**
   * Load session messages
   */
  private static function loadMessages() {
    if (\is_null(self::$messages)) {
      self::$messages = \system\session\Session::getInstance()->get('log', 'messages');
      if (\is_null(self::$messages)) {
        self::$messages = array();
      }
    }
  }
  
  /**
   * Update session messages
   */
  private static function updateMessages() {
    \system\session\Session::getInstance()->set('log', 'messages', self::$messages);
  }
  
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
