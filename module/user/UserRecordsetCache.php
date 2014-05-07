<?php
namespace module\user;

use module\crud\RecordsetCache;
use system\model2\RecordsetInterface;

class UserRecordsetCache extends RecordsetCache {
  private static $instance = null;
  
  /**
   * @return NodeRecordsetCache Cache class
   */
  public static function getInstance() {
    if (empty(self::$instance)) {
      self::$instance = new self();
    }
    return self::$instance;
  }
  
  private function __construct() { }
  
  /**
   * Load a node recordset by id
   * @param int $id Id
   * @param bool $reset True to get a fresh object
   * @return RecordsetInterface Node recordset
   */
  public function loadById($id, $reset = false) {
    return parent::cachedRecordsetById('user', $id, $reset);
  }
  
  /**
   * Load a node recordset by email
   * @param string $email Email
   * @param bool $reset True to get a fresh object
   * @return RecordsetInterface Node recordset
   */
  public function loadByEmail($email, $reset = false) {
    return parent::cachedRecordset('user', array('email' => $email), $reset);
  }
}