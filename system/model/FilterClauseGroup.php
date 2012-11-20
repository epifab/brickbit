<?php
namespace system\model;

/**
 * Classe per le clausole WHERE nella selezione dei records
 * @author Fabio Epifani
 */
class FilterClauseGroup implements SelectClauseInterface {
	/**
	 * Operatore di congiunzione
	 */
	const OP_AND = "AND"; //1;
	/**
	 * Operatore di disgiunzione
	 */
	const OP_OR  = "OR"; //2;

	/**
	 * Clausole figlie
	 */
	private $clauses = array();

	public function __construct() {
		$clauseExpected = true;
		foreach (func_get_args() as $arg) {
			$this->addClause(	$arg, $clauseExpected);
			$clauseExpected = !$clauseExpected;
		}
		if ($clauseExpected) {
			throw new \system\InternalErrorException("Parametri non validi per la clausola Filter");
		}
	}

	public function addClauses() {
		$clauseExpected = false;
		foreach (func_get_args() as $arg) {
			$this->addClause($arg, $clauseExpected);
			$clauseExpected = !$clauseExpected;
		}
		if ($clauseExpected) {
			throw new \system\InternalErrorException("Parametri non validi per la clausola Filter");
		}
	}

	private function addClause($arg, $clauseExpected) {
		if ($clauseExpected) {
			if ($arg instanceof FilterClauseGroup || $arg instanceof FilterClause || $arg instanceof CustomClause) {
				$this->clauses[] = $arg;
			} else {
				throw new \system\InternalErrorException("Parametri non validi per la clausola Filter");
			}
		} else {
			if (is_string($arg)) {
				switch (strtoupper($arg)) {
					case "AND":
						$this->clauses[] = FilterClauseGroup::OP_AND;
						break;
					case "OR":
						$this->clauses[] = FilterClauseGroup::OP_OR;
						break;
					default:
						throw new \system\InternalErrorException("Parametri non validi per la clausola Filter");
				}
			} else if (is_int($arg)) {
				if ($arg == FilterClauseGroup::OP_AND || $arg == FilterClauseGroup::OP_OR) {
					$this->clauses[] = $arg;
				}
			} else {
				throw new \system\InternalErrorException("Parametri non validi per la clausola Filter");
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
				if ($clause == FilterClauseGroup::OP_AND) {
					$query .= " AND ";
				} else {
					$query .= " OR ";
				}
			}
			$clauseExpected = !$clauseExpected;
		}
		return $query;
	}
}
?>