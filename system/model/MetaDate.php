<?php
namespace system\model;

class MetaDate extends MetaType {
	protected $sqlFormat = 'Y-m-d';
	
	public function prog2Db($x) {
		if (\is_null($x)) {
			if ($this->getAttr('nullable', array('default' => true))) {
				return "NULL";
			} else {
				return "'" . \date($this->sqlFormat) . "'";
			}
		} else {
			return \date($this->sqlFormat, $x);
		}
	}
	
	public function db2Prog($x) {
		$y = \substr($x,0,4);
		$m = \substr($x,5,2);
		$d = \substr($x,8,2);
		return \mktime(0,0,0,$m,$d,$y);
	}
	
	public function edit2Prog($x) {
		$x = (int)$x;
		$this->validate($x);
		return $x;
	}
	
	public function validate($x) {
		$options = $this->getAttr('options');
		if ($options) {
			if (!\array_key_exists($x, $options)) {
				throw new \system\ValidationException('Invalid value for <me>@name</em> field.', array(
					'@name' => $this->getAttr('label', array('default' => $this->getName()))
				));
			}
		}
	}

	public function getEditWidgetDefault() {
		return 'textbox';
	}
}
?>