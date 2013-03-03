<?php
namespace system\model;

class MetaDateTime extends MetaType {
	protected $sqlFormat = 'Y-m-d H:i:s';
	
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
			$h = \substr($x,11,2);
			$i = \substr($x,14,2);
			$s = \substr($x,17,2);
			return \mktime($h,$i,$s,$m,$d,$y);
		}
	}
	
	public function edit2Prog($x) {
		if (\is_array($x)) {
			$y = intval(\system\Utils::getParam('year', array('default' => -1)));
			$m = intval(\system\Utils::getParam('month', array('default' => -1)));
			$d = intval(\system\Utils::getParam('day', array('default' => -1)));
			$h = intval(\system\Utils::getParam('hours', array('default' => -1)));
			$i = intval(\system\Utils::getParam('minutes', array('default' => -1)));
			$s = intval(\system\Utils::getParam('seconds', array('default' => -1)));
			if ($h < 0 || $h > 23 || $i < 0 || $i > 59 || $s < 0 || $s > 59) {
				throw new \system\ValidationException(\t('Invalid date'));
			}
			if (\checkdate($m, $d, $y)) {
				$x = \mktime($h,$i,$s,$m,$d,$y);
			} else {
				throw new \system\ValidationException(\t('Invalid date.'));
			}
		} else if (\is_string($x)) {
			if (\preg_match('/^[0-9]{4}-[0-9]{2}-[0-9]{2}.[0-9]{2}.[0-9]{2}.[0-9]{2}$/', $x)) {
				$y = \substr($x,0,4);
				$m = \substr($x,5,2);
				$d = \substr($x,8,2);
				$h = \substr($x,11,2);
				$i = \substr($x,14,2);
				$s = \substr($x,17,2);
				if ($h < 0 || $h > 23 || $i < 0 || $i > 59 || $s < 0 || $s > 59) {
					throw new \system\ValidationException(\t('Invalid date'));
				}
				if (\checkdate($m, $d, $y)) {
					$x = \mktime($h,$i,$s,$m,$d,$y);
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