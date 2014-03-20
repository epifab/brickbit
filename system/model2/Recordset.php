<?php
namespace system\model2;

/**
 * Recordset.
 */
class Recordset implements RecordsetInterface {
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
  private $metaData = array();
  
  public function __construct(TableInterface $table, array $data = null) {
    $this->table = $table;
    
    $this->stored = !empty($data);
    
    // It looks like there is some data
    // We still have to check if the current record exists
    if ($this->stored) {
      // We check that the primary key fields are actually not null before 
      //  concluding that the record is stored.
      $primaryKey = $this->table->getPrimaryKey();
      foreach ($primaryKey->getFields() as $field) {
        if (empty($data[$field->getAlias()])) {
          // Empty field. We assume this record doesn't exist yet
          $this->stored = false;
          break;
        }
      }
    }
    
    // Fields initialization
    foreach ($this->table->getFields() as $name => $field) {
      $this->fields[$name] = (!\is_null($data) && \array_key_exists($field->getAlias(), $data))
        ? $field->getMetatype()->db2Prog($data[$field->getAlias()])
        : $field->getMetatype()->getDefaultValue();
    }

    foreach ($this->table->getHasOneRelations() as $name => $relation) {
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
   * @return mixed Recordset property or NULL if $path does not match any 
   *  property and $required isn't set to TRUE
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

  public function __get($name) {
    $property = $this->getTable()->getProperty($name);

    // Unknown property
    if (\is_null($property)) {
      throw new \system\exceptions\InternalError('Unreachable path <em>@path</em> from table <em>@table</em>', array('@path' => $name, '@table' => $this->getTable()->getTableName()));
    }
    
    // Field
    elseif ($property instanceof FieldInterface) {
      if (\array_key_exists($name, $this->modifiedFields)) {
        return $this->modifiedFields[$name];
      }
      else {
        return $this->fields[$name];
      }
    }
    
    // Relation
    elseif ($property instanceof RelationInterface) {
      if ($property->isHasMany()) {
        if (!\array_key_exists($name, $this->hasManyRelations)) {
          // Has many relation hasn't been loaded yet
          $this->hasManyRelations[$name] = $property->selectByParent($this);
        }
        return $this->hasManyRelations[$name];
      }
      else {
        if (!\array_key_exists($name, $this->hasOneRelations)) {
          // Has one relation already loaded
          return $this->hasOneRelations[$name] = $property->selectFirstByParent($this);
        }
        return $this->hasOneRelations[$name];
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
  }

  /**
   * Adds meta data to the recordset.
   * @param string $key Key
   * @param mixed $value Value
   */
  public function setMetaData($key, $value) {
    $this->metaData[$key] = $value;
  }
  
  /**
   * Gets recordset meta data
   * @return mixed Meta data
   */
  public function getMetaData($key) {
    return isset($this->metaData[$key])
      ? $this->metaData[$key]
      : null;
  }
  
  public function create() {
    throw new \system\exceptions\UnderDevelopment();
  }

  public function delete() {
    throw new \system\exceptions\UnderDevelopment();
  }

  /**
   * Saves the record
   */
  public function save() {
    return $this->isStored() ? $this->update() : $this->create();
  }

  public function update() {
    throw new \system\exceptions\UnderDevelopment();
  }
}