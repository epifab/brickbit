<?php
namespace system\model2;

class RelationClause {
  /**
   * @var \system\model2\FieldInterface Parent field
   */
  private $parent = null;
  /**
   * @var \system\model2\FieldInterface Child field
   */
  private $child = null;
  
  public function __construct(\system\model2\FieldInterface $parent, \system\model2\FieldInterface $child) {
    $this->parent = $parent;
    $this->child = $child;
  }
  
  /**
   * @return \system\model2\FieldInterface Parent field
   */
  public function getParentField() {
    return $this->parent;
  }
  /**
   * @return \system\model2\FieldInterface Child field
   */
  public function getChildField() {
    return $this->child;
  }
}