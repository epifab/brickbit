<?php
namespace system\view;

class Widget {
	public static $widgets = array();
	
	private static function getWidgetsMap() {
		static $map = null;
		if (\is_null($map)) {
			if (\config\settings()->CORE_CACHE) {
				$map = \system\Utils::get('system-widgets-map', null);
				if (!\is_null($map)) {
					return $map;
				}
			}
			$map = array();
			
			// default overridable values
			$map['hidden'] = '\\system\\view\\WidgetHidden';
			$map['textbox'] = '\\system\\view\\WidgetTextBox';
			$map['textarea'] = '\\system\\view\\WidgetTextarea';
			$map['selectbox'] = '\\system\\view\\WidgetSelectbox';
			$map['radiobutton'] = '\\system\\view\\WidgetRadiobutton';
			$map['radiobuttons'] = '\\system\\view\\WidgetRadiobuttons';
			$map['checkbox'] = '\\system\\view\\WidgetCheckbox';
			$map['checkboxes'] = '\\system\\view\\WidgetCheckboxes';
			
			$conf = \system\Main::raiseEvent('widgetsMap');

			foreach ($conf as $m) {
				if (\is_array($m)) {
					foreach ($m as $type => $class) {
						$map[$type] = $class;
					}
				}
			}
			if (\config\settings()->CORE_CACHE) {
				\system\Utils::set('system-widgets-map', $map);
			}
		}
		return $map;
	}
	
	/**
	 * Retrieves the widget class
	 * @param string $name Name of the widget
	 * @return \system\view\WidgetInterface
	 * @throws \system\InternalErrorException 
	 */
	public static function getWidget($name) {
		if (isset(self::$widgets[$name])) {
			return self::$widgets[$name];
		}

		$wmap = self::getWidgetsMap();
		
		if (!\array_key_exists($name, $wmap)) {
			throw new \system\InternalErrorException('Unknown widget <em>@name</em>', array('@name' => $name));
		}
		$widgetClass = $wmap[$name];
		
		self::$widgets[$name] = new $widgetClass();
		
		return self::$widgets[$name];
	}
}