<?php
namespace system;

class System {
	public static function widgetsMap() {
		return array(
			'hidden' => '\\system\\view\\WidgetHidden',
			'textbox' => '\\system\\view\\WidgetTextBox',
			'textarea' => '\\system\\view\\WidgetTextarea',
			'selectbox' => '\\system\\view\\WidgetSelectbox',
			'radiobutton' => '\\system\\view\\WidgetRadiobutton',
			'radiobuttons' => '\\system\\view\\WidgetRadiobuttons',
			'checkbox' => '\\system\\view\\WidgetCheckbox',
			'checkboxes' => '\\system\\view\\WidgetCheckboxes'
		);
	}
}
?>