<?php
function smarty_function_panel_form_name($params, &$smarty) {
	return system\view\Panels::getInstance()->getFormName($smarty);
}
?>	