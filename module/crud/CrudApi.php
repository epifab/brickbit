<?php
namespace crud;

use system\Main;
use system\exceptions\InternalError;
use system\model2\RecordsetInterface;
use system\model2\Table;
use system\model2\TableInterface;

class CrudApi {
  /**
   * Allows other modules to alter the cached table (by adding relations etc.)
   * @param TableInterface $table
   */
  public static function cachedRecordsetBuilderAlter(TableInterface $table) {
    Main::invokeMethodAll($table);
  }
  
  /**
   * Gets the table to retrieve a cached recordset.
   * @param string $tableName Table name
   * @return TableInterface Table
   */
  public static function cachedRecordsetBuilder($tableName) {
    static $tables = array();
    if (!isset($tables[$tableName])) {
      $tables[$tableName] = Table::loadTable($tableName);
      // By default imports every field and virtual
      $tables[$tableName]->import('*');
      self::cachedRecordsetBuilderAlter($table);
    }
    return $tables[$tableName];
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
  public static function cachedRecordset($tableName, $id, $reset = false) {
    static $recordsets = array();
    
    if (!\array_key_exists($id, $recordsets) || $reset) {
      $table = self::cachedRecordsetBuilder($tableName);
      
      $pkeyFields = $table->getPrimaryKey()->getFields();
      if (count($pkeyFields) != 1) {
        throw new InternalError('Unable to get a cached recordset from table <em>@table</em>.', array(
          '@table' => $tableName
        ));
      }
      
      $recordsets[$id] = $table->selectFirst($table->filter($pkeyFields[0]->getName(), $id));
    }
    
    return $recordsets[$id];
  }
}