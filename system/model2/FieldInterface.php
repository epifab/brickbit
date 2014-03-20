<?php
namespace system\model2;

interface FieldInterface extends TablePropertyInterface {
  /**
   * Metatype associated with the field.
   * @return \system\metatypes\MetaType
   */
  public function getMetatype();
  /**
   * This is intended to return a valid expression to identify the field 
   *  (e.g. [table alias].[field name]) to be used in a sql select statement.
   * @return string Select expression
   */
  public function getSelectExpression();
  }