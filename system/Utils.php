<?php
namespace system;

class Utils {
	private static $javascript = "";
	
	public static function get($name, $default=null) {
		if (file_exists("temp/php_vars/" . $name . ".var")) {
			$fp = fopen("temp/php_vars/" . $name . ".var", "r");
			$content = "";
			while ($s = fread($fp, 4096)) {
				$content .= $s;
			}
			fclose($fp);
			return unserialize($content);
		}
		return $default;
	}

	public static function set($name, $value) {
		$content = serialize($value);

		$fp = fopen("temp/php_vars/" . $name . ".var", "w");
		fwrite($fp, $content);
		fclose($fp);
	}

	
	public static function php2js($args) {
		if (empty($args)) {
			return '{}';
		}
		$jsVars = "{";
		foreach ($args as $k => $v) {
			!isset($first) ? $first = true : $jsVars .= ",";
			$jsVars .= "'" . $k . "': ";
			if (\is_array($v)) {
				$jsVars .= Utils::php2js($v);
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
	
	public static function addJsCode($content, \Smarty_Internal_Template &$smarty) {
		Utils::$javascript .= "\n" . $content . "\n";
	}
	
	public static function getJsCode() {
		return Utils::$javascript;
	}
}
?>