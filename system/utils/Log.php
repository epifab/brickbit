<?php
namespace system\utils;

class Log {
	/**
	 * Create a log
	 * @param string $code
	 * @param string $body
	 * @param array $args
	 * @param int $level
	 * @return int Log id
	 */
	public static function create($code, $body, $args = array(), $level = \system\LOG_WARNING) {
		$builder = new \system\model\RecordsetBuilder('log');
		$builder->using("*");
		$rs = $builder->newRecordset();
		
		$rs->url = $_SERVER['REQUEST_URI'];
		$rs->code = $code;
		$rs->body = \cb\t($body, $args);
		$rs->level = $level;
		$rs->trace = \system\utils\Utils::backtraceInfo(\array_slice(\debug_backtrace(), 0, -1));
		$rs->date_time_request = \time();
		$rs->user_id = \system\utils\Login::getLoggedUserId();
		$rs->ip_address = \system\utils\HTMLHelpers::getIpAddress();
		
		$rs->create();
		return $rs->id;
	}
}
