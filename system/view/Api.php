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
		static $viewClasses = null;
		if (\is_null($viewClasses)) {
			$viewClasses = \system\Main::getViewClasses();
		}
		foreach ($viewClasses as $c) {
			if (\method_exists($method, $c)) {
				return array($c, $method);
			}
		}
		return null;
	}
	
	public function __call($method, $args) {
		$api = $this->getApi($method);
		if ($api) {
			\call_user_func($api, $args);
		} else {
			throw new \system\InternalErrorException(\system\Lang::translate('Template API <em>@name</em> not found.', array('@name' => $method)));
		}
	}
	
	public function open($callback, $args=array()) {
		if ($callback == "open" || $callback == "close") {
			throw new \system\InternalErrorException(\t('"open" and "close" APIs are not valid callback for the open statement.'));
		}
		\array_push($this->blocks, array($callback, $args));
		\ob_start();
	}
	
	public function close() {
		if (empty($this->blocks)) {
			throw new \system\InternalErrorException(\t('Syntax error. No block has been open.'));
		}
		list($callback, $args) = \array_pop($this->blocks);
		$content = \ob_get_clean();
		return \call_user_func(array($this, $callback), $content, $args);
	}
	
	public function path($url) {
		return \config\settings()->BASE_DIR_ABS . $url;
	}
	
	public function module_path($module, $url) {
		return \system\logic\Module::getAbsPath($module) . $url;
	}
	
	public function theme_path($url) {
		return \system\Theme::getAbsThemePath() . $url;
	}
	
	public function load($path, $args=array()) {
		$tpl = new \system\view\Template($path, $args + \system\view\Template::current()->getVars());
		$tpl->render();
	}
	
	public function region($region) {
		$vars = \system\view\Template::current()->getVars();

		if (\array_key_exists($region, $vars['system']['templates']['regions'])) {
			\asort($vars['system']['templates']['regions'][$region]);
			foreach ($vars['system']['templates']['regions'][$region] as $templates) {
				foreach ($templates as $tpl) {
					$this->load($tpl);
				}
			}
		}
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