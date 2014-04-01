<?php
namespace module\ciderbit;

use \system\Component;
use \system\model2\Table;
use \system\utils\Login;
use \module\crud\RecordMode;

class MenuController extends Component {
  public static function accessMainMenu($urlArgs, $user) {
    return true;
  }
  
  public function runMainMenu() {
    $mm = array();
    
    $table = Table::loadTable('node');
    $table->import(
      'id', 'type', 'url', 'text.title'
    );
    $table->addFilters(
      $table->filter('type', 'page'),
      $table->filter('text.title', null, 'NOT_NULL')
    );
    RecordMode::addReadModeFilters($table, Login::getLoggedUser());
    
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
}
