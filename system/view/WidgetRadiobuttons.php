<?php
namespace system\view;

class WidgetRadiobuttons implements WidgetInterface {
	
	public function render(array $input) {
		// radio widget used to render each radio
		$radioWidget = \system\view\Widget::getWidget('radiobutton');
		
		// radio option
		$options = \cb\array_item('options', $input, array('required' => true, 'type' => 'array'));
		
		// list of active options (option keys)
		$checked = \cb\array_item('checked', $input, array('default' => array(), 'type' => 'array'));
		
		// input optional attributes
		$attributes = \cb\array_item('attributes', $input, array('default' => array(), 'type' => 'array'));
		
		// name and id (the id needs to be changed while rendering each radio)
		$baseName = \cb\array_item('name', $input, array('required' => true));
		$baseId = \cb\array_item('id', $input, array('required' => true));
		
		$output = 
			'<div'
			. ' class="de-group-input-wrapper radiobuttons"' 
			. ' id="' . \cb\text_plain($baseId) . '-wrapper">';
		
		// radio elements
		foreach ($options as $k => $v) {
			$args = array(
				'id' => $baseId . '-option-' . \cb\text_plain($k),
				'name' => $baseName,
				'value' => $k,
				'label' => $v, // defining a label
				'checked' => \in_array($k, $checked),
				'attributes' => $attributes
			);
			$output .= $radioWidget->render($args);
		}
		
		return $output . '</div>';
	}

	public function fetch($value, array $input) {
		return $value;
	}
}
?>