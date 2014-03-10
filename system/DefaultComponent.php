<?php
namespace system;

class DefaultComponent extends \system\Component {
  public function __construct($url) {
    parent::__construct('default', 'system', 'Default', $url, array());
  }
  
  public function runDefault() {
    throw new exceptions\PageNotFound('Page not found');
  }
}