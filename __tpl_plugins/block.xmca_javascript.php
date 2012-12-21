<?php
function smarty_block_xmca_javascript($args, $content, Smarty_Internal_Data &$smarty, &$repeat) {
	if (!$repeat) {
		system\Utils::addJsCode($content, $smarty);
	}
}
?>