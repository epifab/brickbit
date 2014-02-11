<?php
namespace system\view;

class Template {
	public static $templates = array();
	private $templatePath;
	private $vars;
	private $api;
	
	/**
	 * @return \system\view\Api
	 */
	public static function getApi() {
		return self::current()->api;
	}
	
	/**
	 * @return Template
	 */
	public static function current() {
		return \end(self::$templates);
	}
	
	public function __construct($tplName, $vars) {
		$this->templatePath = \system\Main::getTemplate($tplName);
		if (!$this->templatePath) {
			die();
			throw new \system\exceptions\InternalError('Template @name not found.', array('@name' => $tplName));
		}
		$this->vars = $vars;
		$this->api = Api::getInstance();
	}

	public function getVars() {
		\reset($this->vars);
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
