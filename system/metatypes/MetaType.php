<?php
namespace system\metatypes;

abstract class MetaType {

  protected $name;
  protected $type;
  protected $attributes;

  private final function __construct($name, $type, array $attributes) {
    $this->name = $name;
    $this->type = $type;
    $this->attributes = $attributes;
    $this->onInit();
  }
  
  protected function onInit() { }
  
//  public function serialize() {
//    return serialize(array('name' => $this->name, 'type' => $this->type, 'attributes' => $this->attributes));
//  }
//  
//  public function unserialize($data) {
//    $data = unserialize($data);
//    return self::newMetaType($data['name'], $data['type'], $data['attributes']);
//  }
  
  public static function getMetaTypesMap() {
    return \system\Main::invokeStaticMethodAllMerge('metaTypesMap');
  }
  
  public static function newMetaType($name, $type, $attributes = array()) {
    $fmap = self::getMetaTypesMap();
    
    if (!\array_key_exists($type, $fmap)) {
      throw new \system\exceptions\InternalError('Unknown metatype <em>@name</em>', array('@name' => $type));
    }
    $metaTypeClass = $fmap[$type];
    
    return new $metaTypeClass(
      $name,
      $type,
      $attributes
    );
  }
  
  public abstract function toProg($x);

  public function getDefaultValue() {
    return $this->toProg($this->getAttr('default', array('default' => null)));
  }

  public function attrExists($key) {
    return \array_key_exists($key, $this->attributes);
  }

  public function getAttr($key, $options = array()) {
    return \cb\array_item($key, $this->attributes, $options);
  }
  
  public function getAttributes() {
    return $this->attributes;
  }

  public function getName() {
    return $this->name;
  }
  
  public function getLabel() {
    return $this->getAttr('label', array('default' => $this->getName()));
  }

  public function getType() {
    return $this->type;
  }

  protected abstract function getEditWidgetDefault();

  public final function getEditWidget() {
    return $this->attrExists('widget') ? $this->getAttr('widget') : $this->getEditWidgetDefault();
  }

  public function db2Prog($x) {
    return $this->toProg($x);
  }

  public function prog2Db($x) {
    return $x;
  }

  public function edit2Prog($x) {
    $x = $this->toProg($x);
    $this->validate($x);
    return $x;
  }

  public function prog2Edit($x) {
    return $x;
  }
  
  public function validate($x) {
    
  }
  
  protected function toArray($x) {
    if (!\is_array($x)) {
      if (\is_null($x)) {
        return array();
      } else {
        return array($x);
      }
    } else {
      return $x;
    }
  }
}

