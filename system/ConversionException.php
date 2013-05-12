<?php
namespace system;

class ConversionException extends \Exception {
	public function __construct($message, $arguments = array(), $previous=null) {
		parent::__construct(\cb\t($message, $arguments), ErrorCodes::GENERAL, $previous);
	}
}
?>