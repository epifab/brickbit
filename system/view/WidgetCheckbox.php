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
			'name' => \cb\array_item('name', $input, array('required' => true)),
			'value' => \cb\array_item('name', $input, array('required' => true)),
			'class' => 'de-input checkbox' . \cb\array_item('class', $attributes, array('default' => '', 'prefix' => ' '))
		) + $attributes;
		
		if (!empty($input['checked'])) {
			$args['checked'] = 'checked';
		}
		
		return
			'<div'
			. ' id="' . \cb\text_plain($id) . '-wrapper"'
			. ' class="de-input-wrapper checkbox">'
			. '<input' . \cb\xml_arguments($args) . ' />'
			. (isset($input['label']) ? ' <label for="' . $id . '">' . \cb\text_plain($input['label']) . '</label>' : '')
			. '</div>';
	}

	public function fetch($value, array $input) {
		return $value;
	}
}
?>