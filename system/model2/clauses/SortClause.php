<?php
namespace system\model2\clauses;

/**
 * Sort clauses
 */
class SortClause implements SortClauseInterface {
  /**
   * Ordinamento crescente
   */
  const STYPE_ASC = 1;
  /**
   * Ordinamento decrescente
   */
  const STYPE_DESC = 2;
  /**
   * Ordinamento casuale
   */
  const STYPE_RAND = 3;

  /**
   * @var int Sort type
   */
  private $type;
  /**
   * @var \system\model2\FieldInterface Field
   */
  private $field = null;

  public function __construct(\system\model2\FieldInterface $field, $type) {
    $this->field = $field;

    if (\is_string($type)) {
      switch (strtoupper($type)) {
        case 'ASC':
          $this->type = SortClause::STYPE_ASC;
          break;
        case 'DESC':
          $this->type = SortClause::STYPE_DESC;
          break;
        case 'RAND':
          $this->type = SortClause::STYPE_RAND;
          break;
        default:
          throw new \system\exceptions\InternalError('Invalid @name parameter', array('@name' => 'type'));
      }
    }

    else if (is_int($type)) {
      switch ($type) {
        case SortClause::STYPE_ASC:
        case SortClause::STYPE_DESC:
        case SortClause::STYPE_RAND:
          $this->type = $type;
          break;
        default:
          throw new \system\exceptions\InternalError('Invalid @name parameter', array('@name' => 'type'));
      }
    }
  }

  /**
   * Gets the sort clauses query
   * @return Sort clause
   */
  public function getQuery() {
    if ($this->type == SortClause::STYPE_RAND) {
      return 'RAND()';
    } else {
      return $this->field->getAlias() . ' ' . (($this->type == SortClause::STYPE_ASC) ? 'ASC' : 'DESC');
    }
  }
}
