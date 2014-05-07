<?php
namespace system\model2;

use system\SystemApi;

/**
 * Recordset.
 */
class Recordset implements RecordsetInterface {
  /**
   * @var TableInterface Table
   */
  private $table;
  /**
   * @var array Stored fields values
   */
  private $fields = array();
  /**
   * @var array Fields values
   */
  private $modifiedFields = array();
  /**
   * @var RecordsetInterface[] Has one relations
   */
  private $hasOneRelations = array();
  /**
   * @var RecordsetInterface[][] Has many relations
   */
  private $hasManyRelations = array();
  /**
   * @var bool Is the recordset stored?
   */
  private $stored = false;
  /**
   * @var array Metadata
   */
  private $extra = array();
  
  public function __construct(TableInterface $table, array $data = null) {
    $this->table = $table;
    
    $this->stored = !empty($data);
    
    // It looks like there is some data
    // We still have to check if the current record exists
    if ($this->stored) {
      // We check that the primary key fields are actually not null before 
      //  concluding that the record is stored.
      $primaryKey = $this->getTable()->getPrimaryKey();
      foreach ($primaryKey->getFields() as $field) {
        if (empty($data[$field->getAlias()])) {
          // Empty field. We assume this record doesn't exist yet
          $this->stored = false;
          break;
        }
      }
    }
    
    // Fields initialization
    foreach ($this->getTable()->getFields() as $name => $field) {
      $this->fields[$name] = (!\is_null($data) && \array_key_exists($field->getAlias(), $data))
        ? $field->getMetatype()->db2Prog($data[$field->getAlias()])
        : $field->getMetatype()->getDefaultValue();
    }

    foreach ($this->getTable()->getHasOneRelations() as $name => $relation) {
      if (!$relation->isLazyLoading()) {
        $this->hasOneRelations[$name] = $relation->newRecordset($data);
      }
    }
  }
  
  /**
   * Checks whether the record is stored
   * @return bool TRUE if the record is stored
   */
  public function isStored() {
    return $this->stored;
  }
  
  /**
   * Gets the recordset table
   * @return \system\model2\TableInterface Table
   */
  public function getTable() {
    return $this->table;
  }

  /**
   * Searchs for a property in the recordset
   * @param string $path Property path
   * @param bool $required Sets it to TRUE to throw an exception if the path 
   *  does not match any property
   * @return TablePropertyInterface Recordset property or NULL if $path does not
   *  match any property and $required isn't set to TRUE
   * @throws \system\exceptions\InternalError
   */
  public function search($path, $required = false) {
    if (empty($path)) {
      return $this;
    }
    $dotPosition = strpos($path, '.');
    try {
      if ($dotPosition === false) {
        return $this->__get($path);
      }
      else {
        $first = $this->__get(substr($path, 0, $dotPosition));
        if ($first instanceof RecordsetInterface) {
          return $first->search(substr($path, $dotPosition+1), $required);
        }
        else {
          throw new \system\exceptions\InternalError('Relation <em>@path</em> not found in <em>@table</em>.', array('@path' => $name, '@table' => $this->getTable()->getTableName()));
        }
      }
    } catch (\system\exceptions\InternalError $ex) {
      if ($required) {
        throw new \system\exceptions\InternalError('Unreachable path <em>@path</em> from table <em>@table</em>', array('@path' => $path, '@table' => $this->getTable()->getTableName()));
      }
      return null;
    }
  }
  
  /**
   * Gets a recordset property
   * @param string $path Property path
   * @return mixed Property value
   */
  public function get($path) {
    $dotPosition = \strpos($path, '.');
    
    if ($dotPosition === false) {
      $property = $this->getTable()->getProperty($path);

      // Unknown property
      if (\is_null($property)) {
        throw new \system\exceptions\InternalError('Unknown property <em>@path</em> (table <em>@table</em>)', array('@path' => $path, '@table' => $this->getTable()->getTableName()));
      } 

      // Field
      elseif ($property instanceof FieldInterface) {
        if (\array_key_exists($path, $this->modifiedFields)) {
          return $this->modifiedFields[$path];
        }
        elseif (\array_key_exists ($path, $this->fields)) {
          return $this->fields[$path];
        }
        else {
          // Unknown field value
          throw new \system\exceptions\InternalError('Field <em>@name</em> has not been selected on table <em>@table</em>', array('@name' => $path, '@table' => $this->getTable()->getName()));
        }
      }

      // Relation
      elseif ($property instanceof RelationInterface) {
        if ($property->isHasMany()) {
          if (!\array_key_exists($path, $this->hasManyRelations)) {
            if ($this->isStored()) {
              // Has many relation hasn't been loaded yet
              $this->hasManyRelations[$path] = $property->selectByParent($this);
            }
            else {
              return array();
            }
          }
          return $this->hasManyRelations[$path];
        }
        else {
          if (!\array_key_exists($path, $this->hasOneRelations)) {
            if ($this->isStored()) {
              // Has one relation hasn't been loaded yet
              return $this->hasOneRelations[$path] = $property->selectFirstByParent($this);
            }
            else {
              return null;
            }
          }
          return $this->hasOneRelations[$path];
        }
      }

      // Virtual property
      elseif ($property instanceof VirtualInterface) {
        return $property->runHandler($this);
      }

      // Key
      elseif ($property instanceof KeyInterface) {
        $key = array();
        foreach ($property->getFields() as $field) {
          $key[$field->getName()] = $this->fields[$field->getName()];
        }
        return $key;
      }
      
      else {
        return null;
      }
    }
    
    else {
      $relation = \substr($path, 0, $dotPosition);
      $subpath = \substr($path, $dotPosition + 1);
      
      $recordset = $this->get($relation);
      if ($recordset instanceof RecordsetInterface) {
        return $recordset->get($subpath);
      }
      else {
        throw new \system\exceptions\InternalError(
          'Unable to find property <em>@path</em> following the has one relations path (table <em>@table</em>)',
          array('@path' => $path, '@table' => $this->getTable()->getTableName())
        );
      }
    }
  }
  
  public function __get($name) {
    return $this->get($name);
  }

  /**
   * Sets a recordset property
   * @param string $path Property path
   * @param mixed $value Property value
   */
  public function set($path, $value) {
    $dotPosition = \strpos($path, '.');
    
    if ($dotPosition === false) {
      $property = $this->getTable()->getProperty($path);

      // Unknown property
      if (\is_null($property)) {
        throw new \system\exceptions\InternalError('Unknown property <em>@path</em> (table <em>@table</em>)', array('@path' => $path, '@table' => $this->getTable()->getTableName()));
      } 

      // Field
      elseif ($property instanceof FieldInterface) {
        return $this->modifiedFields[$path] = $value;
      }

      // Relation
      elseif ($property instanceof RelationInterface) {
        if ($property->isHasMany()) {
          $this->hasManyRelations[$path] = $value;
        }
        else {
          return $this->hasOneRelations[$path] = $value;
        }
      }

      // Virtual property
      elseif ($property instanceof VirtualInterface) {
        throw new \system\exceptions\InternalError('Cannot set a virtual property value');
      }

      // Key
      elseif ($property instanceof KeyInterface) {
        throw new \system\exceptions\InternalError('Cannot set a key value');
      }
    }
    else {
      $relation = \substr($path, 0, $dotPosition);
      $subpath = \substr($path, $dotPosition + 1);
      
      $recordset = $this->get($relation);
      if ($recordset instanceof RecordsetInterface) {
        $recordset->set($subpath, $value);
      }
      else {
        throw new \system\exceptions\InternalError(
          'Unable to find property <em>@path</em> following the has one relations path (table <em>@table</em>)',
          array('@path' => $path, '@table' => $this->getTable()->getTableName())
        );
      }
    }
  }
  
  public function __set($name, $value) {
    $this->set($name, $value);
  }
  
  /**
   * Gets recordset primary key value
   * @return array Primary key
   */
  public function getPrimaryKey() {
    return $this->get($this->getTable()->getPrimaryKey()->getName());
  }
  
  /**
   * Returns a filter clause initialised with the current recordset values
   * @param string $keyName Key name (fallback to primary key)
   * @return clauses\FilterClauseInterface Filter clause
   */
  private function getKeyFilter($keyName = null) {
    $key = empty($keyName) 
      ? $this->getTable()->getPrimaryKey()
      : $this->getTable()->importKey($keyName); // Just to make sure it exists
    
    $table = $this->getTable();
    
    $filter = $table->filterGroup('AND');
    foreach ($key->getFields() as $field) {
      $filter->addClauses(
        $table->filter($field->getName(), $this->get($field->getName()))
      );
    }
    return $filter;
  }
  
  /**
   * Adds meta data to the recordset.
   * @param string $key Key
   * @param mixed $value Value
   */
  public function setExtra($key, $value) {
    $this->extra[$key] = $value;
  }
  
  /**
   * Gets recordset meta data
   * @param string $key Extra key
   * @param mixed $default Default value
   * @return mixed Meta data
   */
  public function getExtra($key, $default = null) {
    return isset($this->extra[$key])
      ? $this->extra[$key]
      : $default;
  }
  
  /**
   * Assunig the primary key is <em>foo, bar</em> and this record has 
   *  fields[foo] => 2 fields[bar] => 3, then <em>foo = 2 AND bar = 3</em>
   *  is returned.
   * @return string e.g. id = 3
   */
  private function filterByPrimary() {
    $query = '';
    foreach ($this->table->getPrimaryKey()->getFields() as $field) {
      $query .= 
        (empty($query) ? '' : ' AND ')
        . $field->getName() . ' = ' . $field->getMetatype()->prog2Db($this->fields[$field->getName()]);
    }
    return $query;
  }
  
  /**
   * Creates the record
   */
  public function create() {
    SystemApi::onCreate($this);
    
    $q1 = '';
    $q2 = '';

    foreach ($this->getTable()->getFields() as $name => $field) {
      if (\array_key_exists($name, $this->fields)) {
        $value = $field->getMetatype()->prog2Db(\array_key_exists($name, $this->modifiedFields)
          ? $this->modifiedFields[$name]
          : $this->fields[$name]
        );
        $q1 .= (empty($q1) ? '' : ', ') . $name;
        $q2 .= (empty($q2) ? '' : ', ') . $value;
      }
    }
    
    if (empty($q1)) {
      throw new \system\exceptions\DataLayerError('Cannot create a record with no fields.');
    }

    $query = "INSERT INTO {$this->getTable()->getTableName()} ({$q1}) VALUES ({$q2})";

    $dataAccess = DataLayerCore::getInstance();
    $dataAccess->executeUpdate($query);
    
    foreach ($this->modifiedFields as $k => $v) {
      $this->fields[$k] = $v;
    }
    $this->modifiedFields = array();
    
    if ($this->getTable()->isAutoIncrement()) {
      $serial = $this->getTable()->getAutoIncrementField();
      $lid = $dataAccess->sqlLastInsertId();
      if (!empty($lid)) {
        $this->fields[$serial->getName()] = $lid;
      }
      else {
        SystemApi::watchdog(
          'model', 
          'Unable to retrieve the "last insert id" for table <em>@table</em>', 
          array('@table' => $this->getTable()->getTableName()),
          \system\LOG_WARNING
        );
      }
    }

    $this->stored = true;
  }

  /**
   * Updates the record
   */
  public function update() {
    $q1 = '';
    
    if (!$this->isStored()) {
      throw new \system\exceptions\InternalError('Recordset does not exist.');
    }

    SystemApi::onUpdate($this);

    if (!empty($this->modifiedFields)) {
      foreach ($this->modifiedFields as $name => $value) {
        $field = $this->getTable()->getField($name);
        $q1 .= (empty($q1) ? '' : ', ')
          . $name . ' = '
          . $field->getMetatype()->prog2Db($value);
      }

      $query = "UPDATE {$this->getTable()->getTableName()} SET {$q1} WHERE {$this->filterByPrimary()}";
      
      $dataAccess = DataLayerCore::getInstance();
      $dataAccess->executeUpdate($query);
    }
  }

  /**
   * Deletes the record
   */
  public function delete() {
    if (!$this->isStored()) {
      return;
    }
    SystemApi::onDelete($this);
    
    $query = "DELETE FROM {$this->getTable()->getTableName()} WHERE {$this->filterByPrimary()}";
    
    $dataAccess = DataLayerCore::getInstance();
    $dataAccess->executeUpdate($query);

    $this->stored = false;
  }

  /**
   * Saves the record
   */
  public function save() {
    return $this->isStored() ? $this->update() : $this->create();
  }
  
  /**
   * Checks the consistency of the given key
   * This is tipically useful before saving the recordset to ensure no duplicate
   *  keys are stored.
   * @param string $keyName Key name
   * @return boolean TRUE if the recordset key field values are valid
   * @throws InternalError
   */
  public function checkKey($keyName) {
    $tableName = $this->getTable()->getTableName();
    
    $table = Table::loadTable($tableName);
    
    $key = $table->importKey($keyName); // Just to ensure the key really exists
    $filter = $table->filterGroup('AND');
    foreach ($key->getFields() as $field) {
      $filter->addClauses(
        $table->filter($field->getName(), $this->get($field->getName()))
      );
    }
    
    if ($this->isStored()) {
      // We want to ignore this record
      // This is done by excluding the record with the same primary key
      $pkey = $table->getPrimaryKey();
      $pfilter = $table->filterGroup('OR');
      foreach ($pkey->getFields() as $field) {
        $pfilter->addClauses(
          // Note we get the key value from the fields array rather than
          //  modifiedField
          $table->filter($field->getName(), $this->fields[$field->getName()], '<>')
        );
      }
      // Adding the primary key filter
      $filter->addClauses($pfilter);
    }
    
    $table->addFilters($filter);

    return $table->countRecords() == 0;
  }

  /**
   * Recordset as array
   */
  public function toArray() {
    $array = array();
    foreach ($this->fields as $name => $value) {
      $array[$name] = $value;
    }
    foreach ($this->modifiedFields as $name => $value) {
      $array[$name] = $value;
    }
    foreach ($this->hasOneRelations as $name => $relation) {
      $array[$name] = $relation->toArray();
    }
    foreach ($this->hasManyRelations as $name => $relations) {
      $array[$name] = array();
      foreach ($relations as $key => $relation) {
        $array[$name][$key] = $relation->toArray();
      }
    }
    return $array;
  }
}
