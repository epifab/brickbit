<?php
namespace system\model;

class MetaBoolean extends MetaOptions {
//	private $nullAsZero;
	
	public function __construct($name, $builder, $baseMetaType='Integer') {
		parent::__construct($name, $builder, $baseMetaType);
		parent::setOptions(array(
			 "0" => "False", 
			 "1" => "True"
		));
	}
	
//	public function setNullAsZero($nullAsZero) {
//		$this->nullAsZero = (bool)$nullAsZero;
//	}
//	public function getNullAsZero() {
//		return $this->nullAsZero;
//	}

	protected function _stdDb2Prog($x) {
		return ((int)((bool)$x));
	}
	
	protected function _stdProg2Db($x) {
		return ((int)$x);
	}
	
	protected function _stdEdit2Prog($x) {
		return ((int)((bool)$x));
	}
	
	protected function _formalValidation($x) {
		return true;
	}
	
}
?>