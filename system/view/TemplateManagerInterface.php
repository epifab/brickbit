<?php
namespace system\view;

interface TemplateManagerInterface {
	public function addTemplate($key, $region, $tpl);
	public function setMainTemplate($tpl);
	public function setOutlineTemplate($tpl);
	public function setOutlineWrapperTemplate($tpl);
	public function addTemplateDir($dir);
}
?>