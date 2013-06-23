<?php
namespace system\model;

/**
 * Classe per le clausole LIMIT nella selezione dei records
 * @author Fabio Epifani
 */
class LimitClause implements SelectClauseInterface {

	private $limit;
	private $offset;

	/**
	 * Costruisce una clausola LIMIT
	 * @param limit Numero limite di record da selezionare
	 */
	public function __construct($limit, $offset=0) {
		$this->limit = $limit;
		$this->offset = $offset;
	}
	
	public function getOffset() {
		return $this->offset;
	}
	public function getLimit() {
		return $this->limit;
	}

	public function getQuery() {
		if ($this->offset > 0) {
			return $this->offset . ", " . $this->limit;
		} else {
			return $this->limit;
		}
	}

//	public function serialize() {
//		return \serialize(array($this->limit, $this->offset));
//	}
//
//	public function unserialize($serialized) {
//		list($limit, $offset) = \unserialize($serialized);
//		return new self($limit, $offset);
//	}
}
