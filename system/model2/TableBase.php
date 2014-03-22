<?php
namespace system\model2;

abstract class TableBase implements TableInterface {
  /**
   * @var boolean TRUE if you want to transparently importing the properties
   *  on demand
   */
  protected $autoImport = true;
  /**
   * @var array Table info
   */
  protected $tableInfo;
  /**
   * @var string Table name
   */
  protected $tableName;
  /**
   * @var string Table alias
   */
  protected $tableAlias;
  /**
   * @var \system\model2\PropertyInterface[] Imported properties by path
   */
  protected $importedPaths = array();
  /**
   * @var \system\model2\PropertyInterface[] Imported properties by name
   */
  protected $properties = array();
  /**
   * @var \system\model2\PropertyInterface[] Imported fields by name
   */
  protected $fields = array();
  /**
   * @var \system\model2\PropertyInterface[] Imported keys by name
   */
  protected $keys = array();
  /**
   * @var \system\model2\RelationInterface[] Imported relations by name
   */
  protected $relations = array();
  /**
   * @var \system\model2\RelationInterface[] Imported has one relations by name
   */
  protected $hasOneRelations = array();
  /**
   * @var \system\model2\RelationInterface[] Imported has many relations by name
   */
  protected $hasManyRelations = array();
  /**
   * @var \system\model2\PropertyInterface[] Imported virtuals by name
   */
  protected $virtuals = array();
  /**
   * @var \system\model2\KeyInterface Primary key
   */
  protected $primaryKey = null;
  
  
  protected function __construct($tableName, array $tableInfo) {
    $this->tableName = $tableName;
    $this->tableInfo = $tableInfo;
    $this->tableAlias = self::getUniqueAlias($tableName);
    
    $this->primaryKey = null;
    
    foreach ($this->tableInfo['keys'] as $name => $info) {
      // Forces keys import
      $key = $this->importKey($name);
      if ($key->isPrimary()) {
        if (!empty($this->primaryKey)) {
          throw new \system\exceptions\DataLayerError('Multiple primary key defined for table <em>@name</em>', array('@name' => $tableName));
        }
        $this->primaryKey = $key;
      }
    }
    if (empty($this->primaryKey)) {
      // A primary key is required
      throw new \system\exceptions\DataLayerError('Undefined primary key for table <em>@name</em>', array('@name' => $tableName));
    }
  }
  
  /**
   * Imports a set of properties.<br/>
   * Takes an unlimited number of property paths as arguments.<br/>
   * Notes:<br/>
   * <ul>
   *  <li>'*' imports every field and virtual property.</li>
   *  <li>'**' imports every field and virtual property for every relation.<br/>
   *    This is equivalent to:<br/>
   *    <pre>
   * $x->import('*');
   * $tableInfo = $x->getInfo();
   * foreach ($tableInfo['relations'] as $relationName => $relationInfo) {
   *  $x->importRelation($relationName)->import('*');
   * }
   *    </pre>
   *  </li>
   * </ul>
   */
  public function import() {
    foreach (\func_get_args() as $path) {
      $dotPosition = \strpos($path, '.');

      if ($dotPosition === false) {
        // Property name (e.g. *, **, field or key name)
        switch ($path) {
          case '**':
            foreach ($this->tableInfo['relations'] as $name => $info) {
              $this->importRelation($name)->import('*');
            }
            // Intentional no break here
          case '*':
            foreach ($this->tableInfo['fields'] as $name => $info) {
              $this->importProperty($name);
            }
            foreach ($this->tableInfo['virtuals'] as $name => $info) {
              $this->importProperty($name);
            }
            break;
          default:
            $this->importProperty($path);
            break;
        }
      }
      else {
        // Path (e.g. foo.bar.baz)
        //  relationName: foo
        $relationName = \substr($path, 0, $dotPosition);
        $subpath = \substr($path, $dotPosition + 1);
        
        $this->importRelation($relationName)->import($subpath);
      }
    }
  }
  
  /**
   * Checks whether ther property exists in the table
   * @param string $table Table name
   * @param string $path Property path
   * @return boolean True whether exists
   */
  public static function propertyExists($table, $path) {
    try {
      $tableInfo = \system\Main::getTable($table);
      $dotPosition = \strpos($path, '.');

      if ($dotPosition === false) {
        return isset($tableInfo['fields'][$path])
          || isset($tableInfo['keys'][$path])
          || isset($tableInfo['relations'][$path])
          || isset($tableInfo['virtuals'][$path]);
      }
      else {
        $relationName = \substr($path, 0, $dotPosition);
        return isset($tableInfo['relations'][$path])
          && self::propertyExists($tableInfo['relations'][$relationName]['table'], \substr($path, $dotPosition + 1));
      }
    } catch (\system\exceptions\Error $ex) {
      return false;
    }
  }

  /**
   * Imports a property
   * @param string $name 
   *  Property name
   * @throws \system\exceptions\DataLayerError 
   *  In case the property does not exist
   */
  private function _importProperty($name) {
    $property = null;
    if (isset($this->tableInfo['fields'][$name])) {
      $property = new Field($name, $this, $this->tableInfo['fields'][$name]);
      $this->fields[$name] = $property;
      $this->properties[$name] = $property;
    }
    else if (isset($this->tableInfo['keys'][$name])) {
      $property = new Key($name, $this, $this->tableInfo['keys'][$name]);
      $this->keys[$name] = $property;
      $this->properties[$name] = $property;
    }
    else if (isset($this->tableInfo['relations'][$name])) {
      $property = Relation::loadRelation($name, $this, $this->tableInfo['relations'][$name]);
      $this->relations[$name] = $property;
      $this->properties[$name] = $property;
      $property->isHasMany()
        ? $this->hasManyRelations[$name] = $property
        : $this->hasOneRelations[$name] = $property;
    }
    else if (isset($this->tableInfo['virtuals'][$name])) {
      $property = new Virtual($name, $this, $this->tableInfo['virtuals'][$name]);
      $this->virtuals[$name] = $property;
      $this->properties[$name] = $property;
    }
    else {
      throw new \system\exceptions\DataLayerError('Property <em>@name</em> not found in <em>@table</em>', array('@name' => $name, '@table' => $this->getTableName()));
    }
  }

  /**
   * Imports a property
   * @param string $path Property path
   * @return \system\model2\PropertyInterface Property
   * @throws \system\exceptions\DataLayerError
   *  In case path does not match any property
   */
  public function importProperty($path) {
    if (!isset($this->importedPaths[$path])) {
      $dotPosition = \strpos($path, '.');

      if ($dotPosition === false) {
        // Property name (e.g. field or key name)
        if (!isset($this->properties[$path])) {
          // Imports the property
          $this->_importProperty($path);
        }
        return $this->properties[$path];
      }
      else {
        // Path (e.g. foo.bar.baz)
        //  relationName: foo
        $relationName = \substr($path, 0, $dotPosition);
        if (!isset($this->properties[$relationName])) {
          // import foo if it's not been imported yet
          $this->_importProperty($relationName);
        }
        if (!isset($this->relations[$relationName])) {
          // foo does not exist or is not a known relation
          throw new \system\exceptions\DataLayerError('Relation <em>@name</em> not found in <em>@tanle</em>', array('@name' => $relationName, '@table' => $this->getTableName()));
        }

        $this->importedPaths[$path] = $this->relations[$relationName]->importProperty(\substr($path, $dotPosition + 1));
      }
    }
    return $this->importedPaths[$path];
  }
  
  /**
   * Imports a field
   * @param string $path Field path
   * @return \system\model2\FieldInterface Field
   * @throws \system\exceptions\DataLayerError
   */
  public function importField($path) {
    $property = $this->importProperty($path);
    if (!($property instanceof FieldInterface)) {
      throw new \system\exceptions\DataLayerError('Field <em>@field</em> not found', array('@field' => $path));
    }
    return $property;
  }
  
  /**
   * Imports a key
   * @param string $path Key path
   * @return \system\model2\KeyInterface Key
   * @throws \system\exceptions\DataLayerError
   */
  public function importKey($path) {
    $property = $this->importProperty($path);
    if (!($property instanceof KeyInterface)) {
      throw new \system\exceptions\DataLayerError('Key <em>@key</em> not found', array('@key' => $path));
    }
    return $property;
  }
  
  /**
   * Imports a relation
   * @param string $path Relation path
   * @return \system\model2\RelationInterface Relation
   * @throws \system\exceptions\DataLayerError
   */
  public function importRelation($path) {
    $property = $this->importProperty($path);
    if (!($property instanceof RelationInterface)) {
      throw new \system\exceptions\DataLayerError('Relation <em>@relation</em> not found', array('@relation' => $path));
    }
    return $property;
  }
  
  /**
   * Imports a virtual property
   * @param string $path Virtual path
   * @return \system\model2\VirtualInterface Virtual
   * @throws \system\exceptions\DataLayerError
   */
  public function importVirtual($path) {
    $property = $this->importProperty($path);
    if (!($property instanceof VirtualInterface)) {
      throw new \system\exceptions\DataLayerError('Virtual property <em>@virtual</em> not found', array('@virtual' => $path));
    }
    return $property;
  }

  /**
   * Returns the pre-imported property identified by $path.
   * @param string $path Property path
   * @return \system\model2\PropertyInterface
   *  Property or NULL in case $path does not match any known property
   */
  public function getProperty($path) {
    if ($this->autoImport) {
      try {
        // Try to import the property
        return $this->importProperty($path);
      }
      catch (\Exception $ex) {
        // Property not fuond
        return null;
      }
    }
    else {
      $dotPosition = \strpos($path, '.');

      if ($dotPosition === false) {
        return isset($this->properties[$path])
          ? $this->properties[$path]
          : null;
      }
      else {
        $relationName = \substr($path, 0, $dotPosition);
        return isset($this->relations[$relationName])
          ? $this->relations[$relationName]->getProperty(substr($path, $dotPosition+1))
          : null;
      }
    }
  }
  
  /**
   * Returns the pre-imported field identified by $path
   * @param string $path Field path
   * @return \system\model2\FieldInterface 
   *  Field or NULL in case $path does not match any known field
   */
  public function getField($path) {
    $property = $this->getProperty($path);
    return ($property instanceof \system\model2\FieldInterface)
      ? $property
      : null;
  }
  
  /**
   * Returns the pre-imported key identified by $path
   * @param string $path Key path
   * @return \system\model2\KeyInterface
   *  Key or NULL in case $path does not match any known key
   */
  public function getKey($path) {
    $property = $this->getProperty($path);
    return ($property instanceof \system\model2\KeyInterface)
      ? $property
      : null;
  }
  
  /**
   * Returns the primary key.
   * @return \system\model2\KeyInterface Primary key
   */
  public function getPrimaryKey() {
    return $this->primaryKey;
  }

  /**
   * Returns the pre-imported relation identified by $path
   * @param string $path Relation path
   * @return \system\model2\RelationInterface 
   *  Relation or NULL in case $path does not match any known relation
   */
  public function getRelation($path) {
    $property = $this->getProperty($path);
    return ($property instanceof \system\model2\RelationInterface)
      ? $property
      : null;
  }

  /**
   * Returns the pre-imported has one relation identified by $path
   * @param string $path Has one relation path
   * @return \system\model2\RelationInterface 
   *  Relation or NULL in case $path does not match any known has one relation
   */
  public function getHasOneRelation($path) {
    $property = $this->getRelation($path);
    return !empty($property) && $property->isHasOne()
      ? $property
      : null;
  }

  /**
   * Returns the pre-imported has many relation identified by $path
   * @param string $path Has many relation path
   * @return \system\model2\RelationInterface 
   *  Relation or NULL in case $path does not match any known has many relation
   */
  public function getHasManyRelation($path) {
    $property = $this->getRelation($path);
    return !empty($property) && $property->isHasMany()
      ? $property
      : null;
  }

  /**
   * Returns the pre-imported virtual identified by $path
   * @param string $path Vritual path
   * @return \system\model2\VritualInterface
   *  Vritual or NULL if not imported
   */
  public function getVirtual($path) {
    $property = $this->getProperty($path);
    return ($property instanceof \system\model2\VirtualInterface)
      ? $property
      : null;
  }

  /**
   * @return array Table info
   */
  public function getInfo() {
    return $this->tableInfo;
  }

  /**
   * @return string Table alias
   */
  public function getAlias() {
    return $this->tableAlias;
  }

  /**
   * @return string Table name
   */
  public function getName() {
    return $this->tableName;
  }
  
  /**
   * Table name.<br/>
   * This is guaranteed to always return the table name as the getName method
   *  would return the relation name if appropriate.
   * @return string Table name
   */
  public function getTableName() {
    return $this->tableName;
  }

  /**
   * @return string Table path (should be overriden by relation classes)
   */
  public function getPath() {
    return $this->getName();
  }

  /**
   * Gets the auto increment field for the table
   * @return \system\model2\FieldInterface
   *  Returns the serial field defined for this table (if any, NULL otherwise)
   */
  public function getAutoIncrementField() {
    return ($this->getPrimaryKey()->isAutoIncrement())
      ? \current($this->getPrimaryKey()->getFields())
      : null;
  }

  /**
   * Is there an auto increment field defined for the table?
   * @param boolean $autoImport
   *  TRUE if there is a serial field defined for the table
   */
  public function isAutoIncrement() {
    return $this->getPrimaryKey()->isAutoIncrement();
  }

  /**
   * Returns a unique alias based on the table name
   * @param string $tableName Table name
   * @return string Alias
   */
  private static function getUniqueAlias($tableName) {
    static $tableIds = array();
    if (!\array_key_exists($tableName, $tableIds)) {
      $tableIds[$tableName] = 1;
    } else {
      $tableIds[$tableName]++;
    }
    return $tableName . $tableIds[$tableName];
  }
  
  /**
   * Auto import properties on demand
   * @return boolean 
   *  TRUE if properties should be auto imported on demand
   */
  public function isAutoImport() {
    return $this->autoImport;
  }
  /**
   * Auto import properties on demand
   * @param boolean $autoImport
   *  TRUE if properties should be auto imported on demand
   */
  public function setAutoImport($autoImport) {
    $this->autoImport = (bool)$autoImport;
  }
  
  /**
   * Gets a list of imported fields
   * @return \system\model2\FieldInterface Imported fields
   */
  public function getFields() {
    return $this->fields;
  }
  
  /**
   * Gets a list of imported keys
   * @return \system\model2\KeyInterface Imported keys
   */
  public function getKeys(){
    return $this->keys;
  }
  
  /**
   * Gets a list of imported relations
   * @return \system\model2\RelationInterface Imported relations
   */
  public function getRelations() {
    return $this->relations;
  }
  
  /**
   * Gets a list of imported has one relations
   * @return \system\model2\RelationInterface Imported has one relations
   */
  public function getHasOneRelations() {
    return $this->hasOneRelations;
  }
  
  /**
   * Gets a list of imported has many relations
   * @return \system\model2\RelationInterface Imported has many relations
   */
  public function getHasManyRelations() {
    return $this->hasManyRelations;
  }
  
  /**
   * Gets a list of imported virtual properties
   * @return \system\model2\VirtualInterface Imported virtual properties
   */
  public function getVirtuals() {
    return $this->virtuals;
  }
  
  public function __get($name) {
    return $this->getProperty($name);
  }
}