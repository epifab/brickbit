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
		eval('$this->handle = ' . $handle . ';');
	}
	public function getHandler() {
		return $this->handle;
	}
	
	public function isVirtual() {
		return true;
	}
}
?>