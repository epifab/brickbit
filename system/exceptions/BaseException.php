<?php
namespace system\exceptions;

use system\utils\Lang;

abstract class BaseException extends \Exception {
  public function __construct($message, $arguments = array(), $previous=null) {
    parent::__construct(Lang::format($message, $arguments), 0, $previous);
  }
}
