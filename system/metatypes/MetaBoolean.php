<?php
namespace system\metatypes;

class MetaBoolean extends MetaType {
  public function prog2Db($x) {
    return $x ? "1" : "0";
  }
  
  public function validate($x) {
    
  }

  public function getEditWidgetDefault() {
    return 'checkbox';
  }
  
  public function toProg($x) {
    if (\is_null($x)) {
      return false;
    } else {
      return (bool)$x;
    }
  }
}
