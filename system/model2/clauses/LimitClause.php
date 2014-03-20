<?php
namespace system\model2\clauses;

class LimitClause implements \system\model2\clauses\ClauseInterface {
  private $limit;
  private $offset;
  
  public function __construct($limit, $offset = 0) {
    $this->limit = (int)$limit;
    $this->offset = (int)$offset;
    if ($this->limit <= 0) {
      throw new \system\exceptions\InternalError('Invalid @name parameter', array('@name' => 'limit'));
    }
    if ($this->offset < 0) {
      throw new \system\exceptions\InternalError('Invalid @name parameter', array('@name' => 'offset'));
    }
  }
  
  public function getQuery() {
    if ($this->offset > 0) {
      return $this->offset . ', ' . $this->limit;
    } else {
      return $this->limit;
    }
  }
}