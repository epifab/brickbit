<?php

namespace system\model;

abstract class MetaType {

	protected $name;
	protected $type;
	protected $attributes;
	protected $selectExpression;
	protected $builder;

	public function __construct($name, $type, RecordsetBuilder $builder, $attributes = array()) {
		$this->name = $name;
		$this->type = $type;
		$this->builder = $builder;
		$this->attributes = $attributes;
	}

	public function isVirtual() {
		return false;
	}

	public function getDefaultValue() {
		return null;
	}

	/**
	 * Restituisce l'espressione SQL per la selezione del campo
	 * @return string
	 */
	public function getSelectExpression() {
		if (empty($this->selectExpression)) {
			return $this->builder->getTableAlias() . "." . $this->name;
		}
		return $this->selectExpression;
	}

	public function attrExists($key) {
		return \array_key_exists($key, $this->attributes);
	}

	public function getAttr($key, $options = array()) {
		return \system\Utils::getParam($key, $this->attributes, $options);
	}

	public function getAttributes() {
		return $this->attributes;
	}

	public function getName() {
		return $this->name;
	}

	public function getType() {
		return $this->type;
	}

	public function getAlias() {
		return $this->builder->getTableAlias() . "__" . $this->name;
	}

	public function getAbsolutePath() {
		return ($this->builder->getAbsolutePath() != "" ? $this->builder->getAbsolutePath() . "." . $this->name : $this->name);
	}

	public function getTableName() {
		return $this->builder->getTableName();
	}

	public function getTableAlias() {
		return $this->builder->getTableAlias();
	}

	public function getBuilder() {
		return $this->builder;
	}

	protected abstract function getEditWidgetDefault();

	public final function getEditWidget() {
		return $this->attrExists('widget') ? $this->getAttr('widget') : $this->getEditWidgetDefault();
	}

	public function db2Prog($x) {
		return $x;
	}

	public function prog2Db($x) {
		return $x;
	}

	public function edit2Prog($x) {
		return $x;
	}

	public function prog2Edit($x) {
		return $x;
	}

	public function prog2Read($x) {
		return $x;
	}

	public function validate($x) {
		
	}

}

?>