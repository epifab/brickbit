<?php
namespace system;

interface ThemeInterface {
  /**
   * This hook is fired whenever a component is initialised
   * @param \system\Component $component Component running
   */
  public function init(\system\Component $component);

  /**
   * Renders an element
   * @param string $name Element name
   * @param array $attributes Element attributes
   */
  public function render($name, array $attributes);
}
