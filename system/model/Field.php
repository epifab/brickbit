<?php
namespace system\model;

class Field {
	/**
	 * @var \system\metatypes\MetaType
	 */
	protected $metaType;
	/**
	 * @var string
	 */
	protected $selectExpression;
	/**
	 * @var \system\model\RecordsetBuilder
	 */
	protected $builder;
	
	public function __construct($name, $type, RecordsetBuilder $builder, array $attributes) {
		$this->builder = $builder;
		
		$this->metaType = \system\metatypes\MetaType::newMetaType($name, $type, $attributes);
	}
	
	/**
	 * Restituisce l'espressione SQL per la selezione del campo
	 * @return string
	 */
	public function getSelectExpression() {
		if (empty($this->builder)) {
			throw new \system\error\InternalError('Unable to retrieve the select expression.');
		}
		if (empty($this->selectExpression)) {
			return $this->builder->getTableAlias() . "." . $this->getName();
		}
		return $this->selectExpression;
	}

	public function getAlias() {
		return $this->builder->getTableAlias() . "__" . $this->getName();
	}

	public function getAbsolutePath() {
		return ($this->builder->getAbsolutePath() != '' ? $this->builder->getAbsolutePath() . "." . $this->getName() : $this->getName());
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
	
	/**
	 * @return MetaType
	 */
	public function getMetaType() {
		return $this->metaType;
	}
	
	public function isVirtual() {
		return false;
	}

//	/*
//	 * Implements delegate design pattern
//	 */
//	public function __call($name, $arguments) {
//		if (is_callable(array($this->metaType, $name))) {
//			call_user_func(array($this->metaType, $name), $arguments);
//		} else {
//			throw new \system\error\InternalError('Uknown method <em>@name</em>', array('@name' => $name));
//		}
//	}

	public final function getDefaultValue() {
		return $this->metaType->getDefaultValue();
	}

	public final function attrExists($key) {
		return $this->metaType->attrExists($key);
	}

	public final function getAttr($key, $options = array()) {
		return $this->metaType->getAttr($key, $options);
	}

	public final function getAttributes() {
		return $this->metaType->getAttributes();
	}

	public final function getName() {
		return $this->metaType->getName();
	}

	public final function getType() {
		return $this->metaType->getType();
	}
	
	public final function getEditWidget() {
		return $this->metaType->getEditWidget();
	}

	public final function db2Prog($x) {
		return $this->metaType->db2Prog($x);
	}

	public final function prog2Db($x) {
		return $this->metaType->prog2Db($x);
	}

	public final function edit2Prog($x) {
		return $this->metaType->edit2Prog($x);
	}

	public final function prog2Edit($x) {
		return $this->metaType->prog2Edit($x);
	}

	public final function validate($x) {
		return $this->metaType->validate($x);
	}
}