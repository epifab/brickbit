<?php
namespace system\model;

/**
 * Classe per le clausole WHERE nella selezione dei records
 * @author Fabio Epifani
 */
class FilterClauseGroup implements SelectClauseInterface {
	/**
	 * Clausole figlie
	 */
	private $clauses = array();

	public function __construct() {
		foreach (func_get_args() as $arg) {
			$this->addClause($arg);
		}
	}

	public function addClauses() {
		foreach (func_get_args() as $arg) {
			$this->addClause($arg);
		}
	}

	private function addClause($arg) {
		$odd = (bool)(count($this->clauses) % 2);
		if ($odd) {
			// if arg is a clause the AND operator is assumed
			if (is_string($arg)) {
				switch (strtoupper($arg)) {
					case 'AND':
						$this->clauses[] = 'AND';
						break;
					case 'OR':
						$this->clauses[] = 'OR';
						break;
					default:
						throw new \system\error\InternalError('Invalid arg parameter. A logical operator was expected.');
				}
			} else if (\is_object($arg) && ($arg instanceof FilterClause || $arg instanceof FilterClauseGroup || $arg instanceof CustomClause)) {
				$this->clauses[] = 'AND';
				$this->clauses[] = $arg;
			} else {
				throw new \system\error\InternalError('Invalid arg parameter. A logical operator, FilterClause or FilterClauseGroup instance was expected.');
			}
		}
		else {
			if (\is_object($arg) && ($arg instanceof FilterClause || $arg instanceof FilterClauseGroup || $arg instanceof CustomClause)) {
				$this->clauses[] = $arg;
			} else {
				throw new \system\error\InternalError('Invalid arg parameter. A FilterClause or FilterClauseGroup instance was expected.');
			}
		}
	}

	/**
	 * Query corrispondente alla clausola WHERE corrente
	 * @return Clausola WHERE da inserire nella query sql
	 */
	public function getQuery() {
		$query = "";

		$clauseExpected = true;
		foreach ($this->clauses as $clause) {
			if ($clauseExpected) {
				$query .= $clause instanceof FilterClauseGroup ? "(" . $clause->getQuery() . ")" : $clause->getQuery();
			} else {
				$query .= ' ' . $clause . ' ';
			}
			$clauseExpected = !$clauseExpected;
		}
		if ($clauseExpected) {
			$query .= '1';
		}
		return $query;
	}

	public function serialize() {
		return \serialize($this->clauses);
	}

	public function unserialize($serialized) {
		$obj = new self();
		$obj->clauses = \unserialize($serialized);
		return $obj;
	}
}
