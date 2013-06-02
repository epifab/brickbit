<?php
namespace system\error;

abstract class Error extends \Exception {
	public function __construct($message, $arguments = array(), $previous=null) {
		parent::__construct(\cb\t($message, $arguments), 0, $previous);
	}
	
	public function getDetails() {
		return '';
	}
}
?>