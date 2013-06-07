<?php
namespace system\utils;

class Cache {
	private static $cache = array();
	
	public static function __callStatic($hook, $arguments) {
		if (!\array_key_exists($hook, self::$cache)) {
			self::$cache[$hook] = array();
			$x = \system\Main::raiseEvent($hook);
			foreach ($x as $y) {
				if (\is_array($y)) {
					self::$cache[$hook] = \array_merge_recursive(self::$cache[$hook], $y);
				}
			}
		}
		return self::$cache[$hook];
	}
}
