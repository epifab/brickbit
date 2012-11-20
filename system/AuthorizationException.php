<?php
namespace system;

class AuthorizationException extends \Exception {
	public function __construct($message, $previous=null) {
		parent::__construct($message, ErrorCodes::AUTHORIZATION, $previous);
	}
}
?>
