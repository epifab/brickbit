<?php
namespace system\view;

class WidgetPassword extends WidgetTextbox {
  public function render(array $input) {
    // Do never send password
    $input['value'] = '';
    $input['attributes']['type'] = 'password';
    return parent::render($input);
  }
}