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
      $mm[$r->id] = array(
        'id' => $r->id,
        'url' => $r->url,
        'title' => $r->text->title
      );
    }
    $this->datamodel['mainMenu'] = $mm;
    $this->setMainTemplate('main-menu');
    return \system\Component::RESPONSE_TYPE_READ;
  }
  
  public function runAdminMenu() {
    $am = array();
    if (!\system\utils\Login::isAnonymous()) {
//      $am = array(
//        array(
//          'url' => 'user/' . \system\utils\Login::getLoggedUserId(), 
//          'title' => 'account'
//        )
//      );
      if (\system\utils\Login::isSuperuser()) {
        $am[] = array('url' => 'user/list', 'title' => \cb\t('users'), 'ajax' => false);
        $am[] = array('url' => 'system/settings', 'title' => \cb\t('settings'));
        $am[] = array('title' => 'admin', 'items' => array(
            'logs' => array('title' => \cb\t('Logs'), 'url' => 'admin/logs', 'ajax' => false)
        ));
      }
    }
    $this->datamodel['adminMenu'] = $am;
    $this->setMainTemplate('admin-menu');
    return \system\Component::RESPONSE_TYPE_READ;
  }
  
  public function runLoginControl() {
    $this->setMainTemplate('login-control');
    return \system\Component::RESPONSE_TYPE_READ;
  }
}