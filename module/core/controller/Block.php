<?php
namespace module\core\controller;

use \system\Main;

class Block extends \system\Component {
  
  public function runMainMenu() {
    $mm = array();
    
    $table = \system\model2\Table::loadTable('node');
    $table->import(
      'id', 'type', 'url', 'text.title'
    );
    $table->addFilters(
      $table->filter('type', 'page'),
      $table->filter('text.title', null, 'NOT_NULL')
    );
    \module\core\model\RecordMode::addReadModeFilters($table, \system\utils\Login::getLoggedUser());
    
    $rs = $table->select();
    foreach ($rs as $r) {
      $mm[$r->id] = array(
        'id' => $r->id,
        'url' => $r->url,
        'title' => $r->text->title
      );
    }
    $this->datamodel['mainMenu'] = $mm;
    $this->setMainTemplate('main-menu');
    return \system\RESPONSE_TYPE_READ;
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
        $am[] = array('url' => Main::getUrl('user/list'), 'title' => \cb\t('users'), 'ajax' => false);
        $am[] = array('url' => Main::getUrl('system/settings'), 'title' => \cb\t('settings'));
        $am[] = array('title' => 'admin', 'items' => array(
          'logs' => array('title' => \cb\t('Logs'), 'url' => Main::getUrl('admin/logs'), 'ajax' => false)
        ));
      }
    }
    $this->datamodel['adminMenu'] = $am;
    $this->setMainTemplate('admin-menu');
    return \system\RESPONSE_TYPE_READ;
  }
  
  public function runLoginControl() {
    $this->setMainTemplate('login-control');
    return \system\RESPONSE_TYPE_READ;
  }
}