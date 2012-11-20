<?php
namespace system;

class GroupingMetaType extends MetaType {
	private $sqlName;
	private $sqlFunction;
	
	public function __construct($vname, $sqlName, $sqlFunction, $type, RecordsetBuilderInterface $builder) {
		parent::__construct($vname, $type, $builder);
		
		$this->sqlName = $sqlName;
		$this->sqlFunction = $sqlFunction;

		$this->validate(function($x) { throw new ValidationException("Il campo e' virtuale e non puo' essere scritto in memoria"); });
	}
	
	public function getExprLocal() {
		return $this->getSqlFunction() . "(" . $this->getSqlName() . ")";
	}
	
	public function getExprGlobal() {
		return $this->getBuilder()->getTableAlias() . "." . $this->getExprLocal();
//		$this->getSqlFunction() . "(" . $this->getBuilder()->getTableAlias() . "." . $this->getSqlName() . ")";
	}
	
	public function getSqlName() {
		return $this->sqlName;
	}
	
	public function getSqlFunction() {
		return $this->sqlFunction;
	}
	
	public function readOnly() {
		return true;
	}
}
?>