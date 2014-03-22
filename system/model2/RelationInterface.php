<?php
namespace system\model2;

interface RelationInterface extends TableInterface {
  /**
   * Relation type
   * @return string 1-1, 1-N, N-1
   */
  public function getType();
  /**
   * Join type
   * @return string INNER, LEFT or RIGHT
   */
  public function getJoinType();
  
  /**
   * Is this a * to 1 relationship?
   * @return boolean TRUE if this is a * to 1 relationship
   */
  public function isHasOne();
  /**
   * Is this a 1 to N relationship?
   * @return boolean TRUE if this is a 1 to N relationship
   */
  public function isHasMany();
  
  /**
   * Root table (e.g. $foo->bar->baz->getRootTable() returns $foo)
   * @return TableInterface Root table
   */
  public function getRootTable();
  /**
   * Parent table
   * @return TableInterface Relation parent table
   */
  public function getParentTable();
  
  /**
   * Join clauses
   * @return \system\model2\RelationClause[] Join clauses
   */
  public function getClauses();
  
  /**
   *  Lazy loading
   * @return bool TRUE if the relation is loaded on request
   */
  public function isLazyLoading();
  
  /**
   * Lazy loading
   * @param \system\model2\RecordsetInterface $parent
   * @return \system\model2\RecordsetInterface[] List of recordsets returned by the query
   */
  public function selectByParent(RecordsetInterface $parent);
  
  /**
   * Lazy loading
   * @param \system\model2\RecordsetInterface $parent
   * @return \system\model2\RecordsetInterface First result
   */
  public function selectFirstByParent(RecordsetInterface $parent);
  
  /**
   * Get the join clause
   * @param RecordsetInterface $recordset Recordset (for lazy loading)
   * @return clauses\FilterClauseInterface Join clause string
   */
  public function getJoinClause(RecordsetInterface $recordset = null);
}
