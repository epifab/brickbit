<?php
namespace system;

class TemplateManagerException extends \Exception {
	function __construct($message, $previous=null) {
		parent::__construct($message, ErrorCodes::TEMPLATE, $previous);
	}
}
?>