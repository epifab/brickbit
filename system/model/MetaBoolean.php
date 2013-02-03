<?php
namespace system\model;

class MetaBoolean extends MetaType {
	public function prog2Db($x) {
		if (\is_null($x)) {
			if ($this->getAttr('nullable', array('default' => true))) {
				return "NULL";
			} else {
				return "0";
			}
		} else {
			return $x;
		}
	}
	
	public function db2Prog($x) {
		$x = (bool)$x;
		return $x;
	}
	
	public function edit2Prog($x) {
		$x = (bool)$x;
		$this->validate($x);
		return $x;
	}
	
	public function validate($x) {
	}

	public function getEditWidgetDefault() {
		return 'checkbox';
	}
}
?>