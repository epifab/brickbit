<?php
namespace system\model2;

abstract class TableProperty implements TablePropertyInterface {
  /**
   * @var string Property name
   */
  protected $name;
  /**
   * @var TableInterface Table
   */
  protected $table;
  /**
   * @var array Property info
   */
  protected $propertyInfo;
  
  abstract protected function init();
  
  public final function __construct($name, TableInterface $table, $propertyInfo) {
    $this->name = $name;
    $this->table = $table;
    $this->propertyInfo = $propertyInfo;
    $this->init();
  }
  
  /**
   * @return string Property alias
   */
  public function getAlias() {
    return $this->table->getAlias() . '_' . $this->name;
  }

  /**
   * @return array Property info
   */
  public function getInfo() {
    return $this->propertyInfo;
  }
  
  /**
   * Gets a info setting
   * @param string $name Name
   * @param array $options Options
   * @return mixed Value
   */
  protected function getInfoSetting($name, array $options = array('default' => null)) {
    return \cb\array_item($name, $this->propertyInfo, $options);
  }
  
  /**
   * @return string Property name
   */
  public function getName() {
    return $this->name;
  }

  /**
   * @return string Property path
   */
  public function getPath() {
    return $this->table->getPath() . '.' . $this->name;
  }
  
  /**
   * Gets the parent table
   * @return TableInterface Table
   */
  public function getTable() {
    return $this->table;
  }
}