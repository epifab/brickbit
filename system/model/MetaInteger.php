<?php
namespace system\model;

class MetaInteger extends MetaType {
	public static function stdDb2Prog($x) {
		if (\is_null($x)) {
			return null;
		} else {
			return (int)$x;
		}
	}
	public static function stdProg2Db($x) {
		if (\is_null($x)) {
			return "NULL";
		} else if ($x == 0) {
			return "0";
		} else {
			return $x;
		}
	}
	public static function stdEdit2Prog($x) {
		if (\is_null($x)) {
			return null;
		} else {
			return (int)$x;
		}
	}
	public static function stdProg2Edit($x) {
		if ($x == 0) {
			return "0";
		} else {
			return $x;
		}
	}
	public static function stdProg2Read($x) {
		self::stdProg2Edit($x);
	}
	public static function formalValidation($x) {
		if (!empty($x) && !is_integer($x) && !\preg_match('/^-?[0-9]+$/', $x)) {
			throw new \system\InternalErrorException("Formato numerico non valido");
		}
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