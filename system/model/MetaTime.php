<?php
namespace system\model;

class MetaTime extends MetaType {
	public static function stdDb2Prog($x) {
		if (empty($x)) {
			return null;
		} else {
			$h = \substr($x,0,2);
			$i = \substr($x,3,2);
			$s = \substr($x,6,2);
			return \mktime($h,$i,$s);
		}
	}
	public static function stdProg2Db($x) {
		if (\is_null($x)) {
			return "NULL";
		} else {
			return "'" . \date("H:i:s", $x) . "'";
		}
	}
	public static function stdEdit2Prog($x) {
		return self::stdDb2Prog($x);
	}
	public static function stdProg2Edit($x) {
		if (\is_null($x)) {
			return "";
		} else {
			return "'" . \date("H:i:s", $x) . "'";
		}
	}
	public static function stdProg2Read($x) {
		return self::stdProg2Edit($x);
	}
	public static function formalValidation($x) {
		if (!\preg_match("/^[0-2][0-9]:[0-5][0-9]:[0-5][0-9]$/", $x)) {
			throw new \system\InternalErrorException("Formato ora non valido");
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