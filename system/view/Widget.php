<?php
namespace system\view;

class Widget {
	public static $widgets = array();
	
	/**
	 * Retrieves the widget class
	 * @param string $name Name of the widget
	 * @return \system\view\WidgetInterface
	 * @throws systemerror$1Error 
	 */
	public static function getWidget($name) {
		if (isset(self::$widgets[$name])) {
			return self::$widgets[$name];
		}

		$wmap = \system\utils\Cache::widgetsMap();
		
		if (!\array_key_exists($name, $wmap)) {
			throw new \system\exceptions\InternalError('Unknown widget <em>@name</em>', array('@name' => $name));
		}
		$widgetClass = $wmap[$name];
		
		self::$widgets[$name] = new $widgetClass();
		
		return self::$widgets[$name];
	}
}
