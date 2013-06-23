<?php
namespace module\core\components;

class Admin extends \system\Component {
	public static function accessLogsReset($urlArgs, $request, $user) {
		return $user && $user->superuser;
	}
	
	public static function accessLogs($urlArgs, $request, $user) {
		return $user && $user->superuser;
	}
	
	public static function accessLogsKey($urlArgs, $request, $user) {
		\system\utils\Utils::log('test', 'This is a test log', \system\utils\Utils::LOG_ERROR);
		return $user && $user->superuser;
	}
	
	public static function accessLogsType($urlArgs, $request, $user) {
		\system\utils\Utils::log('test', 'This is a test log', \system\utils\Utils::LOG_ERROR);
		return $user && $user->superuser;
	}
	
	public function runLogsReset() {
		\system\utils\Utils::resetLogs();
		return $this->runLogs();
	}
	
	public function runLogs() {
		$this->setMainTemplate('logs');

		$rsb = self::rsb();
		$logs = $rsb->select();
		$this->setData('logs', $logs);
		
		return \system\Component::RESPONSE_TYPE_READ;
	}
	
	public function runLogsByKey() {
		$this->setMainTemplate('logs');
		
		$rsb = self::rsb();
		$logs = $rsb->selectBy(array('code' => $this->getUrlArg(0)));
		$this->setData('logs', $logs);
		
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
		$this->setData('logs', $logs);
		
		return \system\Component::RESPONSE_TYPE_READ;
	}
	
	private static function rsb() {
		$pageSize = 30;
		
		$rsb = new \system\model\RecordsetBuilder('log');
		$rsb->using('*');
		$pages = $rsb->countPages($pageSize);
		$page = isset($_REQUEST['page']) && $_REQUEST['page'] > 0 && $_REQUEST['page'] < $pages
			? intval($_REQUEST['page'])
			: 0;
		$rsb->setLimit(new \system\model\LimitClause($pageSize, ($pageSize * $page)));
		$rsb->setSort(new \system\model\SortClause($rsb->date_time_request, 'DESC'));
		return $rsb;
	}
}
?>