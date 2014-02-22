<?php
namespace system\model;

class FieldVirtual extends Field {
  /**
   * @var \system\utils\Handler
   */
  private $handler;
  
  public function __construct($name, $type, RecordsetBuilder $builder, array $attributes) {
    parent::__construct($name, $type, $builder, $attributes);
    
    $handler = $this->getAttr('handler', array('required' => true));
    $this->setHandler($handler);
    $dependencies = $this->getAttr('dependencies', array('default' => false));
    if ($dependencies && \is_array($dependencies)) {
      foreach ($dependencies as $d) {
        $builder->using($d);
      }
    }
  }
  
  public function isVirtual() {
    return true;
  }
  
  public function setHandler($handler) {
    $this->handler = new \system\utils\Handler($handler);
  }
  
  public function getHandler() {
    return $this->handler->getHandler();
  }

//  public function serialize() {
//    return \serialize(array(
//      $this->getName(),
//      $this->getType(),
//      $this->builder,
//      $this->getAttributes()
//    ));
//  }
//  
//  public function unserialize($serialized) {
//    list($a, $b, $c, $d) = \unserialize($serialized);
//    return new self($a, $b, $c, $d);
//  }
}
