<?php
namespace system\session;

use system\model\RecordsetBuilder;
use system\utils\Login;

#\session_destroy();
\session_start();

class Session {
  private static $instance;
  
  private $data;
  
  /**
   * @var \system\model\Recordset Session object
   */
  private $session;
  
  /**
   * @return \system\session\Session Session
   */
  public static function getInstance() {
    if (empty(self::$instance)) {
      self::$instance = new self();
    }
    return self::$instance;
  }
  
  private function __construct() {
    
    $rsb = new RecordsetBuilder('session');
    $rsb->using('*');
    
    $userId = \system\utils\Login::getLoggedUserId();
    $sessionId = \session_id();
    
    $this->session = $rsb->selectFirstBy(array(
      'user_id' => $userId,
      'session_id' => $sessionId
    ));
    
    if (!$this->session) {
      $this->session = $rsb->newRecordset();
      $this->session->user_id = $userId;
      $this->session->session_id = $sessionId;
      $this->session->create_time = \time();
      $this->data = array();
    }
    else {
      $this->session->update_time = \time();
      $this->data = \unserialize($this->session->data);
    }
    $this->session->expire_time = \strtotime(($userId ? '+3 months' : '+2 days'), \time());
  }
  
  public function __get($name) {
    return $this->session->{$name};
  }
  
  public function commit() {
    $this->session->data = \serialize($this->data);
    echo '<code>' . $this->session->data . '</code>';
    $this->session->save();
  }
  
  public function get($type, $key) {
    if (isset($this->data[$type]) && isset($this->data[$type][$key])) {
      return $this->data[$type][$key];
    }
    return null;
  }
  
  public function set($type, $key, $data) {
    \system\utils\Log::create(__FUNCTION__, \system\utils\Utils::backtraceInfo());
    if (!$this->data[$type]) {
      $this->data[$type] = array();
    }
    $this->data[$type][$key] = $data;
  }
  
  public function remove($type, $key) {
    if (isset($this->data[$type]) && isset($this->data[$type][$key])) {
      unset($this->data[$type][$key]);
    }
  }
  
  public function exists($type, $key) {
    return $this->get($type, $key) != null;
  }
  
  public static function usersOnline() {
    static $users = null;
    
    if (\is_null($users)) {
      $users = array();
      $rsb = new RecordsetBuilder('session');
      $rsb->using('user.*');
      $rsb->setFilter(new \system\model\FilterClauseGroup(
        new \system\model\FilterClause($rsb->update_time, '>', \strtotime('-10 minutes', \time())),
        'AND',
        new \system\model\FilterClause($rsb->user_id, '<>', 0)
      ));
      $sessions = $rsb->select();
      foreach ($sessions as $session) {
        $users[$session->user->id] = $session->user;
      }
    }
    return $users;
  }
  
  public static function deleteExpired() {
    throw new \Exception('Not implemented yet');
  }
}
