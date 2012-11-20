<?php
namespace system;

class LangException extends \Exception {
	function __construct($message, $previous=null) {
		parent::__construct($message, ErrorCodes::INTERNAL, $previous);
	}
}
?>