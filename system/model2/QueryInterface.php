<?php
namespace system\model2;

interface QueryInterface {
  /**
   * Returns the select query
   * @return string SQL query
   */
  public function selectQuery();

  /*
   * Initializes the select query.
   * @param string $q1 Select field list
   * @param string $q2 Select tables
   */
  public function initQuery(&$q1, &$q2);
  /**
   * Performs the query and returns a list of recordsets.
   * @param clauses\FilterClauseInterface $tmpFilter Temporary filter to be 
   *  applied on selection
   * @return \system\model2\RecordsetInterface[] List of recordsets returned by 
   *  the query
   */
  public function select(clauses\FilterClauseInterface $tmpFilter = null);
  /**
   * Performs the query limiting the results to the first and returns the 
   *  recordset. NULL in case the query produced no results
   * @param clauses\FilterClauseInterface $tmpFilter Temporary filter to be 
   *  applied on selection
   * @return \system\model2\RecordsetInterface Recordset
   */
  public function selectFirst(clauses\FilterClauseInterface $tmpFilter = null);
  
  /**
   * Counts the number of results which would be produced by the query.
   * @return int Number of results
   */
  public function countRecords();
  /**
   * Counts the number of pages which would be produced by the query.
   * @param int $pageSize Page size
   * @return int Number of pages
   */
  public function countPages($pageSize);
  
  /**
   * Sort clauses
   * @return \system\model2\clauses\SortClauseGroup
   */
  public function getSort();
  /**
   * Filter clauses
   * @return \system\model2\clauses\FilterClauseGroup
   */
  public function getFilter();
  /**
   * Limit clause
   * @return \system\model2\clauses\LimitClause
   */
  public function getLimit();
  /**
   * Resets the filter clause to its original state
   */
  public function resetFilter();
  /**
   * Resets the sort clause to its original state
   */
  public function resetSort();
  /**
   * Resets the limit clause to its original state
   */
  public function resetLimit();
  /**
   * Adds filters to the clause.
   * This take an unlimited number of filter clauses. Each clause is expected to
   *  implements the \system\model2\clauses\FilterClauseInterface
   */
  public function addFilters();
  /**
   * Adds sorts to the clause.
   * This take an unlimited number of sort clauses. Each clause is expected to
   *  implements the \system\model2\clauses\SortClauseInterface
   */
  public function addSorts();
  /**
   * Sets the limit clause
   * @param clauses\LimitClause $limit Limit clause
   */
  public function setLimit(\system\model2\clauses\LimitClause $limit = null);

  /**
   * Initializes a filter clause
   * @return \system\model2\clauses\FilterClauseInterface Filter clause
   */
  public function filter($path, $value, $eq = '=');
  /**
   * Initializes a custom filter clause
   * @return \system\model2\clauses\FilterClauseInterface Custom filter clasue
   */
  public function filterCustom($query, array $args = array());
  /**
   * Initializes a filter clause group
   * @param string $type Logic operator (AND, OR)
   * @return \system\model2\clauses\FilterClauseGroup Filter clause group
   */
  public function filterGroup($type = 'AND');
  /**
   * Initializes a sort clause
   * @return \system\model2\clauses\SortClauseInterface Sort clause
   */
  public function sort($path, $eq = 'ASC');
  /**
   * Sets the limit clause.
   * @param int $limit Maximum number of records
   * @param int $offset Offset
   * @return clauses\LimitClause Limit clause
   */
  public function limit($limit, $offset = 0);
  /**
   * Sets the limit clause.
   * @param int $pageSize Size of a page
   * @param int $page Page offset
   * @return clauses\LimitClause Limit clause
   */
  public function pageLimit($pageSize, $page = 0);
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