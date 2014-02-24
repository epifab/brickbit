<?php
namespace system\model;

/**
 * Classe per le clausole SORT nella selezione dei records
 * @author Fabio Epifani
 */
class SortClause implements SelectClauseInterface {
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
   * Informazioni sul campo per l'ordinamento
   */
  private $field;
  /**
   * Tipo dell'ordinamento
   */
  private $type;

  /**
   * Costruisce una clausola SORT BY
   * @param field Informazioni sul del campo
   * @param type Operatore di ordinamento
   */
  public function __construct(Field $field, $type) {
    $this->field = $field;

    if (is_string($type)) {
      switch (strtoupper($type)) {
        case "ASC":
          $this->type = SortClause::STYPE_ASC;
          break;
        case "DESC":
          $this->type = SortClause::STYPE_DESC;
          break;
        case "RAND":
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
   * Query corrispondente alla clausola ORDER BY corrente
   * @return Clausola ORDER BY da inserire nella query sql
   */
  public function getQuery() {
    if ($this->type == SortClause::STYPE_RAND) {
      return "RAND()";
    } else {
      return $this->field->getAlias() . " " . (($this->type == SortClause::STYPE_ASC) ? "ASC" : "DESC");
    }
  }

//  public function serialize() {
//    return \serialize(array($this->field, $this->type));
//  }
//
//  public function unserialize($serialized) {
//    list($field, $type) = \unserialize($serialized);
//    return new self($field, $type);
//  }
}
