<?php
function smarty_function_load($params, Smarty_Internal_Data &$smarty) {
	$url = \system\Utils::getParam('url', $params, array('required' => true));
	$args = \system\Utils::getParam('args', $params, array('default' => array()));
	if (\system\Main::checkAccess($url)) {
		\system\Main::run($url, $args);
	}
}
?>