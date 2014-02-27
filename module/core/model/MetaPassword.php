<?php
namespace module\core\model;

class MetaPassword extends \system\metatypes\MetaString {
  public function getEditWidgetDefault() {
    return 'password';
  }
  
  public function edit2Prog($x) {
    // Always crypt passwords
    return \md5($x);
  }
}
