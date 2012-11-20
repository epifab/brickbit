<?php
namespace system\model;

class MetaReal extends MetaType {
	public static function stdDb2Prog($x) {
		if (\is_null($x)) {
			return null;
		} else {
			return (double)$x;
		}
	}
	public static function stdProg2Db($x) {
		if (\is_null($x)) {
			return "NULL";
		} else if ($x == 0.0) {
			return "0.0";
		} else {
			return $x;
		}
	}
	public static function stdEdit2Prog($x) {
		if (empty($x)) {
			return null;
		} else {
			$x = str_replace($x, ".", "");
			$x = str_replace($x, ",", ".");
			return (double)$x;
		}
	}
	public static function stdProg2Edit($x) {
		return \number_format($x,3,",",".");
	}
	public static function stdProg2Read($x) {
		return \number_format($x,3,",",".");
	}
	public static function formalValidation($x) {
		if (!empty($x) && !\is_real($x) && !\preg_match('/^-?[0-9]{1,2}(?:\\.[0-9]{3})*|[0-9]*(?:\\,[0-9]+)?$/', $x)) {
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