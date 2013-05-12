<?php
namespace system\view;

class WidgetSelectbox implements WidgetInterface {
	
	public function render(array $input) {
		// select options
		$options = \cb\array_item('options', $input, array('required' => true, 'type' => 'array'));
		
		// list of selected items (option keys)
		$selected = \cb\array_item('selected', $input, array('default' => array(), 'type' => 'array'));
		
		// input optional attributes
		$attributes = \cb\array_item('attributes', $input, array('default' => array(), 'type' => 'array'));

		// select element attributes
		$args = array(
			'id' => \cb\array_item('id', $input, array('required' => true)),
			'name' => \cb\array_item('name', $input, array('required' => true)),
			'class' => 'de-input selectbox' . \cb\array_item('class', $attributes, array('default' => '', 'prefix' => ' '))
		) + $attributes;
		
		$output = 
			'<div'
			. ' class="de-input-wrapper selectbox"' 
			. ' id="' . \cb\text_plain($baseId) . '-wrapper">'
			. '<select' . \cb\xml_arguments($args) . '>';
		
		// option elements
		foreach ($options as $k => $v) {
			$output .= 
				'<option'
				. ' value="' . \cb\text_plain($k) . '"'
				. (\in_array($k, $selected) ? ' selected="selected"' : '')
				. '>' . \cb\text_plain($v) . '</option>';
		}
		
		return $output . '</select></div>';
	}

	public function fetch($value, array $input) {
		return $value;
	}
}
?>