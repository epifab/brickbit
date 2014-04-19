<?php
namespace module\node_file;

use module\crud\RecordsetCache;
use system\model2\RecordsetInterface;

class NodeFileRecordsetCache extends RecordsetCache {
  private static $instance = null;
  
  /**
   * @return self Cache class
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
    return parent::cachedRecordsetById('node_file', $id, $reset);
  }
  
  /**
   * Load a node recordset by urn
   * @param int $nodeId Node id
   * @param string $nodeIndex Node index
   * @param string $virtualName Virtual name
   * @param bool $reset True to get a fresh object
   * @return RecordsetInterface Node recordset
   */
  public function loadByUrlInfo($nodeId, $nodeIndex, $virtualName, $reset = false) {
    return parent::cachedRecordset('node_file', array(
      'node_id' => $nodeId,
      'node_index' => $nodeIndex,
      'virtual_name' => $virtualName
    ), $reset);
  }
}