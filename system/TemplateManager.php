<?php
namespace system;

require "smarty/Smarty.class.php";

class TemplateManager extends \Smarty {
//	private static $instance;
	
	private $theme;
	
	private $mainTemplate;
	private $outlineTemplate;
	
	public function getOutlineTemplate() {
		return $this->outlineTemplate;
	}
	
	public function setOutlineTemplate($tpl) {
		$this->outlineTemplate = empty($tpl) ? null : \system\File::stripExtension($tpl) . ".tpl";
	}
	
	public function getMainTemplate() {
		return $this->mainTemplate;
	}
	
	public function setMainTemplate($tpl) {
		$this->mainTemplate = empty($tpl) ? null : \system\File::stripExtension($tpl) . ".tpl";
	}
	
//	public static function getInstance() {
//		if (\is_null(self::$instance)) {
//			self::$instance = new self();
//		}
//		return self::$instance;
//	}
	
	public function __construct() {
		parent::__construct();
		$this->addPluginsDir(array(
			"plugins",
			"tpl_plugins"
		));
		$this->setCompileDir(\config\settings()->TPL_CACHE_DIR);
	}
	
	public function getTheme() {
		if (empty($theme)) {
			return \config\settings()->DEFAULT_THEME;
		}
		return $this->theme;
	}
	
	public function setTheme($theme) {
		$this->theme = $theme;
	}
	
	public function getThemePath($subfolder=null) {
		return "theme/" . $this->getTheme() . "/" . (empty($subfolder) ? "" : $subfolder . "/");
	}
	
	public function process($datamodel) {
		if ($this->outlineTemplate) {
			$datamodel["private"]["mainTemplate"] = $this->mainTemplate;
		}
		foreach ($datamodel as $k => $v) {
			$this->assign($k, $v);
		}
		if ($this->outlineTemplate) {
			$this->display($this->outlineTemplate);
		} else {
			$this->display($this->mainTemplate);
		}
	}
}
?>