<?php
namespace system\model;

class MetaVirtual extends MetaString {
	private $handle;
	
	public function __construct($name, $type, RecordsetBuilder $builder, $attributes=array()) {
		parent::__construct($name, $type, $builder, $attributes);
		$handle = $this->getAttr('handle', array('required' => true));
		$this->setHandler($handle);
		$dependencies = $this->getAttr('dependencies', array('default' => false));
		if ($dependencies && \is_array($dependencies)) {
			foreach ($dependencies as $d) {
				$builder->using($d);
			}
		}
	}
	
	public function setHandler($handle) {
		if (\is_array($handle)) {
			if (\is_callable($handle)) {
				$this->handle = $handle;
			} else {
				throw new \system\InternalErrorException(\t('Method @class::@method does not exist.', array(
					'@method' => $handle[1], 
					'@class' => $handle[0]
				)));
			}
		} else {
			eval('$this->handle = ' . $handle . ';');
		}
	}
	public function getHandler() {
		return $this->handle;
	}
	
	public function isVirtual() {
		return true;
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