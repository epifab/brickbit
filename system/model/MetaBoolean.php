<?php
namespace system\model;

class MetaBoolean extends MetaOptions {
	public function __construct($name, $builder) {
		parent::__construct($name, $builder);
		parent::setOptions(array(
			 "0" => "False", 
			 "1" => "True"
		));
	}
}
?>