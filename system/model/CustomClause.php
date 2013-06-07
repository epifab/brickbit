<?php
namespace system\model;

class CustomClause implements SelectClauseInterface {
	private $query;
	
	public function __construct($query) {
		$this->query = $query;
	}
	
	public function getQuery() {
		return $this->query;
	}
}
