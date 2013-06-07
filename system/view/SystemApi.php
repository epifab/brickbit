<?php
namespace system\view;

class SystemApi {
	private static $blocks = array();
	
	public static function open($callback, $args = array()) {
		$callback = 'block_' . $callback;
		\system\view\Api::__callStatic($callback, array(null, $args, true));
		\array_push(self::$blocks, array($callback, $args));
		\ob_start();
	}

	public static function close() {
		if (empty(self::$blocks)) {
			throw new \system\error\InternalError('Syntax error. No block has been open.');
		}
		list($callback, $args) = \array_pop(self::$blocks);
		$content = \ob_get_clean();
		$x = \system\view\Api::__callStatic($callback, array($content, $args, true));
		if (!\is_null($x)) {
			echo $x;
		}
	}

	public static function path($url) {
		return \config\settings()->BASE_DIR . $url;
	}

	public static function module_path($module, $url) {
		return \system\Module::getAbsPath($module) . $url;
	}

	public static function theme_path($url) {
		return \system\Theme::getThemePath() . $url;
	}

	public static function lang_path($lang) {
		return \system\utils\Lang::langPath($lang);
	}
	
	public static function element($name, $args) {
		$out = '<' . $name . ' ';
		foreach ($args as $key => $val) {
			$out .= $key . '="' . \cb\plaintext($val) . '" ';
		}
		return $out . '/>';
	}
	
	public static function open_element($name, $args) {
		$out = '<' . $name . ' ';
		foreach ($args as $key => $val) {
			$out .= $key . '="' . \cb\plaintext($val) . '" ';
		}
		return $out . '>';
	}
	
	public static function close_element($name) {
		return '</' . $name . '>';
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
	
	public static function load_block($name, $url, $args=array()) {
		$url = \system\view\Api::path($url);
		echo self::print_block($name, $url, null, $args);
	}

	private static function print_block($name, $url, $content, $args=array()) {
		static $ids = array();
		
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
			'requestType' => 'AJAX',
			'blockId' => $blockId
		);
		
		echo
			'<form'
			. ' action="' . $url . '"'
			. ' method="POST"' 
			. ' name="' . $blockId . '"'
			. ' class="system-block-form"'
			. ' id="system-block-form-' . $blockId . '">';
		
		foreach ($args as $key => $val) {
			self::params2input($key, $val, $out);
		}

		echo
			'</form>'
			. '<div class="system-block" id="' . $blockId . '">';
	
		if (\is_null($content)) {
			\system\Main::run($url, $args);
		} else {
			echo $content;
		}
		
		echo '</div>';
	}
	
	public static function block_block($content, $params, $open) {
		if (!$open) {
			$name = \cb\array_item('name', $params, array('required' => true));
			$url = \cb\array_item('url', $params, array('required' => true));
			$args = \cb\array_item('args', $params, array('default' => array()));
			echo self::print_block($name, $url, $content, $args);
		}
	}
	
	public static function import($name, $args = array()) {
		$a = $args + \system\view\Template::current()->getVars();
		$tpl = new \system\view\Template($name, $a);
		$tpl->render();
	}

	public static function region($region) {
		$vars = \system\view\Template::current()->getVars();

		if (\array_key_exists($region, $vars['system']['templates']['regions'])) {
			\asort($vars['system']['templates']['regions'][$region]);
			foreach ($vars['system']['templates']['regions'][$region] as $templates) {
				foreach ($templates as $tpl) {
					\system\view\Api::import($tpl);
				}
			}
		}
	}

	public static function t($sentence, $args = null) {
		return \system\utils\Lang::translate($sentence, $args);
	}
}
?>