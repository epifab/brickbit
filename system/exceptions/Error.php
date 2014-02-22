<?php
namespace system\exceptions;

abstract class Error extends \system\exceptions\BaseException {
  public function getDetails() {
    return '';
  }
}
