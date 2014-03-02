<?php
namespace module\test11\controller;

use \system\Component;

class TestController extends Component {
  public function runTest() {
    $this->datamodel['test'] = $this->getModule();
    $this->setMainTemplate('test');
    return Component::RESPONSE_TYPE_READ;
  }
}