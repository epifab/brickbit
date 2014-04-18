<?php
namespace system\model2;

interface RecordsetInterface {
  /**
   * Gets the recordset table
   * @return \system\model2\TableInterface Table
   */
  public function getTable();
  /**
   * Searchs for a property in the recordset
   * @param string $path Property path
   * @param bool $required Sets it to TRUE to throw an exception if the path 
   *  does not match any property
   * @return TablePropertyInterface Recordset property or NULL if $path does not
   *  match any property and $required isn't set to TRUE
   */
  public function search($path, $required = false);
  
  /**
   * Creates the record
   */
  public function create();
  /**
   * Updates the record
   */
  public function update();
  /**
   * Saves the record
   */
  public function save();
  /**
   * Deletes the record
   */
  public function delete();
  
  /**
   * Checks whether the record is stored
   * @return bool TRUE if the record is stored
   */
  public function isStored();
  
  /**
   * Adds meta data to the recordset.
   * @param string $key Key
   * @param mixed $value Value
   */
  public function setExtra($key, $value);
  
  /**
   * Gets recordset meta data
   * @param string $key Extra key
   * @param mixed $default Default value
   * @return mixed Meta data
   */
  public function getExtra($key, $default = null);
  
  /**
   * Gets recordset primary key value
   * @return array Primary key
   */
  public function getPrimaryKey();
  
  /**
   * Gets a recordset property
   * @param string $path Property path
   * @return mixed Property value
   */
  public function get($path);
  
  /**
   * Sets a recordset property
   * @param string $path Property path
   * @param mixed $value Property value
   */
  public function set($path, $value);
  
  /**
   * Recordset as array
   */
  public function toArray();
}