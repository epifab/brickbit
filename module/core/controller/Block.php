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

class Block extends \system\Component {
  
  public function runMainMenu() {
    $mm = array();
    
    $rsb = new \system\model\RecordsetBuilder('node');
    $rsb->using(
      'id', 'type', 'url', 'text.title'
    );
    $rsb->addFilter(new \system\model\FilterClause($rsb->type, '=', 'page'));
    $rsb->addFilter(new \system\model\FilterClause($rsb->text->title, 'IS_NOT_NULL'));
    $rsb->addReadModeFilters(\system\utils\Login::getLoggedUser()  );
    
    $rs = $rsb->select();
    foreach ($rs as $r) {
      $mm[] = array(
        'id' => $r->id,
        'url' => $r->url,
        'title' => $r->text->title
      );
    }
    $this->setData('mainMenu', $mm);
    $this->setMainTemplate('main-menu');
    return \system\Component::RESPONSE_TYPE_READ;
  }
  
  public function runAdminMenu() {
    if (!\system\utils\Login::isAnonymous()) {
      $am = array(
        array('url' => 'user/' . \system\utils\Login::getLoggedUserId(), 'title' => 'account'),
      );
      if (\system\utils\Login::isSuperuser()) {
        $am[] = array('url' => 'users', 'title' => 'users');
        $am[] = array('url' => 'system/settings', 'title' => 'settings');
      }
      $this->setData('adminMenu', $am);
    }
    $this->setMainTemplate('admin-menu');
    return \system\Component::RESPONSE_TYPE_READ;
  }
  
  public function runLoginControl() {
    $this->setMainTemplate('login-control');
    return \system\Component::RESPONSE_TYPE_READ;
  }
}
?>