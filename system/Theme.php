<?php
namespace system;

class Theme {
	private static $theme;
	
	public static function getTheme() {
		if (\is_null(self::$theme)) {
			self::setTheme(\config\settings()->DEFAULT_THEME);
		}
		return self::$theme;
	}
	
	public static function setTheme($theme) {
		if (\in_array($theme, \config\settings()->THEMES)) {
			self::$theme = $theme;
		} else {
			throw new \system\InternalErrorException('Theme <em>@name</em> not found.', array('@name' => $theme));
		}
	}
	
	public static function getAbsThemePath($subfolder=null) {
		return \config\settings()->BASE_DIR . "theme/" . self::getTheme() . "/" . (empty($subfolder) ? "" : $subfolder . "/");
	}
	
	public static function getThemePath($subfolder=null) {
		return \config\settings()->BASE_DIR . "theme/" . self::getTheme() . "/" . (empty($subfolder) ? "" : $subfolder . "/");
	}
}
?>