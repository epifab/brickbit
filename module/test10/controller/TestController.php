<?php
namespace module\test10\controller;

use \system\Component;

class TestController extends Component {
  public function runTest() {
    $this->datamodel['test'] = $this->getModule();
    $this->setMainTemplate('test');
    return \system\RESPONSE_TYPE_READ;
  }
}