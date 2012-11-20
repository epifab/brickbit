<?php
namespace system;

class ConversionException extends \Exception {
	function __construct($message, $previous=null) {
		parent::__construct($message, ErrorCodes::INTERNAL, $previous);
	}
}
?>