<?php

namespace system\view;

class Api {

	/**
	 * @var \system\view\Api
	 */
	private static $instance;
	private $blocks = array();
	private $javascript = array();

	
	/**
	 * @return \system\view\Api
	 */
	public static function getInstance() {
		if (\is_null(self::$instance)) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	protected function getApi($method) {
		static $cache = array();
		static $viewClasses = null;
		if (!\array_key_exists($method, $cache)) {
			if (\is_null($viewClasses)) {
				$viewClasses = \system\Main::getViewClasses();
			}
			$cache[$method] = null;
			foreach ($viewClasses as $c) {
				if (\method_exists($c, $method)) {
					$cache[$method] = array($c, $method);
					break;
				}
			}
		}
		return $cache[$method];
	}
	
	public static function __callStatic($method, $args) {
		self::getInstance()->__call($method, $args);
	}

	public function __call($method, $args) {
		$api = $this->getApi($method);
		if (!\is_null($api)) {
			return \call_user_func_array($api, $args);
		} else {
			throw new \system\InternalErrorException(\system\Lang::translate('Template API <em>@name</em> not found.', array('@name' => $method)));
		}
	}

	public function open($callback, $args = array()) {
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
		$x = \call_user_func(array($this, $callback), $content, $args);
		if (!\is_null($x)) {
			echo $x;
		}
	}

	public function path($url) {
		return \config\settings()->BASE_DIR . $url;
	}

	public function module_path($module, $url) {
		return \system\logic\Module::getPath($module) . $url;
	}

	public function theme_path($url) {
		return \system\Theme::getThemePath() . $url;
	}

	public function lang_path($lang) {
		return \system\Lang::langPath($lang);
	}
	
	private static function params2input($key, $val, &$input, $prefix='') {
		if (\is_array($val)) {
			foreach ($val as $k1 => $v1) {
				self::params2input($k1, $v1, $input, (empty($prefix) ? $key : $prefix . '[' . $key . ']'));
			}
		}
		else {
			$input .= 
				'<input'
				. ' type="hidden"'
				. ' name="' . (empty($prefix) ? $key : $prefix . '[' . $key . ']') . '"'
				. ' value="' . \htmlentities($val) . '"/>';
		}
	}

	public function load($name, $url, $args = array()) {
		static $ids = array();
		
		$url = $this->path($url);
		
		$blockId = $name;
		if (!\array_key_exists($name, $ids)) {
			$ids[$name] = 1;
		} else {
			$ids[$name]++;
			$blockId .= '-' . $ids[$name];
		}

		$vars = \system\view\Template::current()->getVars();

		// system array is reserved
		// make sure it isn't overridden
		$args['system'] = array(
			'url' => $vars['system']['mainComponent']['url'],
			'requestType' => 'MAIN',
			'blockId' => $blockId
		);
		
		\ob_start();
		\system\Main::run($url, $args);
		$componentOut = \ob_get_clean();
		
		$args['system']['requestType'] = 'AJAX';
		
		$content =
			'<form'
			. ' action="' . $url . '"'
			. ' method="POST"' 
			. ' name="' . $blockId . '"'
			. ' class="system-block-form"'
			. ' id="system-block-form-' . $blockId . '">';
		
		foreach ($args as $key => $val) {
			self::params2input($key, $val, $content);
		}
		
		$content .=
			'</form>'
			. '<div class="system-block" id="' . $blockId . '">'
			. $componentOut
			. '</div>';
		
		echo $content;
	}
	
	public function import($path, $args = array()) {
		$a = $args + \system\view\Template::current()->getVars();
		$tpl = new \system\view\Template($path, $a);
		$tpl->render();
	}

	public function region($region) {
		$vars = \system\view\Template::current()->getVars();

		if (\array_key_exists($region, $vars['system']['templates']['regions'])) {
			\asort($vars['system']['templates']['regions'][$region]);
			foreach ($vars['system']['templates']['regions'][$region] as $templates) {
				foreach ($templates as $tpl) {
					$this->import($tpl);
				}
			}
		}
	}

	public function t($sentence, $args = null) {
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