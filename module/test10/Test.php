<?php
namespace module\test10;

use \system\Main;

class Test {
  
  public static function test() {
    return array(
      'a' => 'test10',
      'b' => 'test10',
      'c' => 'test10'
    );
  }
  
  public static function onRun() {
    Main::pushMessage(Main::invokeStaticMethod('test', false));
    Main::pushMessage(Main::invokeStaticMethodAll('test', false));
    Main::pushMessage(Main::invokeStaticMethodAllMerge('test', false));
  }
}