<?php
namespace system\model;

class FieldVirtual extends Field implements \Serializable {
	private $handle;
	
	public function __construct($name, $type, RecordsetBuilder $builder, array $attributes) {
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
	
	public function isVirtual() {
		return true;
	}
	
	public function setHandler($handle) {
		if (\is_array($handle)) {
			if (\is_callable($handle)) {
				$this->handle = $handle;
			} else {
				throw new \system\error\InternalError('Method @class::@method does not exist.', array(
					'@method' => $handle[1], 
					'@class' => $handle[0]
				));
			}
		} else {
			eval('$this->handle = ' . $handle . ';');
		}
	}
	
	public function getHandler() {
		return $this->handle;
	}
	
	public function serialize() {
		return \serialize(array(
			$this->getName(),
			$this->getType(),
			$this->builder,
			$this->getAttributes()
		));
	}
	
	public function unserialize($serialized) {
		list($a, $b, $c, $d) = \unserialize($serialized);
		return new self($a, $b, $c, $d);
	}
}
