<?php
namespace system\view;

class WidgetCheckboxes implements WidgetInterface {
	
	public function render(array $input) {
		// checkbox widget used to render each checkbox
		$checkboxWidget = \system\view\Widget::getWidget('checkbox');
		
		// checkboxes options
		$options = \cb\array_item('options', $input, array('required' => true, 'type' => 'array'));
		
		// list of active checkboxes (option keys)
		$checked = \cb\array_item('checked', $input, array('default' => array(), 'type' => 'array'));
		
		// input optional attributes
		$attributes = \cb\array_item('attributes', $input, array('default' => array(), 'type' => 'array'));
		
		// name and id (they need to be changed while rendering each checkbox)
		$baseName = \cb\array_item('name', $input, array('required' => true));
		$baseId = \cb\array_item('id', $input, array('required' => true));
		
		$output = 
			'<div'
			. ' class="de-group-input-wrapper checkboxes"' 
			. ' id="' . \cb\text_plain($baseId) . '-wrapper">';
		
		// checkbox elements
		foreach ($options as $k => $v) {
			$args = array(
				'id' => $baseId . '-option-' . \cb\text_plain($k),
				'name' => $baseName . '[' . \cb\text_plain($k) . ']',
				'value' => $k,
				'label' => $v, // defining a label
				'checked' => \in_array($k, $checked),
				'attributes' => $attributes
			);
			$output .= $checkboxWidget->render($args);
		}
		
		return $output . '</div>';
	}

	public function fetch($value, array $input) {
		return $value;
	}
}
?>