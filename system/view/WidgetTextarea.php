<?php
namespace system\view;

class WidgetTextarea implements WidgetInterface {
  
  public function render(array $input) {
    $attributes = \cb\array_item('attributes', $input, array('default' => array(), 'type' => 'array'));

    $args = array(
      'id' => \cb\array_item('id', $input, array('required' => true)),
      'name' => $input['name'],
      'rows' => isset($attributes['rows']) ? $attributes['rows'] : 10,
      'cols' => isset($attributes['cols']) ? $attributes['cols'] : 70,
    ) + $attributes;
    
    return '<textarea' . \cb\xml_arguments($args) . '>' 
      . \cb\plaintext($input['state'])
      . '</textarea>';
  }

  public function fetch($value, array $input) {
    return $value;
  }
}
