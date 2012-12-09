<?php
namespace system;

class Log {
	private static $logs = "";
	
	public static function saveLog(\system\logic\Component $component, $output) {
		$builder = new \system\model\RecordsetBuilder('log');
		$builder->using("*");
		
//		$rs = $builder->newRecordset();
//		$rs->user_id = \system\Login::getLoggedUserId();
//		$rs->url = $url;
//		$rs->module = $component->getModule();
//		$rs->component = $component->getName();
//		$rs->action = $component->getAction();
//		$rs->date_time_request = $component->getRequestTime();
//		$rs->execution_time = $component->getExecutionTime();
//		$rs->body = self::$logs;
//		$rs->output = $output;
//		$rs->ip_address = \system\HTMLHelpers::getIpAddress();
//		$rs->create();
//		return $rs->getProg("id");
	}
	
	public static function add($log) {
		self::$logs = (empty(self::$logs) ? "" : "\n") . $log;
	}
	
	public static function get() {
		return self::$logs;
	}
}
?>