<?php
namespace system\metatypes;

class MetaPassword extends MetaString {
  public function getEditWidgetDefault() {
    return 'password';
  }
  
  public function edit2Prog($x) {
    // Always crypt passwords
    return \md5($x);
  }
}
