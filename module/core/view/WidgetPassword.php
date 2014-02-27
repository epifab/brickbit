<?php
namespace module\core\view;

class WidgetPassword extends \system\view\WidgetTextbox {
  public function render(array $input) {
    // Do never send password
    $input['value'] = '';
    $input['attributes']['type'] = 'password';
    return parent::render($input);
  }
}