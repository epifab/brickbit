<?php
namespace module\admin;

use \system\Component;
use \system\Main;
use \system\utils\Lang;
use \system\utils\Login;

class AdminController extends Component {
  public static function accessAdminMenu($urlArgs, $user) {
    // Admin menu will be shown for not logged users as well
    return true;
  }
  
  public static function accessFlushCache($urlArgs, $user) {
    return $user->superuser;
  }
  
  public function runAdminMenu() {
    $am = array();
    if (Login::isSuperuser()) {
      $am[] = array('url' => Main::getUrl('user/list'), 'title' => Lang::translate('users'), 'ajax' => false);
      $am[] = array('url' => Main::getUrl('system/settings'), 'title' => Lang::translate('settings'));
      $am[] = array('title' => Lang::translate('admin'), 'items' => array(
        'logs' => array('title' => Lang::translate('Logs'), 'url' => Main::getUrl('admin/logs'), 'ajax' => false),
        'cache' => array('title' => Lang::translate('Flush cache'), 'url' => Main::getUrl('admin/cache/flush'), 'ajax' => true)
      ));
    }
    $this->datamodel['adminMenu'] = $am;
    $this->setMainTemplate('admin-menu');
    return \system\RESPONSE_TYPE_READ;
  }
  
  public function runFlushCache() {
    Main::flushCache();
    $this->setMainTemplate('notify');
    $this->setPageTitle(Lang::translate('Cache cleared'));
    $this->datamodel['message'] = array(
      'body' => Lang::translate('Cache have been flushed'),
    );
    return \system\RESPONSE_TYPE_NOTIFY;
  }
}