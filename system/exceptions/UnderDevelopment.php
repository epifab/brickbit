<?php
namespace system\exceptions;

class UnderDevelopment extends \system\exceptions\InputOutputError {
  public function __construct($page = '') { 
    parent::__construct('The resource you tried to access is under active development. Please try later.');
  }
}
