<?php
function smarty_function_theme_path($params) {
	return \system\Theme::getThemePath() . \system\Utils::getParam('url', $params, array('default' => ''));
}
?>