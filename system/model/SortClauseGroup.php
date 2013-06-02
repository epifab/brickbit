<?php
namespace system\model;

/**
 * Classe per le clausole ORDER BY nella selezione dei records
 * @author Fabio Epifani
 */
class SortClauseGroup implements SelectClauseInterface {
	/**
	 * Clausole figlie
	 */
	private $clauses = array();

	public function __construct() {
		if (func_num_args() == 0) {
			throw new \system\error\InternalError("Parametri non validi per la clausola Sort");
		}
		foreach (func_get_args() as $arg) {
			if ($arg instanceof SortClause) {
				$this->clauses[] = $arg;
			} else {
				throw new \system\error\InternalError("Parametri non validi per la clausola Sort");
			}
		}
	}

	public function addClauses() {
		foreach (\func_get_args() as $arg) {
			if ($arg instanceof SortClause) {
				$this->clauses[] = $arg;
			} else {
				throw new \system\error\InternalError("Parametri non validi per la clausola Sort");
			}
		}
	}

	/**
	 * Query corrispondente alla clausola ORDER BY corrente
	 * @return Clausola ORDER BY da inserire nella query sql
	 */
	public function getQuery() {
		$query = "";
		$first = true;
		for ($i = 0; $i < count($this->clauses); $i++) {
			$first ? $first = false : $query .= ", ";
			$query .= $this->clauses[$i]->getQuery();
		}
		return $query;
	}
}
?>