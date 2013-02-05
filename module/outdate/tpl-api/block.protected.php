<?php
function smarty_block_protected($params, $content, &$smarty, $repeat) {
	if (!$repeat) {
		$url = \system\Utils::getParam('url', $params, array('required' => true));
		$args = \system\Utils::getParam('args', $params, array('default' => array()));
		\system\Main::checkAccess($url, $args) ? $content : '';
	}
}
?>