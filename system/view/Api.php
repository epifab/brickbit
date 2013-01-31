<?php
namespace system\view;

class Api {
	/**
	 * @var \system\view\Api
	 */
	private static $instance;
	
	private $api = array();
	
	public static function getInstance() {
		if (\is_null(self::$instance)) {
			self::$instance = new self();
		}
		return self::$instance;
	}
	
	private function __construct() { }
	
	public function __call($method, $args) {
		if (\array_key_exists($method, $this->api)) {
			return \call_user_func_array($this->api[$method], $args);
		} else {
			throw new \system\InternalErrorException(\t('Template API <em>@name</em> not found.', array('@name' => $method)));
		}
	}
}
?>