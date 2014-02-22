<?php
namespace module\core\controller;

use \system\Component;
use \system\model\Recordset;
use \system\model\RecordsetBuilder;
use \system\model\FilterClause;
use \system\model\FilterClauseGroup;
use \system\model\LimitClause;
use \system\model\SortClause;
use \system\model\SortClauseGroup;

class Page extends Node {
//  public static function access() {
//    return true;
//  }
  
  public function runNotFound() {
    $this->setPageTitle(\cb\t('Page not found'));
    $this->setMainTemplate('page-not-found');
    return \system\Component::RESPONSE_TYPE_READ;
  }
  
  public function runHome() {
    $this->setMainTemplate('home');
    return \system\Component::RESPONSE_TYPE_READ;
  }
}
?>