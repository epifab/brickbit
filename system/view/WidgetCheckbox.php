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
      'name' => $input['name'],
      'value' => \cb\array_item('value', $input, array('default' => '1')),
    ) + $attributes;
    
    if (!empty($input['state'])) {
      $args['checked'] = 'checked';
    }
    
    return
      (isset($input['label']) ? ' <label class="checkbox-inline" for="' . $id . '">' : '')
      . '<input' . \cb\xml_arguments($args) . ' />'
      . (isset($input['label']) ? ' ' . \cb\plaintext($input['label']) . '</label>' : '');
  }

  public function fetch($value, array $input) {
    return $value;
  }
}
