<?php
namespace system\model;

class MetaString extends MetaType {
	public static function stdProg2Db($x) {
		return "'" . \system\model\DataLayerCore::getInstance()->sqlRealEscapeStrings($x) . "'";
	}
	
	public function prog2Db($x) {
		if ($this->getAttr('multiple', array('default' => false))) {
			$x = $this->toArray($x);
			return self::stdProg2Db(\serialize($x));
		}
		if (empty($x)) {
			if ($this->getAttr('nullable', array('default' => true)) && \is_null($x)) {
				return "NULL";
			} else {
				return "''";
			}
		} else {
			return self::stdProg2Db($x);
		}
	}
	
	public function db2Prog($x) {
		if ($this->getAttr('multiple', array('default' => false))) {
			$x = \unserialize($x);
			$x = $this->toArray($x);
			foreach ($x as $k => $v) {
				$x[$k] = $this->toProg($v);
			}
		} else {
			$x = $this->toProg($x);
		}
		return $x;
	}
	
	public function edit2Prog($x) {
		if ($this->getAttr('multiple', array('default' => false))) {
			$x = $this->toArray($x);
			foreach ($x as $k => $v) {
				$x[$k] = $this->toProg($v);
			}
		} else {
			$x = $this->toProg($x);
		}
		$this->validate($x);
		return $x;
	}
	
	public function validate($x) {
		if ($this->getAttr('multiple', array('default' => false))) {
			$x = $this->toArray($x);
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
		$options = $this->getAttr('options', array('default' => null));
		if (!\is_null($options)) {
			if (!\array_key_exists($x, $options)) {
				throw new \system\ValidationException('Invalid value for <me>@name</em> field.', array(
					'@name' => $this->getAttr('label', array('default' => $this->getName()))
				));
			}
		}
		$regexp = $this->getAttr('regexp', array('default' => null));
		if (!\is_null($regexp)) {
			if (!@\preg_match('@^' . $regexp . '$@', $x)) {
				throw new \system\ValidationException('Invalid value for <me>@name</em> field.', array(
					'@name' => $this->getAttr('label', array('default' => $this->getName()))
				));
			}
		}
		$maxlength = $this->getAttr('maxlength', array('default' => null));
		if (!\is_null($maxlength)) {
			if (\strlen($x) > $maxlength) {
				throw new \system\ValidationException('Too long value for <me>@name</em> field.', array(
					'@name' => $this->getAttr('label', array('default' => $this->getName()))
				));
			}
		}
		$minlength = $this->getAttr('minlength', array('default' => null));
		if ($minlength) {
			if (\strlen($x) > $maxlength) {
				throw new \system\ValidationException('Too short value for <me>@name</em> field.', array(
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
	
	public function toProg($x) {
		if (\is_null($x)) {
			if ($this->getAttr('nullable', array('default' => true))) {
				return null;
			} else {
				return '';
			} 
		} else {
			return \strval($x);
		}
	}
}
?>