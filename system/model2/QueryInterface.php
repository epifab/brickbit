<?php
namespace system\model2;

interface QueryInterface {
  /**
   * Returns the select query
   * @return string SQL query
   */
  public function selectQuery();
  /**
   * Performs the query and returns a list of recordsets.
   * @return \system\model2\RecordsetInterface[] List of recordsets returned by the query
   */
  public function select();
  /**
   * Performs the query limiting the results to the first and returns the 
   *  recordset. NULL in case the query produced no results
   * @return \system\model2\RecordsetInterface Recordset
   */
  public function selectFirst();
  
  /**
   * Counts the number of results which would be produced by the query.
   * @return int Number of results
   */
  public function countResults();
  /**
   * Counts the number of pages which would be produced by the query.
   * @param int $pageSize Page size
   * @return int Number of pages
   */
  public function countPages($pageSize);
  
  /**
   * Resets the filter clause to its original state
   */
  public function resetFilter();
  /**
   * Resets the sort clause to its original state
   */
  public function resetSort();
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
   * @return \system\model\LimitClause
   */
  public function getLimit();
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
   * Initializes a custom filter clause
   * @return \system\model2\clauses\FilterClauseInterface Custom filter clasue
   */
  public function filterCustom($query, array $args = array());
  /**
   * Initializes a filter clause
   * @return \system\model2\clauses\FilterClauseInterface Filter clause
   */
  public function filter($path, $value, $eq = '=');
  /**
   * Initializes a sort clause
   * @return \system\model2\clauses\SortClauseInterface Sort clause
   */
  public function sort($path, $eq = 'ASC');
  
  /**
   * Initializes a filter clause group
   * @param string $type Logic operator (AND, OR)
   * @return \system\model2\clauses\FilterClauseGroup Filter clause group
   */
  public function filterGroup($type = 'AND');
  
  /**
   * Sets the limit clause.
   * @param int $limit Maximum number of records
   * @param int $offset Offset
   */
  public function setLimit($limit, $offset = 0);
  /**
   * Sets the limit clause.
   * @param int $pageSize Size of a page
   * @param int $page Page offset
   */
  public function setPage($pageSize, $page = 0);
}