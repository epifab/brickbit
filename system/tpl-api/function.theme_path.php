<?php
function smarty_function_theme_path($params) {
	return \system\Theme::getThemePath() . \system\Utils::getParam($params, 'url', array('default' => ''));
}
?>