<?php
function smarty_function_panel_form_id($params, &$smarty) {
	return system\view\Panels::getInstance()->getFormId($smarty);
}
?>