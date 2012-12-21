<?php
function smarty_block_protected($params, $content, &$smarty, $repeat) {
	if (!$repeat) {
		$url = \system\Utils::getParam($params, 'url', array('required' => true));
		$args = \system\Utils::getParam($params, 'args', array('default' => array()));
		\system\logic\Module::checkAccess($url, $args) ? $content : '';
	}
}
?>