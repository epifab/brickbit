<?php
namespace system\utils;

use system\error\InternalError;

class Utils {
	const LOG_ERROR = 1;
	const LOG_WARNING = 2;
	const LOG_INFO = 3;
	const LOG_DEBUG = 4;
	
	public static function log($key, $message, $type=self::LOG_INFO) {
		$logs = self::get('system-logs', array());
		$logsByKey = self::get('system-logs-by-key', array());
		$logsByType = self::get('system-logs-by-type', array());

		// because of the reverse array order
		// the first element key is the greatest one
		$index = 1 + \key($logs);

		$traceDesc = '<ol>';
		$trace = \debug_backtrace();
		$i = count($trace);
		foreach ($trace as $t) {
			$traceDesc .= '<li value="' . $i . '"><p><code>';

			$i--;

			if (array_key_exists('class', $t) && !empty($t['class'])) {
				$traceDesc .= $t['class'] . '->';
			}
			$traceDesc .= '<b>' . $t['function'] . '</b>(';

			$first = true;

			if (\array_key_exists("args", $t)) {
				foreach ($t['args'] as $arg) {
					$first ? $first = false : $traceDesc .= ', ';
					$traceDesc .= self::varDump($arg);
				}
			}
			$traceDesc .= '</code>)<br/> ' . @$t['file'] . ' ' . @$t['line'] . '</p></li>';
		}
		$traceDesc .= '</ol>';
		
		$logs[$index] = array(
			'id' => $index,
			'time' => \time(),
			'key' => $key,
			'message' => $message,
			'type' => $type,
			'trace' => $traceDesc
		);
		
		$logsByKey[$key][] = $index;
		$logsByType[$type][] = $index;
		
		\arsort($logs);
		\arsort($logsByKey[$key]);
		\arsort($logsByType[$type]);
		
		self::set('system-logs', $logs);
		self::set('system-logs-by-key', $logsByKey);
		self::set('system-logs-by-type', $logsByType);
	}
	
	public static function varDump($arg) {
		$msg = '';
		if (\is_array($arg)) {
			$msg .= 'array(';
			$first = true;
			foreach ($arg as $k => $v) {
				$first ? $first = false : $msg .= ", ";
				$msg .= self::varDump($k) . " => " . self::varDump($v);
			}
			$msg .= ')';
		} else if (\is_object($arg)) {
			$msg .= '[object ' . get_class($arg) . ']';
		} else if (\is_null($arg)) {
			$msg .= 'null';
		} else if (\is_string($arg)) {
			$msg .= '"' . $arg . '"';
		} else {
			$msg .= $arg;
		}
		return $msg;
	}
	
	public static function getLogs() {
		return self::get('system-logs', array());
	}
	
	public static function getLogsByKey($key) {
		$logs = self::get('system-logs', array());
		$logsByKey = self::get('system-logs-by-key', array());
		
		$return = array();
		
		if (isset($logsByKey[$key])) {
			foreach ($logsByKey[$key] as $index) {
				$return[] = $logs[$index];
			}
		}
		return $return;
	}
	
	public static function getLogsByType($type) {
		$logs = self::get('system-logs', array());
		$logsByType = self::get('system-logs-by-type', array());
		
		$return = array();
		
		if (isset($logsByType[$type])) {
			foreach ($logsByType[$type] as $index) {
				$return[] = $logs[$index];
			}
		}
		return $return;
	}
	
	public static function resetLogs() {
		self::set('system-logs', array());
		self::set('system-logs-by-key', array());
		self::set('system-logs-by-type', array());
	}
		
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
	
	public static function getSession($module, $key, $default) {
		if (!\array_key_exists($module, $_SESSION)) {
			$_SESSION[$module] = array();
		}
		return self::getParam($key, $_SESSION[$module], array('default' => $default));
	}
	
	public static function setSession($module, $key, $value) {
		$_SESSION[$module][$key] = $value;
	}
	
	public static function unsetSession($module, $key=null) {
		if (\is_null($key)) {
			unset($_SESSION[$module]);
		} else {
			unset($_SESSION[$module][$key]);
		}
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
				self::arg2Input($results, $prefix . "[" . $k . "]", $v);
			}
		}
	}
	
	public static function getParam($needle, $haystack, $options=array()) {
		if (!\array_key_exists($needle, $haystack)) {
			if (\array_key_exists('required', $options) && (bool)$options['required']) {
				throw new InternalError('Required arg @name.', array('@name' => $needle));
			} else if (\array_key_exists('default', $options)) {
				return $options['default'];
			} else {
				return null;
			}
		} 
		else {
			if (\array_key_exists('options', $options) && \is_array($options['options'])) {
				if (!\in_array($haystack[$needle], $options['options'])) {
					throw new InternalError('Invalid param @name', array('@name' => $needle));
				}
			}
			if (\is_null($haystack[$needle])) {
				return null;
			}
			else if (\is_string($haystack[$needle]) || \array_key_exists('prefix', $options) || \array_key_exists('suffix', $options)) {
				return
					(\array_key_exists('prefix', $options) ? $options['prefix'] : '')
					. $haystack[$needle]
					. (\array_key_exists('suffix', $options) ? $options['suffix'] : '');
			} else {
				return $haystack[$needle];
			}
		}
	}
}