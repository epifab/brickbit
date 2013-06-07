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
		$this->setData('logs', \system\utils\Utils::getLogs());
		return \system\Component::RESPONSE_TYPE_READ;
	}
	
	public function runLogsByKey() {
		$this->setMainTemplate('logs');
		$this->setData('logs', \system\utils\Utils::getLogsByKey($this->getUrlArg(0)));
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
		$this->setData('logs', \system\utils\Utils::getLogsByType($type));
		return \system\Component::RESPONSE_TYPE_READ;
	}
}
?>