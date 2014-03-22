<?php
namespace system\model2\clauses;

/**
 * Custom clause
 */
class CustomClause implements FilterClauseInterface {
  /**
   * @var string Query
   */
  private $query;
  /**
   * @var array Query parameters
   */
  private $params;
  
  public function __construct($query, array $params = array()) {
    $this->query = (string)$query;
    $this->params = (array)$params;
  }
  
  public function getQuery() {
    $query = $this->query;
    foreach ($this->params as $key => $value) {
      $query = \str_replace($key, $value, $query);
    }
    return $query;
  }
}
