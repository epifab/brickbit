<?php
namespace system\model2;

class Virtual extends TableProperty implements VirtualInterface {
  /**
   * @var \system\utils\Handler
   */
  private $handler;
  
  protected function init() {
    $handler = $this->getInfoSetting('handler', array('required' => true));
    $dependencies = $this->getInfoSetting('dependencies', array('type' => 'array', 'default' => array()));
    
    $this->handler = new \system\utils\Handler($handler);
    foreach ($dependencies as $dependency) {
      $this->getTable()->import($dependency);
    }
  }
  
  /**
   * Runs the virtual handler
   * @param \system\model2\RecordsetInterface $recordset Recordset
   * @return mixed Virtual property value
   */
  public function runHandler(RecordsetInterface $recordset) {
    return $this->handler->run($recordset);
  }
}
