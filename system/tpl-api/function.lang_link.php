<?php
function smarty_function_lang_link($params, Smarty_Internal_Data &$smarty) {
	$id = \system\Utils::getParam('id', $params, array('required' => true));
	return \system\Lang::langLink($id);
}
?>