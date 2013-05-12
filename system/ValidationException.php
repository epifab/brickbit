<?php
namespace system;

class ValidationException extends \Exception {
	function __construct($message, $arguments = array(), $previous=null) {
		parent::__construct(\cb\t($message, $arguments), ErrorCodes::INPUT, $previous);
	}
}
?>