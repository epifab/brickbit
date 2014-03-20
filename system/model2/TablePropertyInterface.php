<?php
namespace system\model2;

interface TablePropertyInterface extends PropertyInterface {
  /**
   * Gets the parent table
   * @return TableInterface Table
   */
  public function getTable();
}