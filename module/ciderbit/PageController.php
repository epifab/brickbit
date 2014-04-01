<?php
namespace module\ciderbit;

use \module\node\NodeCrudController;
use \system\exceptions\PageNotFound;

class PageController extends NodeCrudController {
  public static function accessHome() {
    return true;
  }
  
  /**
   * @throws \system\exceptions\PageNotFound
   */
  public function runNotFound() {
    throw new PageNotFound();
  }
  
  public function runHome() {
    $this->setMainTemplate('home');
    $this->setPageTitle('Home');

    return \system\RESPONSE_TYPE_READ;
  }
}