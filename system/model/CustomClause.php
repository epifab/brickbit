<?php
namespace system\model;

class CustomClause implements SelectClauseInterface {
	private $query;
	
	public function __construct($query) {
		$this->query = (string)$query;
	}
	
	public function getQuery() {
		return $this->query;
	}

	public function serialize() {
		return $this->query;
	}

	public function unserialize($serialized) {
		return new CustomClause($serialized);
	}
}
