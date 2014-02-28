<?php
namespace module\core\controller;

class Page extends Node {
  /**
   * @throws \system\exceptions\PageNotFound
   */
  public function runNotFound() {
    throw new \system\exceptions\PageNotFound();
  }
  
  public function runHome() {
    $x = \system\utils\Cache::nodeTypes();
    $this->addMessage('<pre>' . print_r($x, TRUE) . '</pre>');
    $this->addMessage('<pre>' . print_r($_SESSION, TRUE) . '</pre>');
    $this->setMainTemplate('home');
    return \system\Component::RESPONSE_TYPE_READ;
  }
}
