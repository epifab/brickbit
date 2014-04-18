<?php
namespace module\node;

use module\crud\RecordsetCache;
use system\model2\RecordsetInterface;

class NodeRecordsetCache extends RecordsetCache {
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
    return parent::cachedRecordsetById('node', $id, $reset);
  }
  
  /**
   * Load a node recordset by urn
   * @param string Urn
   * @param bool $reset True to get a fresh object
   * @return RecordsetInterface Node recordset
   */
  public function loadByUrn($urn, $reset = false) {
    return parent::cachedRecordset('node', array('text.urn' => $urn), $reset);
  }
}