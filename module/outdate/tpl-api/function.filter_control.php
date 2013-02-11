<?php
function smarty_function_filter_control($params, &$smarty) {
	static $count = array();
	
	$vars = $smarty->getTemplateVars();

	$type = system\Utils::getParam('type', $params, array(
		'default' => 'STARTS',
		'options' => array('=', '<', '>', '<=', '>=', 'CONTAINS', 'STARTS', 'ENDS')
	));
	$path = system\Utils::getParam('path', $params, array('required' => true));
	$prefix = system\Utils::getParam('prefix', $params, array('default' => '', 'suffix' => '_'));
//	$lazy = system\Utils::getParam('lazy', $params, array('options' => array(true, false), 'default' => false));
	$size = system\Utils::getParam('size', $params, array('default' => 10));
	
	$count[$vars['system']['requestId']] = 
		(!\array_key_exists($vars['system']['requestId'], $count))
		? $count[$vars["private"]["requestId"]] + 1
		: 1;

	list($formId, , $output) = system\view\Panels::getForm($smarty);
	
	$id = "system-" . $vars["private"]["requestId"] . "-filter-control-" . $count[$vars["private"]["requestId"]];
	$class = 'system-filter-control';
	$onkeyup = "ciderbit.filter({formId: '$formId', path: '$path', rop: '$type', ctrlId: '$ctrlId', prefix: '$prefix'});";
	
	$output .= '<input class="' . $class . '" name="" size="' . $size . '" id="' . $id . '" type="text" value="' . @$_REQUEST["filters"][$ctrlId]["value"] . '" onkeyup="' . $onkeyup . '"/>';
	return $output;
}
?>