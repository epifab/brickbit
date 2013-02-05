<?php
function smarty_block_javascript($args, $content, Smarty_Internal_Data &$smarty, &$repeat) {
	if (!$repeat) {
		system\Utils::addJsCode($content, $smarty);
	}
}
?>