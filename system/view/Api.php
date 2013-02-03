<?php
namespace system\view;

class Api {
	/**
	 * @var \system\view\Api
	 */
	private static $instance;
	
	private $apiClasses = array();
	private $blocks = array();
	private $javascript = array();
	
	public static function getInstance() {
		if (\is_null(self::$instance)) {
			self::$instance = new self();
		}
		return self::$instance;
	}
	
	protected function getApi($method) {
		foreach ($this->apiClasses as $c) {
			if (\method_exists($method, $c)) {
				return array($c, $method);
			}
		}
		return null;
	}
	
	private function __construct() { }
	
	public function __call($method, $args) {
		$api = $this->getApi($method);
		if ($api) {
			\call_user_func($api, $args);
		} else {
			throw new \system\InternalErrorException(\t('Template API <em>@name</em> not found.', array('@name' => $method)));
		}
	}
	
	public function open($callback, $args=array()) {
		if ($callback == "open" || $callback == "close") {
			throw new \system\InternalErrorException(\t('"open" and "close" APIs are not valid callback for the open statement.'));
		}
		\array_push($this->blocks, array($callback, $args));
		\ob_start();
		
		$openCallback = $this->getApi($callback . 'Start');
		if ($openCallback) {
			\call_user_func($openCallback, $args);
		}
	}
	
	public function close() {
		if (empty($this->blocks)) {
			throw new \system\InternalErrorException(\t('Syntax error. No block has been open.'));
		}
		list($callback, $args) = \array_pop($this->blocks);
		$content = \ob_get_clean();
		\call_user_func(array($this, $callback), $content, $args);
	}
	
	public function modulePath($module, $url) {
		return \system\logic\Module::getPath($module) . $url;
	}
	
	public function themePath($url) {
		return \system\Theme::getThemePath() . $url;
	}
	
	public function t($sentence, $args=null) {
		return \system\Lang::translate($sentence, $args);
	}
	
	public function javascript($code) {
		$this->javascript .= "\n" . $code;
	}
	
	public function jss() {
		$jss = "";
		foreach ($this->javascript as $js) {
			$jss .= $js . "\n";
		}
		return $jss;
	}
}
?>