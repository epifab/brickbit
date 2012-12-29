<?php
namespace system;

require "smarty/Smarty.class.php";

class TemplateManager extends \Smarty {
	private $regions = array();
	private $mainTemplate;
	private $outlineTemplate;
	private $outlineTemplateWrapper;
	
	public function getOutlineTemplate() {
		return $this->outlineTemplate;
	}
	
	public function setOutlineTemplate($tpl) {
		$this->outlineTemplate = empty($tpl) ? null : \system\File::stripExtension($tpl) . ".tpl";
	}
	
	public function getOutlineTemplateWrapper() {
		return $this->outlineTemplateWrapper;
	}
	
	public function setOutlineTemplateWrapper($tpl) {
		$this->outlineTemplateWrapper = empty($tpl) ? null : \system\File::stripExtension($tpl) . ".tpl";
	}
	
	public function getMainTemplate() {
		return $this->mainTemplate;
	}
	
	public function setMainTemplate($tpl) {
		$this->mainTemplate = empty($tpl) ? null : \system\File::stripExtension($tpl) . ".tpl";
	}
	
	public function addTemplate($template, $region, $weight=0) {
		if (!\array_key_exists($region, $this->regions)) {
			$this->regions[$region] = array();
		}
		if (!\array_key_exists($weight, $this->regions[$region])) {
			$this->regions[$region][$weight] = array();
		}
		$this->regions[$region][$weight][] = \system\File::stripExtension($template) . '.tpl';
	}
	
	public function __construct() {
		parent::__construct();
		$this->addPluginsDir(array(
			"plugins",
			"system/tpl-api"
		));
		$this->setCompileDir(\config\settings()->TPL_CACHE_DIR);
	}
	
	public function process($datamodel) {
		$datamodel['system']['templates'] = array(
			'main' => $this->mainTemplate,
			'outline' => $this->outlineTemplate,
			'outline-wrapper' => $this->outlineTemplateWrapper,
			'regions' => $this->regions
		);
		foreach ($datamodel as $k => $v) {
			$this->assign($k, $v);
		}
		if ($this->outlineTemplateWrapper) {
			$this->display($this->outlineTemplateWrapper);
		} else if ($this->outlineTemplate) {
			$this->display($this->outlineTemplate);
		} else if ($this->mainTemplate) {
			$this->display($this->mainTemplate);
		}
	}
}
?>