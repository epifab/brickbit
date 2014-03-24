<?php
namespace module\core\controller;
use \system\Main;

class Test {
  function x(Page $p1, \module\core\controller\Page $p2 = null, $asd = null) {
  }
}

class Page extends Node {
  /**
   * @throws \system\exceptions\PageNotFound
   */
  public function runNotFound() {
    throw new \system\exceptions\PageNotFound();
  }
  
  public function runHome() {
    $this->setMainTemplate('home');
    $this->setPageTitle('Home');

    return \system\RESPONSE_TYPE_READ;
  }
}