<?php
namespace system\view;

class WidgetTextbox implements WidgetInterface {
  
  public function render(array $input) {
    $attributes = \cb\array_item('attributes', $input, array('default' => array(), 'type' => 'array'));

    $args = array(
      'type' => \cb\array_item('type', $attributes, array('options' => array('text', 'password'), 'default' => 'text')),
      'id' => \cb\array_item('id', $input, array('required' => true)),
      'name' => $input['name'],
      'value' => $input['state'],
    ) + $attributes + array('size' => 30);
    
    return '<input' . \cb\xml_arguments($args) . ' />';
  }

  public function fetch($value, array $input) {
    return $value;
  }
}
