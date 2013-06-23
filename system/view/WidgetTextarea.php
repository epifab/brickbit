<?php
namespace system\view;

class WidgetTextarea implements WidgetInterface {
	
	public function render(array $input) {
		if (!isset($input['attributes'])) {
			$input['attributes'] = array();
		}

		$args = array(
			'id' => \cb\array_item('id', $input, array('required' => true)),
			'name' => $input['name'],
			'class' => 'de-input textarea' . \cb\array_item('class', $input['attributes'], array('default' => '', 'prefix' => ' ')),
			'rows' => isset($input['attributes']['rows']) ? $input['attributes']['rows'] : 10,
			'cols' => isset($input['attributes']['cols']) ? $input['attributes']['cols'] : 70,
		);
		
		return '<textarea' . \cb\xml_arguments($args) . '>' 
			. \cb\plaintext($input['value'])
			. '</textarea>';
	}

	public function fetch($value, array $input) {
		return $value;
	}
}
