<?php
namespace system\model2;

use system\Main;
use system\exceptions\InternalError;
use system\model2\RecordsetInterface;
use system\model2\Table;

class Model {
  private static $recordsets = array();
  private static $recordsetIds = array();
  
  /**
   * Flush the cache
   */
  public static function flushCache() {
    self::$recordsets = array();
    self::$recordsetIds = array();
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
  public static function loadById($tableName, $id, $reset = false) {
    if (!\array_key_exists($id, self::$recordsets) || $reset) {
      $table = Main::getTable($tableName);
      
      $pkeyFields = $table->getPrimaryKey()->getFields();
      if (count($pkeyFields) != 1) {
        throw new InternalError('Unable to get a cached recordset from table <em>@table</em>.', array(
          '@table' => $tableName
        ));
      }
      
      self::$recordsets[$id] = $table->selectFirst($table->filter(\reset($pkeyFields)->getName(), $id));
    }
    
    return self::$recordsets[$id];
  }
  
  /**
   * Retrieves the recordset id
   * @param string $tableName Table name
   * @param array $fields Associative array field name => value
   * @return mixed Id value or false if no record was found
   */
  protected static function getRecordsetId($tableName, array $fields) {
    $hash = '';
    foreach ($fields as $k => $v) {
      $hash .= (empty($hash) ? '' : ',') . $k . ':"' . \addcslashes($v, '"\\') . '"';
    }
    
    if (!isset(self::$recordsetIds[$tableName])) {
      self::$recordsetIds[$tableName] = array();
    }
    
    if (!isset(self::$recordsetIds[$tableName][$hash])) {
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

      self::$recordsetIds[$tableName][$hash] = ($table->countRecords() == 1)
        ? $table->selectFirst()->{\current($pkeyFields)->getName()}
        : null;
    }
    
    return self::$recordsetIds[$tableName][$hash];
  }
  
  /**
   * Gets a cached recordset.
   * @param string $tableName Table name
   * @param array $fields Associative array field name => value
   * @param bool $reset TRUE if needs to get a fresh recordset
   * @return RecordsetInterface Recordset
   */
  public static function loadBy($tableName, $fields, $reset = false) {
    $id = self::$getRecordsetId($tableName, $fields);
    return (!empty($id))
      ? self::$loadById($tableName, $id, $reset)
      : null;
  }
}