<?php
namespace system\exceptions;

class AuthorizationError extends \system\exceptions\Error {
  public function __construct($message = 'Sorry, you are not authorized to access this resource', $args = array()) {
    parent::__construct($message, $arguments);
  }
}

