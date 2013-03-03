<?php
namespace system\model;

class MetaDate extends MetaType {
	protected $sqlFormat = 'Y-m-d';
	
	public function prog2Db($x) {
		if (empty($x)) {
			if ($this->getAttr('nullable', array('default' => true))) {
				return "NULL";
			} else {
				return "'" . \date($this->sqlFormat) . "'";
			}
		} else {
			return "'" . \date($this->sqlFormat, $x) . "'";
		}
	}
	
	public function db2Prog($x) {
		if (empty($x)) {
			return $this->toProg(null);
		} else {
			$y = \substr($x,0,4);
			$m = \substr($x,5,2);
			$d = \substr($x,8,2);
			return \mktime(0,0,0,$m,$d,$y);
		}
	}
	
	public function edit2Prog($x) {
		if (\is_array($x)) {
			$y = intval(\system\Utils::getParam('year', array('default' => -1)));
			$m = intval(\system\Utils::getParam('month', array('default' => -1)));
			$d = intval(\system\Utils::getParam('day', array('default' => -1)));
			if (\checkdate($m, $d, $y)) {
				$x = \mktime(0,0,0,$m,$d,$y);
			} else {
				throw new \system\ValidationException(\t('Invalid date.'));
			}
		} else if (\is_string($x)) {
			if (\preg_match('/^[0-9]{4}-[0-9]{2}-[0-9]{2}$/', $x)) {
				$y = \substr($x,0,4);
				$m = \substr($x,5,2);
				$d = \substr($x,8,2);
				if (\checkdate($m, $d, $y)) {
					$x = \mktime(0,0,0,$m,$d,$y);
				} else {
					throw new \system\ValidationException(\t('Invalid date.'));
				}
			}
		} else {
			$x = $this->toProg($x);
		}
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
	
	public function getDefaultValue() {
		return \time();
	}
	
	public function toProg($x) {
		if (\is_null($x)) {
			if ($this->getAttr('nullable', array('default' => true))) {
				return null;
			} else {
				return \time();
			} 
		} else {
			return \intval($x);
		}
	}
}
?>