<?php
namespace system;

class LoginException extends \Exception {
	public function __construct($message, $arguments = array(), $previous=null) {
		parent::__construct(\cb\t($message, $arguments), ErrorCodes::AUTHORIZATION, $previous);
	}
}
?>