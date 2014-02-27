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
    $this->addMessage('<pre>' . print_r($_SESSION, TRUE) . '</pre>');
    $this->setMainTemplate('home');
    return \system\Component::RESPONSE_TYPE_READ;
  }
}
