<?php
function smarty_function_de_submit_control($params, Smarty_Internal_Data &$smarty) {
//	$path = \system\Utils::getParam('path', $params, array('required' => true));
	$vars = $smarty->getTemplateVars();
	if ($vars['system']['component']['requestType'] != 'MAIN') {
		return '<div class="de-controls"><input class="de-control" type="submit" value="' . \system\Lang::translate('Save') . '"/></div>';
	}
}
?>