<?php
namespace system\view;

interface WidgetInterface {
	/**
	 * Returns the widget HTML
	 */
	public function render(array $input);
	/**
	 * Fetch the widget value
	 */
	public function fetch($value, array $input);
}
?>