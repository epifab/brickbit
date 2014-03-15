<?php
namespace system\model;

class Key implements RecordsetPropertyInterface {
  private $fields = array();
  private $autoIncrement;
  private $primary;
  private $builder;
  private $name;
  private $desc;
  
  public function __construct($name, RecordsetBuilder $builder, $primary=false, $autoIncrement=false) {
    $this->name = $name;
    $this->builder = $builder;
    $this->autoIncrement = $autoIncrement;
  }
  
  
  public function setPrimary($primary) {
    $this->primary = $primary;
  }
  public function setAutoIncrement($autoIncrement) {
    $this->autoIncrement = $autoIncrement;
  }
  
  public function addField(Field $field) {
    $this->fields[$field->getName()] = $field;
  }
  
  public function setDesc($desc) {
    $this->desc = (string)$desc;
  }
  
  public function getFields() {
    return $this->fields;
  }
  
  public function getDesc() {
    return empty($this->desc) ? $this->name : $this->desc;
  }
  
  public function getBuilder() {
    return $this->builder;
  }
  
  public function getName() {
    return $this->name;
  }
  
  public function getAlias() {
    return $this->builder->getTableAlias() . '_' . $this->getName();
  }
  
  public function isAutoIncrement() {
    return $this->autoIncrement;
  }
  
  public function isPrimary() {
    return $this->primary;
  }
}
