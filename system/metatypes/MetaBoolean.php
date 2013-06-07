<?php
namespace system\metatypes;

class MetaBoolean extends MetaType {
	public function prog2Db($x) {
		if ($x) {
			return "1";
		} else {
			return 0;
		}
	}
	
	public function validate($x) {
		
	}

	public function getEditWidgetDefault() {
		return 'checkbox';
	}
	
	public function toProg($x) {
		if (\is_null($x)) {
			return false;
		} else {
			return (bool)$x;
		}
	}
}
