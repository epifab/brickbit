<?php
namespace module\admin;

use system\Component;
use system\exceptions\PageNotFound;
use system\exceptions\UnderDevelopment;
use system\model2\Table;

class LogController extends Component {
  public static function accessLogsReset($urlArgs, $user) {
    return $user && $user->superuser;
  }
  
  public static function accessLogs($urlArgs, $user) {
    return $user && $user->superuser;
  }
  
  public static function accessLogDetails($urlArgs, $user) {
    return $user && $user->superuser;
  }
  
  public static function accessLogsKey($urlArgs, $user) {
    return $user && $user->superuser;
  }
  
  public static function accessLogsType($urlArgs, $user) {
    return $user && $user->superuser;
  }
  
  public function runLogsReset() {
    throw new UnderDevelopment('Not yet implemented');
  }
  
  public function runLogs() {
    $this->setPageTitle('Logs');
    
    $this->setMainTemplate('logs');

    $table = self::getLogTable();
    $logs = $table->select();
    $this->datamodel['logs'] = $logs;
    
    return \system\RESPONSE_TYPE_READ;
  }
  
  public function runLogDetails() {
    $this->setPageTitle(\cb\t('Log #@id', array('@id' => $this->getUrlArg(0))));
    
    $this->setMainTemplate('log');

    $table = self::getLogTable();
    $log = $table->selectFirst($table->filter('id', $this->getUrlArg(0)));
    if (empty($log)) {
      throw new PageNotFound();
    }
    $this->datamodel['log'] = $log;
    
    return \system\RESPONSE_TYPE_READ;
  }
  
  public function runLogsByKey() {
    $this->setMainTemplate('logs');
    
    $table = self::getLogTable();
    $logs = $table->select($table->filter('code', $this->getUrlArg(0)));
    $this->datamodel['logs'] = $logs;
    
    return \system\RESPONSE_TYPE_READ;
  }
  
  public function runLogsByType() {
    $type = 0;
    switch ($this->getUrlArg(0)) {
      case "error":
        $type = \system\LOG_ERROR;
        break;
      case "warning":
        $type = \system\LOG_WARNING;
        break;
      case "info":
        $type = \system\LOG_INFO;
        break;
      case "debug":
        $type = \system\LOG_DEBUG;
        break;
      default:
        return $this->runLogs();
    }
    $this->setMainTemplate('logs');
    
    $table = self::getLogTable();
    $logs = $table->select($table->filter('type', $type));
    $this->datamodel['logs'] = $logs;
    
    return \system\RESPONSE_TYPE_READ;
  }
  
  private static function getLogTable() {
    $pageSize = 30;
    
    $table = Table::loadTable('log');
    $table->import('*', 'user.full_name');
    $pages = $table->countPages($pageSize);
    $page = isset($_REQUEST['page']) && $_REQUEST['page'] > 0 && $_REQUEST['page'] < $pages
      ? intval($_REQUEST['page'])
      : 0;
    $table->setLimit($table->limit($pageSize, ($pageSize * $page)));
    $table->addSorts($table->sort('date_time_request', 'DESC'));
    return $table;
  }
}