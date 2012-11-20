<?php
namespace system\model;

abstract class MetaType {
	protected $name;
	protected $desc = null;
	
	protected $selectExpression;
	
	protected $builder;
	
	protected $defaultValue = null;
	protected $nullable = false;
	
	protected $db2ProgHandle = null;
	protected $prog2DbHandle = null;
	protected $edit2ProgHandle = null;
	protected $prog2EditHandle = null;
	protected $prog2ReadHandle = null;
	protected $validateHandles = array();
	
	protected abstract function _stdDb2Prog($x);
	protected abstract function _stdProg2Db($x);
	protected abstract function _stdEdit2Prog($x);
	protected abstract function _stdProg2Edit($x);
	protected abstract function _stdProg2Read($x);
	// Validazione formale dei campi prima della conversione edit2Prog
	protected abstract function _formalValidation($x);
	
	protected static function checkCallable($func) {
		if (!\is_callable($func)) {
			throw new \system\InternalErrorException("Il parametro non rappresenta ne' una funzione ne' un metodo");
		}
	}
	
	public function __construct($name, RecordsetBuilderInterface $builder) {
		$this->name = $name;
		$this->builder = $builder;
		
		$this->setDb2Prog(array($this, "_stdDb2Prog"));
		$this->setProg2Db(array($this, "_stdProg2Db"));
		$this->setEdit2Prog(array($this, "_stdEdit2Prog"));
		$this->setProg2Edit(array($this, "_stdProg2Edit"));
		$this->setProg2Read(array($this, "_stdProg2Read"));
	}
	
	public function setDefaultValue($defaultValue) {
		$this->defaultValue = $defaultValue;
	}
	
	/**
	 * Imposta il campo come virtuale
	 * Permette di specificare un'espressione SQL per la selezione dei dati
	 * @param string $selectExpression 
	 */
	public function setVirtual($selectExpression) {
		$this->selectExpression = $selectExpression;
	}
	
	/**
	 * True se il MetaType è virtuale
	 * @return boolean
	 */
	public function isVirtual() {
		return !empty($this->selectExpression);
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
	
	public function isInteger() {
		return $this instanceof MetaInteger;
	}
	public function isReal() {
		return $this instanceof MetaReal;
	}
	public function isString() {
		return $this instanceof MetaString;
	}
	public function isDate() {
		return $this instanceof MetaDate;
	}
	public function isTime() {
		return $this instanceof MetaTime;
	}
	public function isDateTime() {
		return $this instanceof MetaDateTime;
	}
	public function isOptions() {
		return $this instanceof MetaOptions;
	}
	public function isBoolean() {
		return $this instanceof MetaBoolean;
	}

	
	public function setDesc($desc) {
		$this->desc = $desc;
		return $this;
	}
	
	public function getDesc() {
		return empty($this->desc) ? $this->name : $this->desc;
	}
	
	public function setDb2Prog($db2ProgHandle) {	
		self::checkCallable($db2ProgHandle);
		$this->db2ProgHandle = $db2ProgHandle;
	}
	
	public function setProg2Db($prog2DbHandle) {
		self::checkCallable($prog2DbHandle);
		$this->prog2DbHandle = $prog2DbHandle;
	}
	
	public function setEdit2Prog($edit2ProgHandle) {
		self::checkCallable($edit2ProgHandle);
		$this->edit2ProgHandle = $edit2ProgHandle;
	}
	
	public function setProg2Edit($prog2EditHandle) {
		self::checkCallable($prog2EditHandle);
		$this->prog2EditHandle = $prog2EditHandle;
	}
	
	public function setProg2Read($prog2ReadHandle) {
		self::checkCallable($prog2ReadHandle);
		$this->prog2ReadHandle = $prog2ReadHandle;
	}

	public function addValidate($validateHandle) {
		self::checkCallable($validateHandle);
		$this->validateHandles[] = $validateHandle;
	}
	
	public function getName() {
		return $this->name;
	}
	public function getAlias() {
		return $this->builder->getTableAlias() . "__" . $this->name;
	}
	public function getAbsolutePath() {
		return ($this->builder->getAbsolutePath() != "" ? $this->builder->getAbsolutePath() . "." . $this->name : $this->name);
	}
	public function getDefaultValue() {
		return $this->defaultValue;
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
	
	private function exec($func, $arg) {
		return \is_null($func) ? $arg : \call_user_func($func, $arg);
	}
	
	public function db2Prog($arg) {
		return $this->exec($this->db2ProgHandle, $arg);
	}
	public function prog2Db($arg) {
		if ($this->isVirtual()) {
			throw new \system\InternalErrorException("Il campo e' definito come virtuale: impossibile procedere con la conversione");
		}
		return $this->exec($this->prog2DbHandle, $arg);
	}
	public function edit2Prog($arg) {
		// Validazione formale del campo
		$this->_formalValidation($arg);
		// Converto nel formato interno
		$prog = $this->exec($this->edit2ProgHandle, $arg);
		// Valido il campo convertito
		$this->validate($prog);
		
		return $prog;
	}
	public function prog2Edit($arg) {
		return $this->exec($this->prog2EditHandle, $arg);
	}
	public function prog2Read($arg) {
		return $this->exec($this->prog2ReadHandle, $arg);
	}
	protected function validate($arg) {
		foreach ($this->validateHandles as $validateHandle) {
			$this->exec($validateHandle, $arg);
		}
	}
}
?>