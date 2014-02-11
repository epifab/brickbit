<?php
namespace system\view;

class TemplateManager implements TemplateManagerInterface {
	private $regions = array();
	private $mainTemplate;
	private $outlineTemplate;
	private $outlineWrapperTemplate;
	
	private $templateDirs = array();
	
	public function getOutlineWrapperTemplate() {
		return $this->outlineWrapperTemplate;
	}
	
	public function setOutlineWrapperTemplate($tpl) {
		$this->outlineWrapperTemplate = $tpl;
	}
	
	public function getOutlineTemplate() {
		return $this->outlineTemplate;
	}
	
	public function setOutlineTemplate($tpl) {
		$this->outlineTemplate = $tpl;
	}
	
	public function getMainTemplate() {
		return $this->mainTemplate;
	}
	
	public function setMainTemplate($tpl) {
		$this->mainTemplate = $tpl;
	}
	
	public function addTemplate($tpl, $region, $weight=0) {
		if (!\array_key_exists($region, $this->regions)) {
			$this->regions[$region] = array();
		}
		if (!\array_key_exists($weight, $this->regions[$region])) {
			$this->regions[$region][$weight] = array();
		}
		$this->regions[$region][$weight][] = $tpl;
	}

	public function addTemplateDir($dir) {
		if (\is_array($dir)) {
			foreach ($dir as $d) {
				if (!\is_dir($d)) {
					throw new \system\exceptions\InternalError('Folder <em>@name</em> not found.', array('@name' => $d));
				}
				$this->templateDirs[] = $d;
			}
		} else {
			if (!\is_dir($dir)) {
				throw new \system\exceptions\InternalError('Folder <em>@name</em> not found.', array('@name' => $dir));
			}
			$this->templateDirs[] = $dir;
		}
	}
	
	public function process($datamodel) {
		$datamodel['system']['templates'] = array(
			'main' => $this->mainTemplate,
			'outline' => $this->outlineTemplate,
			'outline-wrapper' => $this->outlineWrapperTemplate,
			'regions' => $this->regions
		);
		
		if ($this->outlineWrapperTemplate) {
			$tpl = new Template($this->outlineWrapperTemplate, $datamodel);
		} else if ($this->outlineTemplate) {
			$tpl = new Template($this->outlineTemplate, $datamodel);
		} else if ($this->mainTemplate) {
			$tpl = new Template($this->mainTemplate, $datamodel);
		} else {
			return;
		}
		
		try {
			\ob_start();
			$tpl->render();
			\ob_flush();
		} catch (\Exception $ex) {
			while (\ob_get_clean()); // erase output buffer
			throw $ex;
		}
	}	
}

