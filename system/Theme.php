<?php
namespace system;

class Theme {
	private static $theme;
	
//	/**
//	 * @var \system\ThemeInterface Theme object
//	 */
//	private static $themeObject;
//	
//	/**
//	 * @return \system\ThemeInterface Theme object (may return null if the class misses)
//	 */
//	private static function getThemeObject() {
//		if (empty(self::$themeObject)) {
//			$className = '\\theme\\' . self::getTheme() . '\\Theme';
//			if (\class_exists($className) && \in_array('\\system\\ThemeInterface', \class_implements($className))) {
//				self::$themeObject = new $className();
//			}
//		}
//		return self::$themeObject;
//	}
	
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
			throw new \system\exceptions\InternalError('Theme <em>@name</em> not found.', array('@name' => $theme));
		}
	}
	
	public static function getPath($subfolder=null) {
		return \config\settings()->BASE_DIR . 'theme/' . self::getTheme() . '/' . (empty($subfolder) ? '' : $subfolder . '/');
	}
	
	public static function getAbsPath($subfolder=null) {
		return \config\settings()->BASE_DIR_ABS . 'theme/' . self::getTheme() . '/' . (empty($subfolder) ? '' : $subfolder . '/');
	}
	
	public static function onRun(\system\Component $component) {
//		$obj = self::getThemeObject();
//		if (!empty($obj)) {
//			$obj->init($component);
//		}
		$cname = '\theme\\' . self::getTheme() . '\\Theme';
		if (\class_exists($cname) && \is_callable($cname . '::onRun')) {
			\call_user_func($cname . '::onRun', $component);
		}
	}
}
