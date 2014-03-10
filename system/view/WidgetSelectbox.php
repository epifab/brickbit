<?php
namespace system\view;

class WidgetSelectbox implements WidgetInterface {
  
  public function render(array $input) {
    if (!isset($input['attributes'])) {
      $input['attributes'] = array();
    }
    
    // select options
    $options = \cb\array_item('options', $input, array('required' => true, 'type' => 'array'));
    
    // list of selected items (option keys)
    $input['state'] = (array)$input['state'];
    
    // input optional attributes
    $attributes = \cb\array_item('attributes', $input, array('default' => array(), 'type' => 'array'));

    // select element attributes
    $args = array(
      'id' => \cb\array_item('id', $input, array('required' => true)),
      'name' => $input['name'],
    ) + $attributes;
    
    $output = '<select' . \cb\xml_arguments($args) . '>';
    
    // option elements
    foreach ($options as $k => $v) {
      $output .= 
        '<option'
        . ' value="' . \cb\plaintext($k) . '"'
        . (\in_array($k, $input['state']) ? ' selected="selected"' : '')
        . '>' . \cb\plaintext($v) . '</option>';
    }
    
    return $output . '</select>';
  }

  public function fetch($value, array $input) {
    if (\array_key_exists($value, $input['options'])) {
      return $value;
    }
    return null;
  }
}
