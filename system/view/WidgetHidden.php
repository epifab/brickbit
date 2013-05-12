<?php
namespace system\view;

class WidgetHidden implements WidgetInterface {
	
	public function render(array $input) {
		$attributes = \cb\array_item('attributes', $input, array('default' => array(), 'type' => 'array'));

		$input = array(
			'type' => 'hidden',
			'name' => \cb\array_item('name', $input, array('required' => true)),
			'value' => \cb\array_item('name', $input, array('required' => true)),
			'class' => 'de-input hidden' . \cb\array_item('class', $attributes, array('default' => '', 'prefix' => ' '))
		) + $attributes;
		
		return '<input' . \cb\xml_arguments($input) . ' />';
	}

	public function fetch($value, array $input) {
		return \cb\array_item('value', $input, array('required' => true));
	}
}
?>