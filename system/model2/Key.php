<?php
namespace system\model2;

class Key extends TableProperty implements KeyInterface {
  /**
   * @var \system\model2\FieldInterface[] List of fields the key consists of
   */
  private $fields = array();
  /**
   * @return bool TRUE if the key is 'serial'
   */
  private $autoIncrement;
  /**
   * @return bool TRUE if the key is 'primary'
   */
  private $primary;
  
  protected function init() {
    $fields = $this->getInfoSetting('fields', array('required' => true, 'type' => 'array'));
    $this->autoIncrement = $this->getInfoSetting('autoIncrement', array('type' => 'bool', 'default' => false));
    $this->primary = $this->getName() == 'primary' || $this->getInfoSetting('primary', array('type' => 'bool', 'default' => false));
    
    foreach ($fields as $field) {
      $this->fields[$field] = $this->table->importField($field);
    }
  }
  
  /**
   * Returns the list of fields the key consists of
   * @return FieldInterface[] List of fields the key consists of
   */
  public function getFields() {
    return $this->fields;
  }
  
  /**
   * @return bool TRUE if the key is 'serial'
   */
  public function isAutoIncrement() {
    return $this->autoIncrement;
  }
  
  /**
   * @return bool TRUE if the key is 'primary'
   */
  public function isPrimary() {
    return $this->primary;
  }
}
