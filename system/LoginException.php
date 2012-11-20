<?php
namespace system;

class LoginException extends \Exception {
	function __construct($message, $previous=null) {
		parent::__construct($message, ErrorCodes::INTERNAL, $previous);
	}
}
?>