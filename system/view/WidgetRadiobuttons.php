<?php
namespace system\view;

class WidgetRadiobuttons implements WidgetInterface {
	
	public function render(array $input) {
		// radio widget used to render each radio
		$radioWidget = \system\view\Widget::getWidget('radiobutton');
		
		// radio option
		$options = \cb\array_item('options', $input, array('required' => true, 'type' => 'array'));
		
		// list of active options (option keys)
		$input['value'] = (array)$input['value'];
		
		// input optional attributes
		$attributes = \cb\array_item('attributes', $input, array('default' => array(), 'type' => 'array'));
		
		// the id needs to be changed while rendering each radio
		$baseId = \cb\array_item('id', $input, array('required' => true));
		
		$output = '<div class="radiobuttons" id="' . \cb\plaintext($baseId) . '">';
		
		// radio elements
		foreach ($options as $k => $v) {
			$inp2 = array(
				'name' => $input['name'],
				'value' => $k,
				'id' => $baseId . '-option-' . \cb\plaintext($k),
				'label' => $v, // defining a label
				'checked' => \in_array($k, $input['value']),
				'attributes' => $attributes
			);
			$output .= '<div class="radiobutton">' . $radioWidget->render($inp2) . '</div>';
		}
		
		return $output . '</div>';
	}

	public function fetch($value, array $input) {
		if (\in_array($value, $input['options'])) {
			return $value;
		}
		return null;
	}
}
?>