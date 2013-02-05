<?php
namespace system\logic;

abstract class Module {
	public static function getPath($module, $subpath=null) {
		return "module/" . $module . "/" . (\is_null($subpath) ? "" : $subpath . "/");
	}
	
	public static function getAbsPath($module, $subpath=null) {
		return \config\settings()->BASE_DIR . self::getPath($module, $subpath);
	}
	
	public static function getNamespace($module, $subnamespace=null) {
		return '\module\\' . $module . '\\' . (is_null($subnamespace) ? '' : $subnamespace . '\\');
	}
}
?>