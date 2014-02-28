<?php
namespace system\model;

class Recordset implements RecordsetInterface {
  /**
   * @var RecordsetBuilder
   */
  private $builder;
  private $fields = array();
  private $modifiedFields = array();
  private $hasOneRelations = array();
  private $hasManyRelations = array();
  private $stored = false;
  
  public function __construct(RecordsetBuilder $builder, $data=null) {

    $this->builder = $builder;

    $this->stored = !\is_null($data);
    
    // Inizializzo i valori dei campi
    foreach ($this->builder->getFieldList() as $name => $field) {
      if ($field->isVirtual()) {
        continue;
      }
      
      // inizializzo il campo
      $this->fields[$name] = null;
      
      if (!\is_null($data) && \array_key_exists($field->getAlias(), $data)) {
        $this->setDb($name, $data[$field->getAlias()]);
      } else {
        $this->setDb($name, $field->getDefaultValue());
      }
    }

    // Inizializzo le has one relations
    foreach ($this->builder->getHasOneRelationBuilderList() as $name => $builder) {
      // carico tutte le has many relation
      $this->hasOneRelations[$name] = $builder->newRecordset($data);
    }
  }
    
  public function isStored() {
    return $this->stored;
  }
  
  public function searchField($path, $required=false) {
    return $this->builder->searchField($path, $required);
  }
  
  public function getFieldList() {
    return $this->builder->getFieldList();
  }
  
  /**
   * @return \system\RecordsetBuilder
   */
  public function getBuilder() {
    return $this->builder;
  }
  
  public function __set($name, $value) {
    return $this->setProg($name, $value);
  }
  
  public function __get($name) {
    
    // RELATION COMPLETE AUTO IMPORT
    if (($this->builder->hasManyRelationExists($name) && !$this->builder->searchRelationBuilder($name))
      || ($this->builder->hasOneRelationExists($name) && !$this->builder->searchRelationBuilder($name))) {
      $this->builder->using($name);
      $this->builder->searchRelationBuilder($name, true)->using('*');
    }
    
    if (\array_key_exists($name, $this->hasOneRelations)) {
      // Has One Relation
      return $this->hasOneRelations[$name];
    }
    
    else if (\array_key_exists($name, $this->hasManyRelations)) {
      // Has Many Relation (precedentemente caricata)
      return $this->hasManyRelations[$name];
    }
    
    else if (\array_key_exists($name, $this->builder->getRelationBuilderList())) {
      // Relation valida ma ancora non caricata (LAZY LOAD)

      // recupero il builder del recordset corrispondente alla has many relation
      $builder = $this->builder->searchRelationBuilder($name);

      // salvo il filtro originale
      $oldFilter = $builder->getFilter();

      if ($builder->getFilterHandler()) {
        $handle = $builder->getFilterHandler();
        // handle should set filters and other stu
        $handle($this, $builder);
      }

      // costruisco un nuovo filtro con le clausole della relazione in AND con il filtro originale
      $newFilter = null;

      foreach ($builder->getClauses() as $clause) {
        $parentField = \key($clause);
        $childField = \current($clause);
        $field = $builder->searchField($childField);
        $filter = new FilterClause($field, "=", $this->fields[$parentField]);
        if (\is_null($newFilter)) {
          $newFilter = new FilterClauseGroup($filter);
        } else {
          $newFilter->addClauses("AND", $filter);
        }
      }
      if (!\is_null($oldFilter)) {
        $newFilter->addClauses("AND", $oldFilter);
      }
      // setto il nuovo filtro nel builder
      $builder->setFilter($newFilter);

      // seleziono i records
      if ($builder->hasMany()) {
        $this->hasManyRelations[$name] = $builder->select();
      } else {
        $this->hasOneRelations[$name] = $builder->selectFirst();
        if (\is_null($this->hasOneRelations[$name])) {
          $this->hasOneRelations[$name] = $builder->newRecordset();
        }
      }

      // reimposto il filtro originale
      $builder->setFilter($oldFilter);
      
      return $builder->hasMany()
        ? $this->hasManyRelations[$name]
        : $this->hasOneRelations[$name];

    } else {
      
      // Campo
      return $this->getProg($name);
      
    }
  }
  
  public function getPrimaryKey() {
    $fields = $this->builder->getPrimaryKey()->getFields();
    $fieldsVal = array();
    foreach ($fields as $field) {
      $fieldsVal[$field->getName()] = $this->getProg($field->getName());
    }
    return $fieldsVal;
  }
  
  public function getKey($keyName) {
    $key = $this->builder->getKey($keyName);
    if (!$key) {
      return null;
    } else {
      $fields = $key->getFields();
      $fieldsVal = array();
      foreach ($fields as $field) {
        $fieldsVal[$field->getName()] = $this->getProg($field->getName());
      }
      return $fieldsVal;
    }
  }
  
  public function search($path, $required=false) {
    if (empty($path)) {
      return $this;
    }
    $dotPosition = strpos($path, ".");
    try {
      if ($dotPosition === false) {
        return $this->{$path};
      } else {
        $first = $this->__get(substr($path, 0, $dotPosition));
        if ($first instanceof RecordsetInterface) {
          return $first->search(substr($path, $dotPosition+1), $required);
        } else {
          throw new \system\exceptions\InternalError('Relation <em>@path</em> not found in <em>@table</em>.', array('@path' => $name, '@table' => $this->getBuilder()->getTableName()));
        }
      }
    } catch (\system\exceptions\InternalError $ex) {
      if ($required) {
        throw $ex;
      }
      return null;
    }
  }
  
  public function searchParent($path, $required=false) {
    try {
      $dotPosition = strpos($path, ".");
      if (empty($path)) {
        throw new \system\exceptions\InternalError('Field or relation <em>@path</em> does not exist or is not used in <em>@table</em>.', array('@path' => $path, '@table' => $this->getBuilder()->getTableName()));
      }
      if ($dotPosition === false) {
        return array($this, $path);
      } else {
        $first = $this->__get(substr($path, 0, $dotPosition));
        if ($first instanceof RecordsetInterface) {
          return $first->searchParent(substr($path, $dotPosition+1), $required);
        } else {
          throw new \system\exceptions\InternalError('Field or relation <em>@path</em> not found in <em>@table</em>.', array('@path' => $path, '@table' => $this->getBuilder()->getTableName()));
        }
      }
    } catch (\system\exceptions\InternalError $ex) {
      if ($required) {
        throw $ex;
      }
      return null;
    }
  }

  public function setProg($path, $value) {
    list($rs, $name) = $this->searchParent($path, true);
    if (\array_key_exists($name, $rs->fields)) {
      if (!$rs->builder->searchField($name, true)->isVirtual()) {
        $rs->modifiedFields[$name] = $value;
      }
      $rs->fields[$name] = $value;
    } else {
      throw new \system\exceptions\InternalError('Field or relation <em>@path</em> not found in <em>@table</em>.', array('@path' => $path, '@table' => $this->getBuilder()->getTableName()));
    }
  }
  
  public function getProg($path) {
    list($rs, $name) = $this->searchParent($path, true);
    if (\array_key_exists($name, $rs->modifiedFields)) {
      return $rs->modifiedFields[$name];
    } else if (\array_key_exists($name, $rs->fields)) {
      return $rs->fields[$name];
    } else {
      $f = $rs->builder->searchField($name, true);
      if ($f->isVirtual()) {
        return \call_user_func($f->getHandler(), $rs);
      }
      throw new \system\exceptions\InternalError('Field, relation or key <em>@path</em> not found in <em>@table</em>.', array('@path' => $name, '@table' => $this->getBuilder()->getTableName()));
    }
  }
  
  public function setDb($path, $value) {
    list($rs, $name) = $this->searchParent($path, true);
    if (\array_key_exists($name, $rs->fields)) {
      $field = $rs->builder->searchField($name);
      $rs->fields[$name] = $field->db2Prog($value);
    }
  }
  
  public function getDb($path) {
    $progValue = $this->getProg($path);
    $field = $this->builder->searchField($path);
    return $field->prog2Db($progValue);
  }

  public function save($readMode=null, $editMode=null, $deleteMode=null) {
    if (!$this->isStored()) {
      return $this->create($readMode, $editMode, $deleteMode);
    } else {
      return $this->update($readMode, $editMode, $deleteMode);
    }
  }
  
  /**
   * Inserisce il record.
   * Nel caso la tabella contenga un campo MySql di tipo auto_increment
   * ne recupera contestualmente il valore.
   * Nel caso la tabella sfrutti i record modes, ne inserisce contestualmente le informazioni.
   * NB: NON INSERISCE AUTOMATICAMENTE RECORD RELATIVI A RELAZIONI HAS MANY O HAS ONE.
   * @param int $readMode Read Mode (vedi \system\model\RecordMode)
   * @param int $editMode Edit Mode (vedi \system\model\RecordMode)
   * @param int $deleteMode Delete Mode (vedi \system\model\RecordMode)
   */
  public function create($readMode=null, $editMode=null, $deleteMode=null) {
    
    \system\Main::raiseModelEvent("onCreate", $this);
    
    if ($this->builder->isRecordModed()) {
      $this->createRecordMode($readMode, $editMode, $deleteMode);
    }
    
    $q1 = "";
    $q2 = "";

    foreach ($this->builder->getFieldList() as $name => $field) {
      if (\array_key_exists($name, $this->fields)) {
        $value = $field->prog2Db(\array_key_exists($name, $this->modifiedFields)
          ? $this->modifiedFields[$name]
          : $this->fields[$name]
        );
        $q1 .= ($q1 === "" ? "" : ", ") . $name;
        $q2 .= ($q2 === "" ? "" : ", ") . $value;
      }
    }
    
    if ($q1 === "") {
      throw new \system\exceptions\InternalError('Cannot create a record with no field values.');
    }

    $query = "INSERT INTO {$this->builder->getTableName()} ({$q1}) VALUES ({$q2})";

    $dataAccess = DataLayerCore::getInstance();
    $dataAccess->executeUpdate($query);
    
    foreach ($this->modifiedFields as $k => $v) {
      $this->fields[$k] = $v;
    }
    $this->modifiedFields = array();
    
    if ($this->builder->isAutoIncrement()) {
      $primaryFieldList = $this->builder->getPrimaryKey()->getFields();
      $this->fields[current($primaryFieldList)->getName()] = $dataAccess->sqlLastInsertId();
    }

    $this->stored = true;
  }

  /**
   * Aggiorna il record.
   * Controlla contestualmente i permessi dell'utente.
   * NB: NON AGGIORNA AUTOMATICAMENTE LE RELAZIONI HAS ONE ED HAS MANY!
   */
  public function update($readMode=null, $editMode=null, $deleteMode=null) {
    $q1 = "";
    
    \system\Main::raiseModelEvent('onUpdate', $this);

    if (!$this->isStored()) {
      throw new \system\exceptions\InternalError('Recordset does not exist.');
    }
    
    // Controllo i permessi dell'utente per l'aggiornamento del record
    if ($this->builder->isRecordModed()) {
      $this->updateRecordMode($readMode, $editMode, $deleteMode);
    }

    if (\count($this->modifiedFields) > 0) {
      foreach ($this->modifiedFields as $name => $value) {
        $field = $this->builder->searchField($name);
        $q1 .= ($q1 === "" ? "" : ", ") . $name . " = " . $field->prog2Db($this->modifiedFields[$name]);
      }

      $query = "UPDATE {$this->builder->getTableName()} SET {$q1} WHERE {$this->filterByPrimaryClause()}";
      
      $dataAccess = DataLayerCore::getInstance();
      $dataAccess->executeUpdate($query);
    }
  }

  /**
   * Cancella il record.
   */
  public function delete() {
    if (!$this->isStored()) {
      return;
    }
    \system\Main::raiseModelEvent('onDelete', $this);
    
    $query = "DELETE FROM {$this->builder->getTableName()} WHERE {$this->filterByPrimaryClause()}";
    $dataAccess = DataLayerCore::getInstance();
    $dataAccess->executeUpdate($query);

    $this->stored = false;
  }
  
  private function filterByPrimaryClause() {
    if (!$this->builder->getPrimaryKey()) {
      throw new \system\exceptions\InternalError('No keys have been set for <em>@name</em> table.', array('@name' => $this->builder->getTableName()));
    }
    $first = true;
    $query = "";
    foreach ($this->builder->getPrimaryKey()->getFields() as $field) {
      if (!\array_key_exists($field->getName(), $this->fields) || \is_null($this->fields[$field->getName()])) {
        throw new \system\exceptions\InternalError('Primary key fields not imported or invalid.');
      }
      $first ? $first = false : $query .= " AND ";
      $query .= $field->getName() . " = " . $field->prog2Db($this->fields[$field->getName()]);
    }
    return $query;
  }

  /**
   *  Aggiunge informazioni sui permessi di lettura e scrittura sul record.
   * Setta la chiave esterna per il record_mode_idR
   * @param int $readMode Read Mode (vedi \system\model\RecordMode)
   * @param int $editMode Edit Mode (vedi \system\model\RecordMode)
   * @param int $deleteMode Delete Mode (vedi \system\model\RecordMode)
   */
  private function createRecordMode($readMode, $editMode, $deleteMode) {
    $recordMode = $this->record_mode;
    
    switch ($readMode) {
      case \system\model\RecordMode::MODE_NOBODY:
      case \system\model\RecordMode::MODE_SU:
      case \system\model\RecordMode::MODE_SU_OWNER:
      case \system\model\RecordMode::MODE_SU_OWNER_ADMINS:
      case \system\model\RecordMode::MODE_REGISTERED:
      case \system\model\RecordMode::MODE_ANYONE:
        $recordMode->read_mode = $readMode;
        break;
      
      default:
        $recordMode->read_mode = \system\model\RecordMode::MODE_ANYONE;
        break;
    }
    
    switch ($editMode) {
      case \system\model\RecordMode::MODE_NOBODY:
      case \system\model\RecordMode::MODE_SU:
      case \system\model\RecordMode::MODE_SU_OWNER:
      case \system\model\RecordMode::MODE_SU_OWNER_ADMINS:
      case \system\model\RecordMode::MODE_REGISTERED:
      case \system\model\RecordMode::MODE_ANYONE:
        $recordMode->edit_mode = $editMode;
        break;
      
      default:
        $recordMode->edit_mode = \system\model\RecordMode::MODE_SU_OWNER_ADMINS;
        break;
    }
    
    switch ($deleteMode) {
      case \system\model\RecordMode::MODE_NOBODY:
      case \system\model\RecordMode::MODE_SU:
      case \system\model\RecordMode::MODE_SU_OWNER:
      case \system\model\RecordMode::MODE_SU_OWNER_ADMINS:
      case \system\model\RecordMode::MODE_REGISTERED:
      case \system\model\RecordMode::MODE_ANYONE:
        $recordMode->delete_mode = $deleteMode;
        break;
      
      default:
        $recordMode->delete_mode = \system\model\RecordMode::MODE_SU_OWNER_ADMINS;
        break;
    }
    
    $recordMode->owner_id = \system\utils\Login::getLoggedUserId();
    $recordMode->ins_date_time = \time();
    $recordMode->last_upd_date_time = \time();
    $recordMode->last_modifier_id = \system\utils\Login::getLoggedUserId();
    
    $recordMode->create();
    
    if (\config\settings()->RECORD_MODE_LOGS) {
      $this->createRecordModeLog($recordMode);
    }
    
    $this->setProg($this->builder->getRecordModeField(), $recordMode->id);
  }
  
  private function updateRecordMode($readMode, $editMode, $deleteMode) {
    $recordMode = $this->record_mode;
    
    if (\is_null($recordMode)) {
      return null;
    }

    if (!\is_null($readMode)) {
      switch ($readMode) {
        case \system\model\RecordMode::MODE_NOBODY:
        case \system\model\RecordMode::MODE_SU:
        case \system\model\RecordMode::MODE_SU_OWNER:
        case \system\model\RecordMode::MODE_SU_OWNER_ADMINS:
        case \system\model\RecordMode::MODE_REGISTERED:
        case \system\model\RecordMode::MODE_ANYONE:
          $recordMode->read_mode = $readMode;
          break;

        default:
          // Lascio inalterato il valore
          break;
      }
    }
    
    if (!\is_null($editMode)) {
      switch ($editMode) {
        case null:
          break;
        case \system\model\RecordMode::MODE_NOBODY:
        case \system\model\RecordMode::MODE_SU:
        case \system\model\RecordMode::MODE_SU_OWNER:
        case \system\model\RecordMode::MODE_SU_OWNER_ADMINS:
        case \system\model\RecordMode::MODE_REGISTERED:
        case \system\model\RecordMode::MODE_ANYONE:
          $recordMode->edit_mode = $editMode;
          break;

        default:
          // Lascio inalterato il valore
          break;
      }
    }
    
    if (!\is_null($deleteMode)) {
      switch ($deleteMode) {
        case null:
          break;
        case \system\model\RecordMode::MODE_NOBODY:
        case \system\model\RecordMode::MODE_SU:
        case \system\model\RecordMode::MODE_SU_OWNER:
        case \system\model\RecordMode::MODE_SU_OWNER_ADMINS:
        case \system\model\RecordMode::MODE_REGISTERED:
        case \system\model\RecordMode::MODE_ANYONE:
          $recordMode->delete_mode = $deleteMode;
          break;

        default:
          // Lascio inalterato il valore
          break;
      }
    }

    $recordMode->last_modifier_id = \system\utils\Login::getLoggedUserId();
    $recordMode->last_upd_date_time = \time();
    $recordMode->update();
    
    if (\config\settings()->RECORD_MODE_LOGS) {
      $this->createRecordModeLog($recordMode);
    }
  }
  
  private function createRecordModeLog($recordMode) {
    $recordModeLog = new RecordsetBuilder("record_mode_log");
    $recordModeLog->using("*");

    $rs = $recordModeLog->newRecordset();

    $rs->record_mode_id = $recordMode->id;
    $rs->user_id = $recordMode->last_modifier_id;
    $rs->upd_date_time = $recordMode->last_upd_date_time;
    
    $rs->create();
  }
  
  /**
   * Checks the consistency of the given key
   * @param string $keyName Key name
   * @return boolean TRUE if the recordset key field values are valid
   * @throws InternalError
   */
  public function checkKey($keyName) {
    $key = $this->builder->searchKey($keyName, true);
    if (is_null($key)) {
      throw new InternalError('Key @name not found', array('@name' => $keyName));
    }
    
    $newFilter = null;
    foreach ($key->getFields() as $field) {
      $fieldValue = $this->{$field->getName()};
      $filterClause = new FilterClause($field, "=", $fieldValue);
      if (is_null($newFilter)) {
        $newFilter = new FilterClauseGroup($filterClause);
      } else {
        $newFilter->addClauses("AND", $filterClause);
      }
    }
    
    if ($this->isStored()) {
      // taglio il record corrispondente a quello che sto modificando
      $primary = $this->builder->getPrimaryKey();
      foreach ($primary as $field) {
        $fieldValue = $this->{$field->getName()};
        $filterClaue = new FilterClause($field, "<>", $fieldValue);
        $newFilter->addClauses("AND", $filterClaue);
      }
    }
    
    $oldFilter = $this->builder->getFilter();
    $this->builder->setFilter($newFilter);
    $numRecords = $this->builder->countRecords(true);
    $this->builder->setFilter($oldFilter);

    return ($numRecords == 0);
  }
  
  public function checkHasOneRelation($relationName, &$errors, $required=true) {
    $relationBuilder = $this->builder->searchHasOneRelationBuilder($relationName, true);
    
    $newFilter = null;
    $nullRelation = false;
    try {
      foreach ($relationBuilder->getClauses() as $parentFieldName => $childFieldName) {
        $fieldValue = $this->getEdit($parentFieldName);

        if (\is_null($fieldValue)) {
          if ($required || !\is_null($newFilter)) {
            // due possibilita':
            // 1 la relazione e' obbligatoria
            // 2 ci sono campi del join che sono stati specificati mentre questo e' nullo
            throw new \system\exceptions\ValidationError('Invalid null values for relation <em>@name</em>', array('@name' => $relationName));
          } else {
            // relazione non obbligatoria e campo (fin'ora) tutti nulli
            $nullRelation = true;
          }
        } else {
          if ($nullRelation) {
            // i campi fin'ora erano tutti nulli
            throw new \system\exceptions\ValidationError('Invalid null values for relation <em>@name</em>', array('@name' => $relationName));
          }
        }

        $field = $relationBuilder->getField($childFieldName);
        $filterClause = new FilterClause($field, "=", $fieldValue);
        if (\is_null($newFilter)) {
          $newFilter = new FilterClauseGroup($filterClause);
        } else {
          $newFilter->addClauses("AND", $filterClause);
        }
      }
      
      if (\is_null($newFilter)) {
        return true;
      }
    
      $oldFilter = $relationBuilder->getFilter();
      $relationBuilder->setFilter($newFilter);
      $numRecords = $relationBuilder->countRecords(true);
      $relationBuilder->setFilter($oldFilter);

      if ($numRecords == 0) {
        throw new ValidationError('No relation found');
      }
      
      return true;
      
    } catch (\system\exceptions\ValidationError $ex) {
      foreach ($relationBuilder->getClauses() as $parentField => $childField) {
        $errors[$this->builder->getField($parentField)->getAbsolutePath()] = $ex->getMessage();
      }
      return false;
    }
  }
  
  public function setRelation($name, $value) {
    if (\array_key_exists($name, $this->hasManyRelations)) {
      $this->hasManyRelations[$name] = (array)$value;
    }
    else if (\array_key_exists($name, $this->hasOneRelations)) {
      $this->hasOneRelations[$name] = $value;
    }
  }
  
  public function unsetRelation($name) {
    if (\array_key_exists($name, $this->hasManyRelations)) {
      unset($this->hasManyRelations[$name]);
    }
    else if (\array_key_exists($name, $this->hasOneRelations)) {
      unset($this->hasOneRelations[$name]);
    }
  }
  
  public function toArray() {
    $array = array();
    foreach ($this->fields as $name => $value) {
      $array[$name] = $value;
    }
    foreach ($this->modifiedFields as $name => $value) {
      $array[$name] = $value;
    }
    foreach ($this->hasOneRelations as $name => $builder) {
      $array[$name] = $builder->toArray();
    }
    foreach ($this->hasManyRelations as $name => $relations) {
      $array[$name] = array();
      foreach ($relations as $relation) {
        $array[$name][] = $relation->toArray();
      }
    }
    return $array;
  }

  public function serialize() {
    return \serialize(array(
      $this->builder->getTableName(),
      $this->getPrimaryKey()
    ));
  }

  public static function unserialize($serialized) {
  }
}
