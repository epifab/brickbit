<?php
namespace system\view;

class WidgetTextbox implements WidgetInterface {
	
	public function render(array $input) {
		$attributes = \cb\array_item('attributes', $input, array('default' => array(), 'type' => 'array'));

		if (!isset($input['attributes'])) {
			$input['attributes'] = array();
		}
		
		$args = array(
			'type' => \cb\array_item('type', $attributes, array('options' => array('text', 'password'), 'default' => 'text')),
			'id' => \cb\array_item('id', $input, array('required' => true)),
			'name' => $input['name'],
			'value' => $input['value'],
			'class' => 'de-input textbox' . \cb\array_item('class', $input['attributes'], array('default' => '', 'prefix' => ' '))
		) + $attributes + array('size' => 30);
		
		return '<input' . \cb\xml_arguments($args) . ' />';
	}

	public function fetch($value, array $input) {
		return $value;
	}
}
