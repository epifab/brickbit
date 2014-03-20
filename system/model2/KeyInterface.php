<?php
namespace system\model2;

interface KeyInterface extends TablePropertyInterface {
  /**
   * Returns the list of fields the key consists of
   * @return FieldInterface[] List of fields the key consists of
   */
  public function getFields();
  /**
   * @return bool TRUE if the key is 'serial'
   */
  public function isAutoIncrement();
  /**
   * @return bool TRUE if the key is 'primary'
   */
  public function isPrimary();
}