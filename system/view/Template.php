<?php
namespace system\view;

class Template {
	public static $templates = array();
	private $templatePath;
	private $vars;
	private $api;
	
	/**
	 * @return Template
	 */
	public static function current() {
		return current(self::$templates);
	}
	
	public function __construct($tplPath, $vars) {
		$this->templatePath = $tplPath;
		$this->vars = $vars;
		$this->api = Api::getInstance();
	}

	public function getVars() {
		return $this->vars;
	}
	
	public function getVarKey() {
		while (\key($this->vars) && (!\preg_match('/^[a-zA-Z_][a-zA-Z0-9_]*$/',	\key($this->vars)) || \key($this->vars) == 'this')) {
			// skip invalid names
			\next($this->vars);
		}
		return \key($this->vars);
	}
	
	public function getVarValue() {
		return \current($this->vars);
	}
	
	public function nextVar() {
		\next($this->vars);
	}
	
	public function resetVars() {
		\reset($this->vars);
	}
	
	public function render() {
		\array_push(self::$templates, $this);
		$this->resetVars();
		while ($___k = $this->getVarKey()) {
			$$___k = $this->getVarValue();
			$this->nextVar();
		}
		unset($___k);
		include $this->templatePath;
		\array_pop(self::$templates);
	}
}
?>