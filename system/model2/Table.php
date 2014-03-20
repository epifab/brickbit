<?php
namespace system\model2;

use system\Main;

/**
 * Class usage example
 * <pre>
 * // User #3
 * $x = new Table('user');
 * $x->import('*');
 * $x->addFilters($x->filter('id', 3));
 * 
 * // Heavy songs released prior to the first Jan 1970 whose author name is Jim 
 * //  or starts with Jo (John? Joe? can't remember now..)
 * $x = new TableQuery('song');
 * $x->import('*');
 * $x->addFilters(
 *   $x->filter('style', 'heavy', 'CONTAINS'),
 *   $x->filter('date', \mktime(00, 00, 00, 1, 1, 1970), '<'),
 *   $x->filterGroup('OR')->addClauses(
 *     $x->filter('author.name', 'Jim'),
 *     $x->filter('author.name', 'Jo', 'STARTS')
 *   )
 * );
 * $x->addSorts($x->sort('name', 'ASC'));
 * $x->getRelation('foo');
 * </pre>
 */
class Table extends TableBase {
  /**
   * @var \system\model2\clauses\FilterClauseGroup
   */
  protected $filterGroupClause = null;
  /**
   * @var \system\model2\clauses\SortClauseGroup
   */
  protected $sortGroupClause = null;
  /**
   * @var \system\model2\clauses\LimitClause
   */
  protected $limitClause = null;
  /**
   * @var \system\model2\FieldInterface Select key
   */
  protected $selectKey = null;
  
  protected function __construct($tableName, $tableInfo) {
    parent::__construct($tableName, $tableInfo);
    if (isset($this->tableInfo['selectKey'])) {
      $this->selectKey = $this->importField($this->tableInfo['selectKey']);
    }
    $this->filterGroupClause = new clauses\FilterClauseGroup('AND');
    $this->sortGroupClause = new clauses\SortClauseGroup();
  }
  
  /**
   * Initializes a new recordset
   * @param array $data Data returned by the select query
   * @return \system\model2\RecordsetInterface Recordset
   */
  public function newRecordset($data = null) {
    return new Recordset($this, $data);
  }
  
  /**
   * Adds filters to the clause.
   * This take an unlimited number of filter clauses. Each clause is expected to
   *  implements the \system\model2\clauses\FilterClauseInterface
   */
  public function addFilters() {
    foreach (func_get_args() as $filter) {
      if (!($filter instanceof clauses\FilterClauseInterface)) {
        throw new \system\exceptions\InternalError('Invalid filter parameter');
      }
      $this->filterGroupClause->addClauses($filter);
    }
  }
  /**
   * Adds sorts to the clause.
   * This take an unlimited number of sort clauses. Each clause is expected to
   *  implements the \system\model2\clauses\SortClauseInterface
   */
  public function addSorts() {
    foreach (func_get_args() as $sort) {
      if (!($sort instanceof clauses\SortClauseInterface)) {
        throw new \system\exceptions\InternalError('Invalid sort parameter');
      }
      $this->sortGroupClause->addClauses($sort);
    }
  }
  
  /**
   * Filter clauses
   * @return \system\model2\clauses\FilterClauseGroup
   */
  public function getFilter() {
    return $this->filterGroupClause;
  }
  
  /**
   * Sort clauses
   * @return \system\model2\clauses\SortClauseGroup
   */
  public function getSort() {
    return $this->sortGroupClause;
  }
  
  /**
   * Limit clause
   * @return \system\model\LimitClause
   */
  public function getLimit() {
    return $this->limitClause;
  }

  /**
   * Counts the number of pages which would be produced by the query.
   * @param int $pageSize Page size
   * @return int Number of pages
   */
  public function countPages($pageSize) {
    throw new \system\exceptions\UnderDevelopment();
  }

  /**
   * Counts the number of results which would be produced by the query.
   * @return int Number of results
   */
  public function countResults() {
    throw new \system\exceptions\UnderDevelopment();
  }

  /**
   * Initializes a filter clause group
   * @param string $type Logic operator (AND, OR)
   * @return \system\model2\clauses\FilterClauseGroup Filter clause group
   */
  public function filterGroup($type = 'AND') {
    return new \system\model2\clauses\FilterClauseGroup($type);
  }
  
  /**
   * Initializes a filter clause
   * @return \system\model2\clauses\FilterClauseInterface Filter clause
   */
  public function filter($path, $value, $eq = '=') {
    return new \system\model2\clauses\FilterClause($this->importField($path), $eq, $value);
  }
  /**
   * Initializes a custom filter clause
   * @return \system\model2\clauses\FilterClauseInterface Custom filter clasue
   */
  public function filterCustom($query, array $args = array()) {
    return new \system\model2\clauses\CustomClause($query, $args);
  }

  /**
   * Resets the filter clause to its original state
   */
  public function resetFilter() {
    $this->filterGroupClause->resetClauses();
  }
  /**
   * Resets the sort clause to its original state
   */
  public function resetSort() {
    $this->sortGroupClause->resetClauses();
  }
  
  protected function executeSelect($query) {
    Main::pushMessage(\system\utils\SqlFormatter::format($query));
    
    $dataAccess = DataLayerCore::getInstance();
    $result = $dataAccess->executeQuery($query);
    
    $recordsets = array();
    
    while (($data = $dataAccess->sqlFetchArray($result))) {
      $model2 = $this->newRecordset($data);
      empty($this->selectKey)
        ? $recordsets[] = $model2
        : $recordsets[$model2->{$this->selectKey->getName()}] = $model2;
    }
    
    $dataAccess->sqlFreeResult($result);
    
    return $recordsets;
  }

  /**
   * Performs the query and returns a list of recordsets.
   * @return \system\model2\RecordsetInterface[] List of recordsets returned by the query
   */
  public function select() {
    return $this->executeSelect($this->selectQuery()) ;
  }

  /**
   * Performs the query limiting the results to the first and returns the 
   *  recordset. NULL in case the query produced no results
   * @return \system\model2\RecordsetInterface Recordset
   */
  public function selectFirst() {
    $oldLimit = $this->limitClause;
    
    $this->setLimit(1);
    $result = $this->select();
    
    $this->limitClause = $oldLimit;
    
    if (empty($result)) {
      return null;
    } else {
      return \reset($result);
    }
  }
  
  /**
   * Returns the select query
   * @return string SQL query
   */
  public function selectQuery() {
    $q1 = '';
    $q2 = '';
    $this->initQuery($q1, $q2);
    
    $query = "SELECT {$q1} FROM {$q2}";
    
    if (!$this->filterGroupClause->isEmpty()) {
      $query .= ' WHERE ' . $this->filterGroupClause->getQuery();
    }
    if (!$this->sortGroupClause->isEmpty()) {
      $query .= ' ORDER BY ' . $this->sortGroupClause->getQuery();
    }
    if (!empty($this->limitClause)) {
      $query .= ' LIMIT ' . $this->limitClause->getQuery();
    }
    
    return $query;
  }
  
  /**
   * Initializes the select query.
   * @param string $q1 Select field list
   * @param string $q2 Select tables
   */
  public function initQuery(&$q1, &$q2) {
    if (empty($q2)) {
      $q2 .= $this->getTableName() . ' ' . $this->getAlias();
    }
    
    foreach ($this->getFields() as $field) {
      $q1 .= empty($q1) ? '' : ', ';
      $q1 .= $field->getSelectExpression() . ' AS ' . $field->getAlias();
    }

    // Has one relations
    foreach ($this->getHasOneRelations() as $relation) {
      if (!$relation->isLazyLoading()) {
        $q2 .= 
          ' ' . $relation->getJoinType() . ' JOIN ' . $relation->getTableName() . ' ' . $relation->getAlias()
          . ' ON ' . $relation->getJoinClause()->getQuery();
        
        if (!$relation->getFilter()->isEmpty()) {
          $q2 .= ' AND (' . $relation->getFilter()->getQuery() . ')';
        }

        $relation->initQuery($q1, $q2);
      }
    }
  }

  /**
   * Sets the limit clause.
   * @param int $limit Maximum number of records
   * @param int $offset Offset
   */
  public function setLimit($limit, $offset = 0) {
    $this->limitClause = new clauses\LimitClause($limit, $offset);
  }

  /**
   * Sets the limit clause.
   * @param int $pageSize Size of a page
   * @param int $page Page offset
   */
  public function setPage($pageSize, $page = 0) {
    $this->limitClause = new clauses\LimitClause($pageSize, $pageSize * $page);
  }

  /**
   * Initializes a sort clause
   * @return \system\model2\clauses\SortClauseInterface Sort clause
   */
  public function sort($path, $eq = 'ASC') {
    return new \system\model2\clauses\SortClause($this->importField($path), $eq);
  }
  
  /**
   * Load a table
   * @param string $tableName Table name
   * @return TableInterface Table
   * @throws \system\exceptions\InternalError
   */
  public static function loadTable($tableName) {
    $tableInfo = Main::getTable($tableName);
    if (isset($tableInfo['class'])) {
      $table = new $tableInfo['class']($tableName, $tableInfo);
      if (!($table instanceof TableInterface)) {
        throw new \system\exceptions\InternalError('Invalid class for table <em>@table</em>', array(
          '@table' => $parent->getTableName()
        ));
      }
    }
    else {
      $table = new self($tableName, $tableInfo);
    }
    return $table;
  }
}