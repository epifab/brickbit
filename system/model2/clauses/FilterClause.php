<?php
namespace system\model2\clauses;

/**
 * Filter clause query
 */
class FilterClause implements FilterClauseInterface {
  /**
   * Equals
   */
  const OP_EQ                  = '='; //1;
  /**
   * Not equal
   */
  const OP_NEQ                 = '<>'; //2;
  /**
   * Less than
   */
  const OP_LT                  = '<'; //3;
  /**
   * Greater than
   */
  const OP_GT                  = '>'; //4;
  /**
   * Less than or equals
   */
  const OP_LTEQ                = '<='; //5;
  /**
   * Greater than or equals
   */
  const OP_GTEQ                = '>='; //6;
  /**
   * Starts with (LIKE wildcard on the left)
   */
  const OP_STARTS              = 'STARTS'; //7;
  /**
   * Ends with (LIKE wildcard on the right)
   */
  const OP_ENDS                = 'ENDS'; //8;
  /**
   * Contains (LIKE wildcard on both sides)
   */
  const OP_CONTAINS            = 'CONTAINS'; //9;
  /**
   * Is null
   */
  const OP_IS_NULL             = 'IS NULL'; //10;
  /**
   * Is not null
   */
  const OP_IS_NOT_NULL         = 'IS NOT NULL'; //11;

  /**
   * @var string Operator type
   */
  private $type;
  /**
   * @var \system\model2\FieldInterface Field
   */
  private $field = null;
  /**
   * Expression
   */
  private $expression = null;

  public function __construct(\system\model2\FieldInterface $field, $type, $expression=null) {
    $this->setType($type);
    $this->expression = $expression;
    $this->field = $field;
    if (!($field instanceof \system\model2\FieldInterface)) {
      throw new \system\exceptions\InternalError('FIeld must be a field');
    }
  }

  private function setType($type) {
    if (\is_int($type)) {
      switch ($type) {
        case self::OP_EQ:
        case self::OP_NEQ:
        case self::OP_LT:
        case self::OP_GT:
        case self::OP_LTEQ:
        case self::OP_GTEQ:
        case self::OP_STARTS:
        case self::OP_ENDS:
        case self::OP_CONTAINS:
        case self::OP_IS_NULL:
        case self::OP_IS_NOT_NULL:
          $this->type = $type;
          break;
        default:
          throw new \system\exceptions\InternalError('Invalid parameter type');
      }
    }

    else if (\is_string($type)) {
      switch (\strtoupper($type)) {
        case 'EQ':
        case 'EQUAL':
        case '=':
          $this->type = self::OP_EQ;
          break;
        case 'NEQ':
        case 'NOT_EQUAL':
        case '!=':
        case '<>':
          $this->type = self::OP_NEQ;
          break;
        case 'LT':
        case 'LESS_THAN':
        case '<':
          $this->type = self::OP_LT;
          break;
        case 'GT':
        case 'GREAT_THAN':
        case '>':
          $this->type = self::OP_GT;
          break;
        case 'LTEQ':
        case 'LESS_THEN_EQUAL':
        case '<=':
          $this->type = self::OP_LTEQ;
          break;
        case 'GTEQ':
        case 'GREAT_THAN_EQUAL':
        case '>=':
          $this->type = self::OP_GTEQ;
          break;
        case 'STARTS':
          $this->type = self::OP_STARTS;
          break;
        case 'ENDS':
          $this->type = self::OP_ENDS;
          break;
        case 'CONTAINS':
          $this->type = self::OP_CONTAINS;
          break;
        case 'NULL':
        case 'IS_NULL':
          $this->type = self::OP_IS_NULL;
          break;
        case 'NOT_NULL':
        case 'IS_NOT_NULL':
          $this->type = self::OP_IS_NOT_NULL;
          break;
        default:
          throw new \system\exceptions\InternalError('Invalid @name parameter', array('@name' => 'type'));
      }
    }

    else {
      throw new \system\exceptions\InternalError('Invalid @name parameter', array('@name' => 'type'));
    }
  }
  
  private function escapeLike($value) {
    $value = \str_replace('%', '\\%', $value);
    $value = \str_replace('_', '\\_', $value);
    return $value;
  }

  /**
   * Gets the filter query
   * @return string Filter query
   */
  public function getQuery() {
    $clause = $this->field->getSelectExpression();

    $value = null;
    if (\is_object($this->expression) && $this->expression instanceof \system\model2\FieldInterface) {
      // The expression is a field
      $value = $this->expression->getSelectExpression();
    }
    else {
      // Relies the metatype to correctly escape the expression
      $value = $this->field->getMetatype()->prog2Db($this->expression);
    
      if (\is_null($value)) {
        switch ($this->type) {
          case self::OP_EQ:
          case self::OP_IS_NULL:
            $this->type = self::OP_IS_NULL;
            break;
          case self::OP_NEQ:
          case self::OP_IS_NOT_NULL:
            $this->type = self::OP_IS_NOT_NULL;
            break;
          default:
            $this->type = '';
            break;
        }
      }
    }
   
    switch ($this->type) {
      case self::OP_EQ:
        $clause .= ' = ?';
        break;
      case self::OP_NEQ:
        $clause .= ' <> ?';
        break;
      case self::OP_LT:
        $clause .= ' < ?';
        break;
      case self::OP_GT:
        $clause .= ' > ?';
        break;
      case self::OP_LTEQ:
        $clause .= ' <= ?';
        break;
      case self::OP_GTEQ:
        $clause .= ' >= ?';
        break;
      case self::OP_CONTAINS:
        $clause .= ' LIKE ?';
        if (\is_object($this->expression) && $this->expression instanceof \system\model2\FieldInterface) {
          $value = "'%' + " . $this->expression->getSelectExpression() . " + '%'";
        }
        else {
          $value = \system\metatypes\MetaString::stdProg2Db('%' . $this->expression . '%');
        }
        break;
      case self::OP_STARTS:
        $clause .= ' LIKE ?';
        if (\is_object($this->expression) && $this->expression instanceof \system\model2\FieldInterface) {
          $value = "'%' + " . $this->expression->getSelectExpression() . " + '%'";
        }
        else {
          $value = \system\metatypes\MetaString::stdProg2Db($this->expression . '%');
        }
        break;
      case self::OP_ENDS:
        $clause .= ' LIKE ?';
        if (\is_object($this->expression) && $this->expression instanceof \system\model2\FieldInterface) {
          $value = "'%' + " . $this->expression->getSelectExpression();
        }
        else {
          $value = \system\metatypes\MetaString::stdProg2Db('%' . $this->expression);
        }
        break;
      case self::OP_IS_NULL:
        $clause .= ' IS NULL';
        break;
      case self::OP_IS_NOT_NULL:
        $clause .= ' IS NOT NULL';
        break;
    }
    
    return \str_replace('?', $value, $clause);
  }
}
