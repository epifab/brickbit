<?php
function smarty_function_module_path($params) {
	return 
		\config\settings()->BASE_DIR
		. \system\logic\Module::getPath(\system\Utils::getParam('module', $params, array('required' => true)))
		. \system\Utils::getParam('url', $params, array('default' => ''));
}
?>