<?php
namespace system\view;

class WidgetRadiobuttons implements WidgetInterface {
  
  public function render(array $input) {
    // radio widget used to render each radio
    $radioWidget = \system\view\Widget::getWidget('radiobutton');
    
    // radio option
    $options = \cb\array_item('options', $input, array('required' => true, 'type' => 'array'));
    
    // list of active options (option keys)
    $input['state'] = (array)$input['state'];
    
    // input optional attributes
    $attributes = \cb\array_item('attributes', $input, array('default' => array(), 'type' => 'array'));
    
    // the id needs to be changed while rendering each radio
    $baseId = \cb\array_item('id', $input, array('required' => true));
    
    $output = '<div class="radios" id="' . \cb\plaintext($baseId) . '">';
    
    // radio elements
    foreach ($options as $k => $v) {
      $inp2 = array(
        'name' => $input['name'],
        'value' => $k,
        'id' => $baseId . '-option-' . \cb\plaintext($k),
        'label' => $v, // defining a label
        'state' => \in_array($k, $input['state']),
        'attributes' => $attributes
      );
      $output .= $radioWidget->render($inp2);
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
