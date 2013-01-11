<?php
function smarty_function_de_form_error($params, Smarty_Internal_Data &$smarty) {
	$path = \system\Utils::getParam('path', $params, array('required' => true));
	$vars = $smarty->getTemplateVars();
	if (\array_key_exists('errors', $vars) && \array_key_exists($path, $vars['errors'])) {
		return '<div class="de-error">' . $vars['errors'][$path] . '</div>';
	}
}
?>