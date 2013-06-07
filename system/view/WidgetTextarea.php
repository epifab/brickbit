<?php
namespace system\view;

class WidgetTextarea implements WidgetInterface {
	
	public function render(array $input) {
		$attributes = \cb\array_item('attributes', $input, array('default' => array(), 'type' => 'array'));

		$args = array(
			'id' => \cb\array_item('id', $input, array('required' => true)),
			'name' => $input['name'],
			'class' => 'de-input textarea' . \cb\array_item('class', $input, array('default' => '', 'prefix' => ' '))
		) + $attributes + array('rows' => 10, 'cols' => 70);
		
		return '<textarea' . \cb\xml_arguments($args) . '>' 
			. \cb\plaintext($input['value'])
			. '</textarea>';
	}

	public function fetch($value, array $input) {
		return $value;
	}
}
