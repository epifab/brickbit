<?php
namespace system\model2;

class Field extends TableProperty implements FieldInterface {
  /**
   * @var \system\metatypes\MetaType
   */
  protected $metaType;
  
  protected function init() {
    // Metatype initialization
    $this->metaType = \system\metatypes\MetaType::newMetaType(
      $this->name,
      $this->propertyInfo['type'],
      $this->propertyInfo
    );
  }
  
  /**
   * Metatype associated with the field.
   * @return \system\metatypes\MetaTypeInterface
   */
  public function getMetaType() {
    return $this->metaType;
  }

  /**
   * This is intended to return [table alias].[field name] to be used in a 
   *  sql select statement.
   * @return string Select expression
   */
  public function getSelectExpression() {
    return $this->getTable()->getAlias() . '.' . $this->getName();
  }
}
