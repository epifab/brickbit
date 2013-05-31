<?php
namespace system\view;

class WidgetCheckbox implements WidgetInterface {
	
	public function render(array $input) {
		// input optional attributes
		$attributes = \cb\array_item('attributes', $input, array('default' => array(), 'type' => 'array'));

		// input id
		$id = \cb\array_item('id', $input, array('required' => true));
		
		$args = array(
			'id' => $id,
			'type' => 'checkbox',
			'name' => $input['name'],
			'value' => $input['value'],
			'class' => 'de-input checkbox' . \cb\array_item('class', $input, array('default' => '', 'prefix' => ' '))
		) + $attributes;
		
		if (!empty($input['checked'])) {
			$args['checked'] = 'checked';
		}
		
		return
			'<input' . \cb\xml_arguments($args) . ' />'
			. (isset($input['label']) ? ' <label for="' . $id . '">' . \cb\plaintext($input['label']) . '</label>' : '');
	}

	public function fetch($value, array $input) {
		return $value;
	}
}
?>