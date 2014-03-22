<?php
namespace system\model2;

interface TableInterface extends PropertyInterface, QueryInterface {
  /**
   * Table name.<br/>
   * This is guaranteed to always return the table name as the getName() method
   *  would return the relation name if appropriate.
   * @return string Table name
   */
  public function getTableName();
  /**
   * Imports a set of properties.<br/>
   * Takes an unlimited number of property paths as arguments.<br/>
   * Notes:<br/>
   * '*' imports every field and virtual property.<br/>
   * '**' imports every field and virtual property for every relation.<br/>
   */
  public function import();
  /**
   * Imports a property
   * @param string $path Property path
   * @return \system\model2\PropertyInterface Property
   * @throws \system\exceptions\DataLayerError 
   *  In case path does not match any property
   */
  public function importProperty($path);
  
  /**
   * Imports a field
   * @param string $path Field path
   * @return \system\model2\FieldInterface Field
   * @throws \system\exceptions\DataLayerError
   */
  public function importField($path);
  
  /**
   * Imports a key
   * @param string $path Key path
   * @return \system\model2\KeyInterface Key
   * @throws \system\exceptions\DataLayerError
   */
  public function importKey($path);
  /**
   * Imports a relation
   * @param string $path Relation path
   * @return \system\model2\RelationInterface Relation
   * @throws \system\exceptions\DataLayerError
   */
  public function importRelation($path);
  /**
   * Imports a virtual property
   * @param string $path Virtual path
   * @return \system\model2\VirtualInterface Virtual
   * @throws \system\exceptions\DataLayerError
   */
  public function importVirtual($path);
  
  /**
   * Returns the pre-imported property identified by $path.
   * @param string $path Property path
   * @return \system\model2\PropertyInterface
   *  Property or NULL in case $path does not match any known property
   */
  public function getProperty($path);

  /**
   * Returns the pre-imported relation identified by $path
   * @param string $path Relation path
   * @return \system\model2\RelationInterface 
   *  Relation or NULL in case $path does not match any known relation
   */
  public function getRelation($path);
  /**
   * Returns the pre-imported has one relation identified by $path
   * @param string $path Has one relation path
   * @return \system\model2\RelationInterface 
   *  Relation or NULL in case $path does not match any known has one relation
   */
  public function getHasOneRelation($path);
  /**
   * Returns the pre-imported has many relation identified by $path
   * @param string $path Has many relation path
   * @return \system\model2\RelationInterface 
   *  Relation or NULL in case $path does not match any known has many relation
   */
  public function getHasManyRelation($path);
  /**
   * Returns the pre-imported field identified by $path
   * @param string $path Field path
   * @return \system\model2\FieldInterface 
   *  Field or NULL in case $path does not match any known field
   */
  public function getField($path);
  /**
   * Returns the pre-imported key identified by $path
   * @param string $path Key path
   * @return \system\model2\KeyInterface
   *  Key or NULL in case $path does not match any known key
   */
  public function getKey($path);
  /**
   * Returns the primary key.
   * @return \system\model2\KeyInterface Primary key
   */
  public function getPrimaryKey();
  /**
   * Returns the pre-imported virtual identified by $path
   * @param string $path Vritual path
   * @return \system\model2\VritualInterface
   *  Vritual or NULL if not imported
   */
  public function getVirtual($path);
  
  /**
   * Is there an auto increment field defined for the table?
   * @param boolean $autoImport
   *  TRUE if there is a serial field defined for the table
   */
  public function isAutoIncrement();
  /**
   * Gets the auto increment field for the table
   * @return \system\model2\FieldInterface
   *  Returns the serial field defined for this table (if any, NULL otherwise)
   */
  public function getAutoIncrementField();
  
  /**
   * Auto import properties on demand
   * @return boolean 
   *  TRUE if properties should be auto imported on demand
   */
  public function isAutoImport();
  /**
   * Auto import properties on demand
   * @param boolean $autoImport
   *  TRUE if properties should be auto imported on demand
   */
  public function setAutoImport($autoImport);
  /**
   * Gets a list of imported fields
   * @return \system\model2\FieldInterface Imported fields
   */
  public function getFields();
  /**
   * Gets a list of imported keys
   * @return \system\model2\KeyInterface Imported keys
   */
  public function getKeys();
  /**
   * Gets a list of imported relations
   * @return \system\model2\RelationInterface Imported relations
   */
  public function getRelations();
  /**
   * Gets a list of imported has one relations
   * @return \system\model2\RelationInterface Imported has one relations
   */
  public function getHasOneRelations();
  /**
   * Gets a list of imported has many relations
   * @return \system\model2\RelationInterface Imported has many relations
   */
  public function getHasManyRelations();
  /**
   * Gets a list of imported virtual properties
   * @return \system\model2\VirtualInterface Imported virtual properties
   */
  public function getVirtuals();
  /**
   * Initializes a new recordset
   * @param array $data Data returned by the select query
   * @return \system\model2\RecordsetInterface Recordset
   */
  public function newRecordset($data = null);
  /**
   * Gets the select key
   * @return FieldInterface Select key or NULL if not set
   */
  public function getSelectKey();
  /**
   * Sets the select key
   * @param FieldInterface $name Field to be used as the select key
   */
  public function setSelectKey(FieldInterface $field);
}