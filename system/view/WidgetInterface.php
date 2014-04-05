<?php
namespace system\view;

interface WidgetInterface {
  /**
   * Returns the widget HTML
   */
  public function render(array $input);
  /**
   * Fetches an input value generated from the widget
   */
  public function fetch($value, array $input);
}
