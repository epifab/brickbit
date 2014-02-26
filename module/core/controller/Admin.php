<?php
namespace module\core\controller;

class Admin extends \system\Component {
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
    \system\utils\Utils::resetLogs();
    return $this->runLogs();
  }
  
  public function runLogs() {
    $this->setPageTitle('Logs');
    
    $this->setMainTemplate('logs');

    $rsb = self::rsb();
    $logs = $rsb->select();
    $this->datamodel['logs'] = $logs;
    
    return \system\Component::RESPONSE_TYPE_READ;
  }
  
  public function runLogDetails() {
    $this->setPageTitle(\cb\t('Log #@id', array('@id' => $this->getUrlArg(0))));
    
    $this->setMainTemplate('log');

    $rsb = self::rsb();
    $log = $rsb->selectFirstBy(array('id' => $this->getUrlArg(0)));
    if (empty($log)) {
      throw new \system\exceptions\PageNotFound();
    }
    $this->datamodel['log'] = $log;
    
    return \system\Component::RESPONSE_TYPE_READ;
  }
  
  public function runLogsByKey() {
    $this->setMainTemplate('logs');
    
    $rsb = self::rsb();
    $logs = $rsb->selectBy(array('code' => $this->getUrlArg(0)));
    $this->datamodel['logs'] = $logs;
    
    return \system\Component::RESPONSE_TYPE_READ;
  }
  
  public function runLogsByType() {
    $type = 0;
    switch ($this->getUrlArg(0)) {
      case "error":
        $type = \system\utils\Utils::LOG_ERROR;
        break;
      case "warning":
        $type = \system\utils\Utils::LOG_WARNING;
        break;
      case "info":
        $type = \system\utils\Utils::LOG_INFO;
        break;
      case "debug":
        $type = \system\utils\Utils::LOG_DEBUG;
        break;
      default:
        return $this->runLogs();
    }
    $this->setMainTemplate('logs');
    
    $rsb = self::rsb();
    $logs = $rsb->selectBy(array('type' => $type));
    $this->datamodel['logs'] = $logs;
    
    return \system\Component::RESPONSE_TYPE_READ;
  }
  
  private static function rsb() {
    $pageSize = 30;
    
    $rsb = new \system\model\RecordsetBuilder('log');
    $rsb->usingAll();
    $pages = $rsb->countPages($pageSize);
    $page = isset($_REQUEST['page']) && $_REQUEST['page'] > 0 && $_REQUEST['page'] < $pages
      ? intval($_REQUEST['page'])
      : 0;
    $rsb->setLimit(new \system\model\LimitClause($pageSize, ($pageSize * $page)));
    $rsb->setSort(new \system\model\SortClause($rsb->date_time_request, 'DESC'));
    return $rsb;
  }
}