<?php
namespace module\core\controller;
use \system\Main;

class Page extends Node {
  /**
   * @throws \system\exceptions\PageNotFound
   */
  public function runNotFound() {
    throw new \system\exceptions\PageNotFound();
  }
  
  public function runHome() {
    $this->setMainTemplate('home');
    
    Main::pushMessage(Main::settings()->toArray());
    
    return \system\RESPONSE_TYPE_READ;
  }
}