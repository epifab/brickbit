<?php
namespace system\view;

class TemplateManager implements TemplateManagerInterface {
	private $regions = array();
	private $mainTemplate;
	private $outlineTemplate;
	private $outlineWrapperTemplate;
	
	private $templateDirs = array();
	
	private function getTemplatePath($tpl) {
		if (\is_null($tpl)) {
			return null;
		}
		\system\Main::getTemplatePath($tpl);
//		$tpl = \system\File::stripExtension($tpl) . ".php";
//		foreach ($this->templateDirs as $dir) {
//			if (\file_exists($dir . DIRECTORY_SEPARATOR . $tpl)) {
//				return $dir . DIRECTORY_SEPARATOR . $tpl;
//			}
//		}
//		throw new \system\InternalErrorException(\t('Template <em>@name</em> not found.', array('@name' => \system\File::stripExtension($tpl))));
	}
	
	public function getOutlineWrapperTemplate() {
		return $this->outlineWrapperTemplate;
	}
	
	public function setOutlineWrapperTemplate($tpl) {
		$this->outlineWrapperTemplate = $this->getTemplatePath($tpl);
	}
	
	public function getOutlineTemplate() {
		return $this->outlineTemplate;
	}
	
	public function setOutlineTemplate($tpl) {
		$this->outlineTemplate = $this->getTemplatePath($tpl);
	}
	
	public function getMainTemplate() {
		return $this->mainTemplate;
	}
	
	public function setMainTemplate($tpl) {
		$this->mainTemplate = $this->getTemplatePath($tpl);
	}
	
	public function addTemplate($tpl, $region, $weight=0) {
		if (!\array_key_exists($region, $this->regions)) {
			$this->regions[$region] = array();
		}
		if (!\array_key_exists($weight, $this->regions[$region])) {
			$this->regions[$region][$weight] = array();
		}
		$this->regions[$region][$weight][] = $this->getTemplatePath($tpl);
	}

	public function addTemplateDir($dir) {
		if (\is_array($dir)) {
			foreach ($dir as $d) {
				if (!\is_dir($d)) {
					throw new \system\InternalErrorException(\t('Folder <em>@name</em> not found.', array('@name' => $d)));
				}
				$this->templateDirs[] = $d;
			}
		} else {
			if (!\is_dir($dir)) {
				throw new \system\InternalErrorException(\t('Folder <em>@name</em> not found.', array('@name' => $dir)));
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
		
		$tpl->render();
	}	
}

?>