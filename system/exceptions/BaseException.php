<?php
namespace system\exceptions;

abstract class BaseException extends \Exception {
	public function __construct($message, $arguments = array(), $previous=null) {
		parent::__construct(\cb\t($message, $arguments), 0, $previous);
	}
}
