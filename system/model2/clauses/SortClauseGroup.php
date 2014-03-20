<?php
namespace system\model2\clauses;

/**
 * Sort clause group
 */
class SortClauseGroup implements SortClauseInterface {
  /**
   * @var SortClauseInterface[] Sort clauses
   */
  private $clauses = array();

  public function __construct() {
    foreach (func_get_args() as $arg) {
      if (!($arg instanceof SortClauseInterface)) {
        throw new \system\exceptions\InternalError("Invalid sort clause");
      }
      $this->clauses[] = $arg;
    }
  }

  /**
   * Adds sort clauses clauses
   * @throws \system\exceptions\InternalError
   */
  public function addClauses() {
    foreach (\func_get_args() as $arg) {
      if (!\is_object($arg) || !($arg instanceof SortClauseInterface)) {
        throw new \system\exceptions\InternalError("Invalid sort clause");
      }
      $this->clauses[] = $arg;
    }
    return $this;
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
   * Gets the sort clauses query
   * @return Sort clause
   */
  public function getQuery() {
    if ($this->isEmpty()) {
      throw new \system\exceptions\DataLayerError('Empty clause group cannot be converted into a query');
    }
    $query = "";
    $first = true;
    for ($i = 0; $i < \count($this->clauses); $i++) {
      $first ? $first = false : $query .= ", ";
      $query .= $this->clauses[$i]->getQuery();
    }
    return $query;
  }
}
