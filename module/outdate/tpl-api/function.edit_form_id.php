<?php
function smarty_function_edit_form_id($params, &$smarty) {
	$vars = $smarty->getTemplateVars();
	return 'system-edit-form-' . $vars['system']['component']['requestId'];
}
?>