<?php
function smarty_function_sort_control($params, &$smarty) {
	static $count = array();
	
	$vars = $smarty->getTemplateVars();
	
	$type = system\Utils::getParam('type', $params, array(
		'default' => 'ASC', 
		'options' => array('ASC', 'DESC'))
	);
	$path = system\Utils::getParam('path', $params, array('required' => true));
	$prefix = system\Utils::getParam('prefix', $params, array('default' => '', 'suffix' => '_'));
	
	$count[$vars['system']['requestId']] = 
		(!\array_key_exists($vars['system']['requestId'], $count))
		? $count[$vars["private"]["requestId"]] + 1
		: 1;

	list($formId, $formName, $output) = system\view\Panels::getForm($smarty);
	
	$id = 'system-' . $vars['system']['requestId'] . '-sort-control-' . $count[$vars['system']['requestId']];
	$class = 'system-sort-control system-sort-' . $type;
	$onclick = "ciderbit.sort({formId: '" . $formId . "', path: '" . $path . "', type: '" . $type . "', prefix: '" . $prefix . "'});";
	
	$output .= '<button id="' . $id . '" class="' . $class . '" onclick="' . $onclick . '">' . \system\Lang::translate('Sort @type', array('type' => $type)) . '</button>';
	return $output;
}
?>