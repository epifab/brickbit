<?php
namespace system\model;

/**
 * Classe per le clausole WHERE nella selezione dei records
 * @author Fabio Epifani
 */
class FilterClause implements SelectClauseInterface {
	/**
	 * Uguaglianza
	 */
	const OP_EQ                  = "="; //1;
	/**
	 * Diversit�
	 */
	const OP_NEQ                 = "<>"; //2;
	/**
	 * Operatore minore
	 */
	const OP_LT                  = "<"; //3;
	/**
	 * Operatore maggiore
	 */
	const OP_GT                  = ">"; //4;
	/**
	 * Operatore minore o uguale
	 */
	const OP_LTEQ                = "<="; //5;
	/**
	 * Operatore maggiore o uguale
	 */
	const OP_GTEQ                = ">="; //6;
	/**
	 * Operatore LIKE con carattere jolly (%) a destra
	 */
	const OP_STARTS              = "STARTS"; //7;
	/**
	 * Operatore LIKE con carattere jolly (%) a sinistra
	 */
	const OP_ENDS                = "ENDS"; //8;
	/**
	 * Operatore LIKE con caratteri jolly (%) a sinistra e a destra
	 */
	const OP_CONTAINS            = "CONTAINS"; //9;
	/**
	 * Operatore IS NULL
	 */
	const OP_IS_NULL             = "IS NULL"; //10;
	/**
	 * Operatore IS NOT NULL
	 */
	const OP_IS_NOT_NULL         = "IS NOT NULL"; //11;

	/**
	 * Tipo di confronto
	 */
	private $type;
	/**
	 * Informazioni sul campo
	 */
	private $field = null;
	/**
	 * Espressione da confrontare
	 */
	private $expression = null;

	public function __construct(Field $field, $type, $expression=null) {
		$this->setType($type);
//		if (($expression == null && $type != FilterClause::OP_IS_NULL && $type != FilterClause::OP_IS_NOT_NULL) ||
//			($expression != null && ($type == FilterClause::OP_IS_NULL || $type == FilterClause::OP_IS_NOT_NULL))) {
//			throw new \system\exceptions\InternalError("Parametri type o expression non validi");
//		}
		$this->expression = $expression;
		$this->field = $field;
	}

	private function setType($type) {

		if (is_int($type)) {
			switch ($type) {
				case FilterClause::OP_EQ:
				case FilterClause::OP_NEQ:
				case FilterClause::OP_LT:
				case FilterClause::OP_GT:
				case FilterClause::OP_LTEQ:
				case FilterClause::OP_GTEQ:
				case FilterClause::OP_STARTS:
				case FilterClause::OP_ENDS:
				case FilterClause::OP_CONTAINS:
				case FilterClause::OP_IS_NULL:
				case FilterClause::OP_IS_NOT_NULL:
					$this->type = $type;
					break;
				default:
					throw new \system\exceptions\InternalError('Invalid parameter type');
			}
		}

		else if (is_string($type)) {
			switch (strtoupper($type)) {
				case "EQ":
				case "EQUAL":
				case "=":
					$this->type = FilterClause::OP_EQ;
					break;
				case "NEQ":
				case "NOT_EQUAL":
				case "!=":
				case "<>":
					$this->type = FilterClause::OP_NEQ;
					break;
				case "LT":
				case "LESS_THAN":
				case "<":
					$this->type = FilterClause::OP_LT;
					break;
				case "GT":
				case "GREAT_THAN":
				case ">":
					$this->type = FilterClause::OP_GT;
					break;
				case "LTEQ":
				case "LESS_THEN_EQUAL":
				case "<=":
					$this->type = FilterClause::OP_LTEQ;
					break;
				case "GTEQ":
				case "GREAT_THAN_EQUAL":
				case ">=":
					$this->type = FilterClause::OP_GTEQ;
					break;
				case "STARTS":
					$this->type = FilterClause::OP_STARTS;
					break;
				case "ENDS":
					$this->type = FilterClause::OP_ENDS;
					break;
				case "CONTAINS":
					$this->type = FilterClause::OP_CONTAINS;
					break;
				case "IS_NULL":
					$this->type = FilterClause::OP_IS_NULL;
					break;
				case "IS_NOT_NULL":
					$this->type = FilterClause::OP_IS_NOT_NULL;
					break;
				default:
					throw new \system\exceptions\InternalError("Parametro type fuori dal range");
			}
		}

		else {
			throw new \system\exceptions\InternalError("Parametro type non valido");
		}
	}

	/**
	 * Query corrispondente alla clausola WHERE corrente
	 * @return Clausola WHERE da inserire nella query sql
	 */
	public function getQuery() {
		if ($this->field->isVirtual()) {
			$clause = $this->field->getExpression();
		} else {
			$clause = $this->field->getTableAlias() . "." . $this->field->getName();
		}
		
			switch ($this->type) {
				case FilterClause::OP_EQ:
					$x = $this->field->prog2Db($this->expression);
					if ($x == "NULL") {
						$clause .= " IS NULL";
					} else {
						$clause .= " = " . $x;
					}
					break;
				case FilterClause::OP_NEQ:
					$x = $this->field->prog2Db($this->expression);
					if ($x == "NULL") {
						$clause .= " IS NOT NULL";
					} else {
						$clause .= " <> " . $x;
					}
					break;
				case FilterClause::OP_LT:
					$clause .= " < " . $this->field->prog2Db($this->expression);
					break;
				case FilterClause::OP_GT:
					$clause .= " > " . $this->field->prog2Db($this->expression);
					break;
				case FilterClause::OP_LTEQ:
					$clause .= " <= " . $this->field->prog2Db($this->expression);
					break;
				case FilterClause::OP_GTEQ:
					$clause .= " >= " . $this->field->prog2Db($this->expression);
					break;
				case FilterClause::OP_CONTAINS:
					$clause .= " LIKE '%" . DataLayerCore::getInstance()->sqlRealEscapeStrings($this->expression) . "%'";
					break;
				case FilterClause::OP_STARTS:
					$clause .= " LIKE '" . DataLayerCore::getInstance()->sqlRealEscapeStrings($this->expression) . "%'";
					break;
				case FilterClause::OP_ENDS:
					$clause .= " LIKE '%" . DataLayerCore::getInstance()->sqlRealEscapeStrings($this->expression) . "'";
					break;
				case FilterClause::OP_IS_NULL:
					$clause .= " IS NULL";
					break;
				case FilterClause::OP_IS_NOT_NULL:
					$clause .= " IS NOT NULL";
					break;
			}
			return $clause;
	}

//	public function serialize() {
//		return \serialize(array(
//			$this->field,
//			$this->type,
//			$this->expression
//		));
//	}
//
//	public function unserialize($serialized) {
//		list($field, $type, $expression) = \unserialize($serialized);
//		return new self($field, $type, $expression);
//	}
}
