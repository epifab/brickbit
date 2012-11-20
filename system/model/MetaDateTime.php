<?php
namespace system\model;

class MetaDateTime extends MetaType {
	public static function stdDb2Prog($x) {
		if (empty($x)) {
			return null;
		} else {
			$y = \substr($x,0,4);
			$m = \substr($x,5,2);
			$d = \substr($x,8,2);
			$h = \substr($x,11,2);
			$i = \substr($x,14,2);
			$s = \substr($x,17,2);
			return \mktime($h,$i,$s,$m,$d,$y);
		}
	}
	public static function stdProg2Db($x) {
		if (\is_null($x)) {
			return "NULL";
		} else {
			return "'" . \date("Y-m-d H:i:s", $x) . "'";
		}
	}
	public static function stdEdit2Prog($x) {
		if (empty($x)) {
			return null;
		} else {
			// Formato dd/mm/yyyy hh:ii:ss			//         0123456789012345678
			$y = \substr($x,6,4);
			$m = \substr($x,3,2);
			$d = \substr($x,0,2);
			$h = \substr($x,11,2);
			$i = \substr($x,14,2);
			$s = \substr($x,17,2);
			return \mktime($h,$i,$s,$m,$d,$y);
		}
	}
	public static function stdProg2Edit($x) {
		if (\is_null($x)) {
			return "";
		} else {
			return \date("d/m/Y H:i:s", $x);
		}
	}
	public static function stdProg2Read($x) {
		return self::stdProg2Edit($x);
	}
	public static function formalValidation($x) {
		if (!\preg_match("/^[0-3][0-9]\\/[0-1][0-9]\\/[1-2][0-9]{3} [0-2][0-9]\\:[0-5][0-9]\\:[0-5][0-9]$/", $x)) {
			throw new \system\InternalErrorException("Formato data ora non valido");
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