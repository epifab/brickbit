<?php
namespace system;

class Utils {
	private static $javascript = "";
	
	public static function get($name, $default=null) {
		if (file_exists("config/vars/" . $name . ".var")) {
			$fp = fopen("config/vars/" . $name . ".var", "r");
			$content = "";
			while ($s = fread($fp, 4096)) {
				$content .= $s;
			}
			fclose($fp);
			return unserialize($content);
		} else {
			return $default;
		}
	}

	public static function set($name, $value) {
		$content = serialize($value);

		$fp = fopen("config/vars/" . $name . ".var", "w");
		fwrite($fp, $content);
		fclose($fp);
	}

	
	public static function php2Js($args) {
		if (empty($args)) {
			return '{}';
		}
		$jsVars = "{";
		foreach ($args as $k => $v) {
			!isset($first) ? $first = true : $jsVars .= ",";
			$jsVars .= "'" . $k . "': ";
			if (\is_array($v)) {
				$jsVars .= self::php2Js($v);
			} else {
				if (is_bool($v)) {
					$jsVars .= $v ? "true" : "false";
				} else if (is_integer($v)) {
					$jsVars .= $v;
				} else if (substr(trim($v),0,8) == "function") {
					$jsVars .= $v;
				} else {
					$jsVars .= "'" . addslashes($v) . "'";
				}
			}
		}
		return $jsVars . "}";
	}
	
	public static function addUrlArgs($url, $args) {
		$first = !\strpos($url, '?', true);
		foreach ($args as $k => $v) {
			if ($first) {
				$first = false;
				$url .= "?";
			} else {
				$url .= "&amp;";
			}
			$url .= urlencode($k) . '=' . urlencode($v);
		}
		return $url;
	}
	
	public static function escape($x, $char) {
		$str = "";
		$escape = false;
		for ($i = 0; $i < \strlen($i); $i++) {
			if ($x{$i} == $char) {
				$str .= (!$escape ? "\\" : "");
				$escape = false;
			} else if ($x{$i} == "\\") {
				$escape = !$escape;
			} else {
				$escape = false;
			}
		}
	}
	
	public static function arg2Input(&$results, $prefix, $value) {
		if (is_object($value)) {
			// ?
		} else if (!is_array($value)) {
			$results[$prefix] = $value;
		} else {
			foreach ($value as $k => $v) {
				arg2Input($results, $prefix . "[" . $k . "]", $v);
			}
		}
	}
	
	public static function getParam($needle, $haystack, $options) {
		if (!\array_key_exists($needle, $haystack)) {
			if (\array_key_exists('required', $options) && (bool)$options['required']) {
				throw new InternalErrorException(Lang::translate('Required arg @name.', array('@name' => $needle)));
			} else if (\array_key_exists('default', $options)) {
				return $options['default'];
			} else {
				return null;
			}
		} 
		else {
			if (\array_key_exists('options', $options) && \is_array($options['options'])) {
				if (!\in_array($haystack[$needle], $options['options'])) {
					throw new InternalErrorException(Lang::translate('Invalid param @name', array('@name' => $needle)));
				}
			}
			if (\is_string($haystack[$needle])) {
				return
					(\array_key_exists('prefix', $options) ? $options['prefix'] : '')
					. $haystack[$needle]
					. (\array_key_exists('suffix', $options) ? $options['suffix'] : '');
			} else {
				return $haystack[$needle];
			}
		}
	}
	
	public static function addJsCode($content, \Smarty_Internal_Template &$smarty) {
		Utils::$javascript .= "\n" . $content . "\n";
	}
	
	public static function getJsCode() {
		return Utils::$javascript;
	}
}
?>