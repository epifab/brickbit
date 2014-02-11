<?php
namespace system\exceptions;

class PageNotFound extends \system\exceptions\InputOutputError {
	public function __construct($page = null) { 
		parent::__construct('Page <em>@name</em> not found', empty($page) ? $_SERVER['REQUEST_URI'] : $page);
	}
}
