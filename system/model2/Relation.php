<?php
namespace system\model2;

use \system\Main;

class Relation extends TableWrapper implements RelationInterface {
  /**
   * @var TableInterface Parent table
   */
  protected $parent;
  /**
   * @var string INNER, LEFT, RIGHT
   */
  protected $joinType;
  /**
   * @var array Info about the relation
   */
  protected $relationInfo;
  /**
   * @var \system\model2\RelationClause[]
   */
  protected $clauses = array();
  /**
   * @var string Relation name
   */
  protected $relationName;
  /**
   * @var bool Lazy loading
   */
  protected $lazyLoading = false;
  /**
   * @var \system\utils\Handler Additional filter handler
   */
  protected $filterHandler = null;
  
  protected function __construct($name, \system\model2\TableInterface $parent, array $info, array $tableInfo) {
    parent::__construct($info['table']);
    
    $this->parent = $parent;
    $this->relationInfo = $info;
    
    $this->relationName = $name;
    
    // Import fields involved in the clauses
    foreach ($info['clauses'] as $c) {
      $this->clauses[] = new RelationClause(
        // Parent
        $this->parent->importField(\key($c)),
        // Child
        $this->importField(\current($c))
      );
    }
    
    if (isset($info['join'])) {
      switch (\strtoupper($info['join'])) {
        case 'INNER':
        case 'LEFT':
        case 'RIGHT':
          $this->joinType = $info['join'];
          break;
      }
    }
    else {
      $this->joinType = 'LEFT';
    }
    
    // Forces has many relations to be loaded on request (to avoid rows 
    //  duplication) and allows has one relations to be optionally lazy loaded
    $this->lazyLoading = $this->isHasMany() || !empty($info['lazyLoading']);
    
    if (isset($info['selectKey'])) {
      // Override the table default select key
      $this->setSelectKey($this->importField($info['selectKey']));
    }
    
//    if (isset($info['filterHandler'])) {
//      $this->filterHandler = new \system\utils\Handler($info['filterHandler']);
//    }
  }
  
  /**
   * @return string Relation path
   */
  public function getPath() {
    $this->parent->getPath() . '.' . $this->getName();
  }
  
  /**
   * In a relation context, getName returns the relation name, not the table 
   *  name. Use getTableName() to get the table name instead.
   * @return string Relation name
   */
  public function getName() {
    return $this->relationName;
  }
  
  /**
   * Join type
   * @return string INNER, LEFT or RIGHT
   */
  public function getJoinType() {
    return $this->joinType;
  }
  
  /**
   * Parent table
   * @return TableInterface Relation parent table
   */
  public function getParentTable() {
    return $this->parent;
  }
  
  /**
   * Root table (e.g. $foo->bar->baz->getRootTable() returns $foo)
   * @return TableInterface Root table
   */
  public function getRootTable() {
    if ($this->parent instanceof Relation) {
      return $this->parent->getRootTable();
    }
    else {
      return $this->parent;
    }
  }
  
  /**
   * Is this a 1 to N relationship?
   * @return boolean TRUE if this is a 1 to N relationship
   */
  public function isHasMany() {
    return $this->relationInfo['type'] == '1-N';
  }
  
  /**
   * Is this a * to 1 relationship?
   * @return boolean TRUE if this is a * to 1 relationship
   */
  public function isHasOne() {
    return !$this->isHasMany();
  }

  /**
   * Relation type
   * @return string 1-1, 1-N, N-1
   */
  public function getType() {
    return $this->relationInfo['type'];
  }
  
  /**
   * Join clauses
   * @return \system\model2\RelationClause[] Join clauses
   */
  public function getClauses() {
    return $this->clauses;
  }

  /**
   * Should the relation be loaded on request?
   * @return bool Lazy loading?
   */
  public function isLazyLoading() {
    return $this->lazyLoading;
  }
  
  /**
   * Gets the join clause
   * @param \system\model2\RecordsetInterface $parent Parent recordset (lazy loading)
   * @return clauses\FilterClauseGroup Join clause
   */
  public function getJoinClause(RecordsetInterface $parent = null) {
    $join = $this->filterGroup('AND');
    foreach ($this->clauses as $clause) {
      $join->addClauses(new clauses\FilterClause(
        $clause->getChildField(),
        '=',
        (empty($parent) ? $clause->getParentField() : $parent->{$clause->getParentField()->getName()})
      ));
    }
    if (!empty($this->filterHandler)) {
      $join->addClauses($this->filterHandler->run($this, $parent));
    }
    return $join;
  }
  
  /**
   * Lazy loading
   * @param \system\model2\RecordsetInterface $parent
   * @return \system\model2\RecordsetInterface[] List of recordsets returned by the query
   */
  public function selectByParent(RecordsetInterface $parent) {
    $query = $this->selectQueryByParent($parent);
    
    Main::pushMessage(\system\utils\SqlFormatter::format($query));
    
    $dataAccess = DataLayerCore::getInstance();
    $result = $dataAccess->executeQuery($query);
    
    $recordsets = array();
    
    while (($data = $dataAccess->sqlFetchArray($result))) {
      $recordset = $this->newRecordset($data);
      if ($this->getSelectKey()) {
        $recordsets[$recordset->{$this->getSelectKey()->getName()}] = $recordset;
      }
      elseif ($this->isAutoIncrement()) {
        $recordsets[$recordset->{$this->getAutoIncrementField()->getName()}] = $recordset;
      }
      else {
        $recordsets[] = $recordset;
      }
    }
    
    $dataAccess->sqlFreeResult($result);
    
    return $recordsets;
  }
  
  /**
   * Returns the select query
   * @return string SQL query
   */
  public function selectQueryByParent(RecordsetInterface $parent) {
    $q1 = '';
    $q2 = '';
    $this->initQuery($q1, $q2);
    
    $query = "SELECT {$q1} FROM {$q2}";
    
    $query .= ' WHERE ' . $this->getJoinClause($parent)->getQuery();
    if (!$this->getFilter()->isEmpty()) {
      $query .= ' AND (' . $this->getFilter()->getQuery() . ')';
    }
    if (!$this->getFilter()->isEmpty()) {
      $query .= ' ORDER BY ' . $this->getFilter()->getQuery();
    }
    if ($this->getLimit()) {
      $query .= ' LIMIT ' . $this->getLimit()->getQuery();
    }
    
    return $query;
  }
  
  /**
   * Lazy loading
   * @param \system\model2\RecordsetInterface $parent
   * @return \system\model2\RecordsetInterface First result
   */
  public function selectFirstByParent(RecordsetInterface $parent) {
    $oldLimit = $this->limitClause;
    
    $this->setLimit($this->limit(1));
    $result = $this->selectByParent($parent);
    
    $this->limitClause = $oldLimit;
    
    if (empty($result)) {
      return null;
    } else {
      return \reset($result);
    }
  }
  
  /**
   * Load a relation.
   * @param string $name Relation name
   * @param \system\model2\TableInterface $parent Parent table
   * @param array $relationInfo Relation info
   * @return RelationInterface Relation
   * @throws \system\exceptions\InternalError
   */
  public static function loadRelation($name, TableInterface $parent, array $relationInfo) {
    $tableInfo = Main::getTable($relationInfo['table']);
    if (isset($relationInfo['class'])) {
      $relation = new $relationInfo['class']($name, $parent, $relationInfo, $tableInfo);
      if (!($relation instanceof RelationInterface)) {
        throw new \system\exceptions\InternalError('Invalid class for relation <em>@relation</em> in table <em>@table</em>', array(
          '@relation' => $name,
          '@table' => $parent->getTableName()
        ));
      }
    }
    else {
      $relation = new self($name, $parent, $relationInfo, $tableInfo);
    }
    return $relation;
  }
}