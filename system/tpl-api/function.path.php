<?php
function smarty_function_path($params) {
	return \config\settings()->BASE_DIR . \system\Utils::getParam('url', $params, array('default' => ''));
}
?>