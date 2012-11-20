<?php
namespace system\model;

class MetaOptions extends MetaType {
	private $options = array();
	private $keyMetaType = '\system\model\MetaString';
	
	protected function _stdDb2Prog($x) {
		return \call_user_func(array($this->keyMetaType, "stdDb2Prog"), $x);
	}
	protected function _stdProg2Db($x) {
		return \call_user_func(array($this->keyMetaType, "stdProg2Db"), $x);
	}
	protected function _stdEdit2Prog($x) {
		return \call_user_func(array($this->keyMetaType, "stdEdit2Prog"), $x);
	}
	protected function _stdProg2Edit($x) {
		return \call_user_func(array($this->keyMetaType, "stdProg2Edit"), $x);
	}
	protected function _stdProg2Read($x) {
		if (\array_key_exists($key, $this->options)) {
			return $this->options[$key];
		} else {
			return "";
		}
	}
	protected function _formalValidation($x) {
		if (!\array_key_exists($x, $this->options)) {
			throw new \system\InternalErrorException("Opzione $x non valida " . print_r($this->options, true));
		}
	}
	
	public function setOptions($options) {
		$this->options = array();
		foreach ($options as $k => $v) {
			if (!\is_scalar($k) || !\is_scalar($v)) {
				throw new \system\InternalErrorException("Opzione non valida");
			}
			$this->options[$k] = $v;
		}
	}
	
	public function getOptions() {
		return $this->options;
	}
}
?>