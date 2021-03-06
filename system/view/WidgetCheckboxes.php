<?php
namespace system\view;

class WidgetCheckboxes implements WidgetInterface {
  
  public function render(array $input) {
    // checkbox widget used to render each checkbox
    $checkboxWidget = \system\view\Widget::getWidget('checkbox');
    
    // checkboxes options
    $options = \cb\array_item('options', $input, array('required' => true, 'type' => 'array'));
    
    // list of active checkboxes (option keys)
    $input['state'] = (array)$input['state']; // just to make sure
    
    // input optional attributes
    $attributes = \cb\array_item('attributes', $input, array('default' => array(), 'type' => 'array'));
    
    // name and id (they need to be changed while rendering each checkbox)
    $baseId = \cb\array_item('id', $input, array('required' => true));
    
    $output = '<div class="checkboxes" id="' . \cb\plaintext($baseId) . '">';
    
    // checkbox elements
    foreach ($options as $k => $v) {
      $inp2 = array(
        'name' => $input['name'] . '[' . \cb\plaintext($k) . ']',
        'value' => '1',
        'id' => $baseId . '-option-' . \cb\plaintext($k),
        'label' => $v, // defining a label
        'state' => \in_array($k, $input['state']),
        'attributes' => $attributes
      );
      $output .= $checkboxWidget->render($inp2);
    }
    
    return $output . '</div>';
  }

  public function fetch($value, array $input) {
    $v = array();
    foreach ($value as $k => $v) {
      if (\in_array($k, $input['options'])) {
        $v[] = $k;
      }
    }
    return $v;
  }
}
