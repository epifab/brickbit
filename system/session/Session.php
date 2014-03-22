<?php
namespace system\session;

use system\model2\Table;
use system\model2\RecordsetInterface;
use system\utils\Login;

\session_start();
//\session_destroy();

class Session {
  private static $instance;
  
  private $data;
    
  /**
   * @var RecordsetInterface Session object
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
    
    $table = Table::loadTable('session');
    $table->import('*');
    
    $userId = \system\utils\Login::getLoggedUserId();
    $sessionId = \session_id();
    
    $this->session = $table->selectFirst($table->filterGroup('AND')->addClauses(
      $table->filter('user_id', $userId),
      $table->filter('session_id', $sessionId)
    ));
    
    if (!$this->session) {
      $this->session = $table->newRecordset();
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
    $this->session->save();
    \system\Main::invokeMethodAll('sessionCommit');
  }
  
  /**
   * Get a session variable
   * @param string $type Key 1
   * @param string $key Key 2
   * @param mixed $default Default value
   * @return null
   */
  public function get($type, $key, $default = null) {
    return (isset($this->data[$type]) && isset($this->data[$type][$key]))
      ? $this->data[$type][$key]
      : $default;
  }
  
  /**
   * Set a session variable
   * @param string $type Key 1
   * @param string $key Key 2
   * @param mixed $data Data
   */
  public function set($type, $key, $data) {
    if (!isset($this->data[$type])) {
      $this->data[$type] = array();
    }
    $this->data[$type][$key] = $data;
  }
  
  /**
   * Remove a session variable
   * @param string $type Key 1
   * @param string $key Key 2
   */
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
      $table = Table::loadTable('session');
      $table->import('user.*');
      $table->addFilters(
        $table->filter('update_time', \strtotime('-10 minutes', \time()), '>'),
        $table->filter('user_id', 0, '<>')
      );
      $sessions = $table->select();
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
