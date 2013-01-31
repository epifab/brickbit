<?php
namespace system\view;

class Template {
	private $templatePath;
	private $vars;
	private $api;
	
	public function __construct($tplPath, $vars) {
		$this->tplPath = $tplPath;
		$this->vars = $vars;
		$this->api = Api::getInstance();
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
		$this->resetVars();
		while ($___k = $this->getVarKey()) {
			$$___k = $this->getVarValue();
			$this->nextVar();
		}
		unset($___k);
		@include $this->templatePath;
	}
}
?>