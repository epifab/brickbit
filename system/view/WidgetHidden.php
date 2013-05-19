<?php
namespace system\view;

class WidgetHidden implements WidgetInterface {
	
	public function render(array $input) {
		$attributes = \cb\array_item('attributes', $input, array('default' => array(), 'type' => 'array'));

		$args = array(
			'type' => 'hidden',
			'name' => $input['name'],
			'value' => $input['value'],
			'class' => 'de-input hidden' . \cb\array_item('class', $attributes, array('default' => '', 'prefix' => ' '))
		) + $attributes;
		
		return '<input' . \cb\xml_arguments($args) . ' />';
	}

	public function fetch($value, array $input) {
		return $input['value'];
	}
}
?>