<?php
namespace module\crud;

use system\Main;
use system\exceptions\InternalError;
use system\model2\RecordsetInterface;
use system\model2\Table;

class RecordsetCache {
  private $recordsets = array();
  private $recordsetIds = array();
  
  /**
   * Flush the cache
   */
  public function flush() {
    $this->recordsets = array();
    $this->recordsetIds = array();
  }
  
  /**
   * Gets a cached recordset.
   * This method deals only with tables with 1-field primary key.
   * @param string $tableName Table name
   * @param int $id Serial (primary key value)
   * @param bool $reset TRUE if needs to get a fresh recordset
   * @return RecordsetInterface Recordset
   * @throws InternalError
   */
  protected function cachedRecordsetById($tableName, $id, $reset = false) {
    if (!\array_key_exists($id, $this->recordsets) || $reset) {
      $table = Main::loadRecordsetTable($tableName);
      
      $pkeyFields = $table->getPrimaryKey()->getFields();
      if (count($pkeyFields) != 1) {
        throw new InternalError('Unable to get a cached recordset from table <em>@table</em>.', array(
          '@table' => $tableName
        ));
      }
      
      $this->recordsets[$id] = $table->selectFirst($table->filter(\reset($pkeyFields)->getName(), $id));
    }
    
    return $this->recordsets[$id];
  }
  
  /**
   * Retrieves the recordset id
   * @param string $tableName Table name
   * @param array $fields Associative array field name => value
   * @return mixed Id value or false if no record was found
   */
  protected function getRecordsetId($tableName, array $fields) {
    $hash = '';
    foreach ($fields as $k => $v) {
      $hash .= (empty($hash) ? '' : ',') . $k . ':"' . \addcslashes($v, '"\\') . '"';
    }
    
    if (!isset($this->recordsetIds[$tableName])) {
      $this->recordsetIds[$tableName] = array();
    }
    
    if (!isset($this->recordsetIds[$tableName][$hash])) {
      $table = Table::loadTable($tableName);
      
      $pkeyFields = $table->getPrimaryKey()->getFields();
      if (count($pkeyFields) != 1) {
        throw new InternalError('Unable to get a cached recordset from table <em>@table</em>.', array(
          '@table' => $tableName
        ));
      }
      
      foreach ($fields as $field => $value) {
        $table->import($field);
        $table->addFilters($table->filter($field, $value));
      }

      $this->recordsetIds[$tableName][$hash] = ($table->countRecords() == 1)
        ? $table->selectFirst()->{$pkeyFields[0]}
        : null;
    }
    
    return $this->recordsetIds[$tableName][$hash];
  }
  
  /**
   * Gets a cached recordset.
   * @param string $tableName Table name
   * @param array $fields Associative array field name => value
   * @param bool $reset TRUE if needs to get a fresh recordset
   * @return RecordsetInterface Recordset
   */
  protected static function cachedRecordset($tableName, $fields, $reset = false) {
    $id = self::getRecordsetId($tableName, $fields);
    return (!empty($id))
      ? self::cachedRecordsetById($tableName, $id, $reset)
      : null;
  }
}