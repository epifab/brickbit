<?php
function smarty_function_load($params, Smarty_Internal_Data &$smarty) {
	$url = \system\Utils::getParam($params, 'component', array('required' => true));
	$args = \system\Utils::getParam($params, 'args', array('default' => array()));
	if (\system\logic\Module::checkAccess($url)) {
		\system\logic\Module::run($url, $args);
	}
}
?>