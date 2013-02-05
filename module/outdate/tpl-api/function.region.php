<?php
function smarty_function_region($params, \Smarty_Internal_Template &$smarty) {
	$vars = $smarty->getTemplateVars();
	
	$region = \system\Utils::getParam('name', $params, array('required' => true));
	
	if (\array_key_exists($region, $vars['system']['templates']['regions'])) {
		\asort($vars['system']['templates']['regions'][$region]);
		foreach ($vars['system']['templates']['regions'][$region] as $templates) {
			foreach ($templates as $tpl) {
				$smarty->display($tpl);
			}
		}
	}
}
?>