<?php
namespace system\model;

class MetaDate extends MetaType {
	public static function stdDb2Prog($x) {
		if (empty($x)) {
			return null;
		} else {
			$y = \substr($x,0,4);
			$m = \substr($x,5,2);
			$d = \substr($x,8,2);
			return \mktime(0,0,0,$m,$d,$y);
		}
	}
	public static function stdProg2Db($x) {
		if (\is_null($x)) {
			return "NULL";
		} else {
			return "'" . \date("Y-m-d", $x) . "'";
		}
	}
	public static function stdEdit2Prog($x) {
		if (empty($x)) {
			return null;
		} else {
			// Formato dd/mm/yyyy
			$y = \substr($x,6,4);
			$m = \substr($x,3,2);
			$d = \substr($x,0,2);
			return \mktime(0,0,0,$m,$d,$y);
		}
	}
	public static function stdProg2Edit($x) {
		if (\is_null($x)) {
			return "";
		} else {
			return \date("d/m/Y", $x);
		}
	}
	public static function stdProg2Read($x) {
		return $this->stdProg2Edit($x);
	}
	public static function formalValidation($x) {
		if (!\preg_match("/^[0-3][0-9]/[0-1][0-9]/[1-2][0-9]{3}$/", $x)) {
			throw new \system\InternalErrorException("Formato data non valido");
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