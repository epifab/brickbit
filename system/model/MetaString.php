<?php
namespace system\model;

class MetaString extends MetaType {
	public static function stdDb2Prog($x) {
		return $x;
	}
	public static function stdProg2Db($x) {
		if (\is_null($x)) {
			return "NULL";
		} else {
			return "'" . DataLayerCore::getInstance()->sqlRealEscapeStrings(\stripslashes($x)) . "'";
		}
	}
	public static function stdEdit2Prog($x) {
		return $x;
	}
	public static function stdProg2Edit($x) {
		return $x;
	}
	public static function stdProg2Read($x) {
		return $x;
	}
	public static function formalValidation($x) {
	}
	
	protected function _stdDb2Prog($x) {
		return self::stdDb2Prog($x);
	}
	protected function _stdProg2Db($x) {
		return self::stdProg2Db($x);
	}
	protected function _stdEdit2Prog($x) {
		return self::stdEdit2Prog($x);
	}
	protected function _stdProg2Edit($x) {
		return self::stdProg2Edit($x);
	}
	protected function _stdProg2Read($x) {
		return self::stdProg2Read($x);
	}
	protected function _formalValidation($x) {
		return self::formalValidation($x);
	}
}
?>