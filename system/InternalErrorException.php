<?php
namespace system;

class InternalErrorException extends \Exception {
	function __construct($message, $arguments = array(), $previous=null) {
		parent::__construct(\cb\t($message, $arguments), ErrorCodes::INTERNAL, $previous);
	}
}
?>