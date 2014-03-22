<?php
namespace system\model2\clauses;

/**
 * Filter clause group
 */
class FilterClauseGroup implements FilterClauseInterface {
  /**
   * @var FilterClauseInterface[] Clauses
   */
  private $clauses = array();
  /**
   * @var string Operator (AND, OR)
   */
  private $operator = 'AND';

  public function __construct($operator = 'AND') {
    switch (\strtoupper($operator)) {
      case 'AND':
      case 'OR':
        $this->operator = $operator;
        break;
      default:
        throw new \system\exceptions\InternalError('Invalid logic operator');
    }
  }

  /**
   * Adds filter clauses to the group.<br/>
   * This function takes an unlimited number of arguments.
   * @return \system\model2\clauses\FilterClauseInterface The clause group
   */
  public function addClauses() {
    foreach (func_get_args() as $arg) {
      if (!\is_object($arg) || !($arg instanceof FilterClauseInterface)) {
        throw new \system\exceptions\InternalError('Invalid arg parameter. A FilterClause or FilterClauseGroup instance was expected.');
      }
      \array_push($this->clauses, $arg);
    }
    return $this;
  }
  
  public function pushClause(FilterClauseInterface $clause) {
    \array_push($this->clauses, $clause);
  }
  
  public function popClause() {
    return \array_pop($this->clauses);
  }
  
  /**
   * Resets clauses
   */
  public function resetClauses() {
    $this->clauses = array();
  }
  
  /**
   * Checks if at least one clause has been added to the group
   * @return bool TRUE if the group doesn't contain any clause
   */
  public function isEmpty() {
    return empty($this->clauses);
  }

  /**
   * @return string Filter clause query
   */
  public function getQuery() {
    if ($this->isEmpty()) {
      throw new \system\exceptions\DataLayerError('Empty clause group cannot be converted into a query');
    }
    $query = '';
    foreach ($this->clauses as $clause) {
      $query .= 
        (!empty($query) ? ' ' . $this->operator . ' ' : '')
        . ($clause instanceof self ? '(' . $clause->getQuery() . ')' : $clause->getQuery());
    }
    return $query;
  }
}
