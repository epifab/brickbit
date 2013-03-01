<?php
namespace system\model;

class MetaDecimal extends MetaType {
	
	public function prog2Db($x) {
		if ($this->getAttr('multiple', array('default' => false))) {
			if (!\is_array($x)) {
				// make sure we have an array
				$x = array($x);
			}
			return MetaString::stdProg2Db(\serialize($x));
		}
		else if (empty($x)) {
			if ($this->getAttr('nullable', array('default' => true)) && \is_null($x)) {
				return "NULL";
			} else {
				return "0.0";
			}
		}
		else {
			return $x;
		}
	}
	
	public function db2Prog($x) {
		if ($this->getAttr('multiple', array('default' => false))) {
			$x = \unserialize($x);
			if (!\is_array($x)) {
				$x = array($x); // just to make sure
			}
			foreach ($x as $k => $v) {
				$x[$k] = (double)$v;
			}
		} else {
			$x = (double)$x;
		}
		return $x;
	}
	
	public function edit2Prog($x) {
		if ($this->getAttr('multiple', array('default' => false))) {
			if (!\is_array($x)) {
				$x = array($x); // just to make sure
			}
			foreach ($x as $k => $v) {
				$x[$k] = (double)$v;
			}
		} else {
			$x = (double)$x;
		}
		$this->validate($x);
		return $x;
	}
	
	public function validate($x) {
		if ($this->getAttr('multiple', array('default' => false))) {
			if (!\is_array($x)) {
				$x = array($x);
			}
			$minOccurrence = $this->getAttr('minOccurrence', array('default' => 0));
			$maxOccurrence = $this->getAttr('maxOccurrence', array('default' => 0));
			if (\count($x) < $minOccurrence) {
				throw new \system\ValidationException('At least @n values should be entered in the <em>@name</em> field.', array(
					'@n' => $minOccurrence,
					'@name' => $this->getAttr('label', array('default' => $this->getName()))
				));
			}
			if ($maxOccurrence > 0 && \count($x) > $maxOccurrence) {
				throw new \system\ValidationException('No more than @n values can be entered in the <em>@name</em> field.', array(
					'@n' => $minOccurrence,
					'@name' => $this->getAttr('label', array('default' => $this->getName()))
				));
			}
			foreach ($x as $v) {
				$this->validateSingle($v);
			}
		} else {
			$this->validateSingle($x);
		}
	}
	
	protected function validateSingle($x) {
		$options = $this->getAttr('options');
		if ($options) {
			if (!\array_key_exists($x, $options)) {
				throw new \system\ValidationException('Invalid value for <me>@name</em> field.', array(
					'@name' => $this->getAttr('label', array('default' => $this->getName()))
				));
			}
		}
		$minvalue = $this->getAttr('minvalue', array('default' => null));
		if (!\is_null($minvalue)) {
			if ($x > $minvalue) {
				throw new \system\ValidationException('Number too small for <me>@name</em> field.', array(
					'@name' => $this->getAttr('label', array('default' => $this->getName()))
				));
			}
		}
		$maxvalue = $this->getAttr('maxvalue', array('default' => null));
		if (!\is_null($maxvalue)) {
			if ($x > $maxvalue) {
				throw new \system\ValidationException('Number too big for <me>@name</em> field.', array(
					'@name' => $this->getAttr('label', array('default' => $this->getName()))
				));
			}
		}
	}
	
	public function getEditWidgetDefault() {
		if ($this->attrExists('options')) {
			if ($this->attrExists('multiple')) {
				return 'checkboxes';
			} else {
				return 'selectbox';			
			}
		} else {
			return 'textbox';
		}
	}
}
?>