<?php
function smarty_function_sort_control($args, &$smarty) {
	static $count = array();
	
	$vars = $smarty->getTemplateVars();
	
	$type = system\Utils::getParam($args, 'type', array(
		'default' => 'ASC', 
		'options' => array('ASC', 'DESC'))
	);
	$path = system\Utils::getParam($args, 'path', array('required' => true));
	$prefix = system\Utils::getParam($args, 'prefix', array('default' => '', 'suffix' => '_'));
	
	$count[$vars['system']['requestId']] = 
		(!\array_key_exists($vars['system']['requestId'], $count))
		? $count[$vars["private"]["requestId"]] + 1
		: 1;

	list($formId, $formName, $output) = smarty_block_block_form($smarty);
	
	$id = 'system-' . $vars['system']['requestId'] . '-sort-control-' . $count[$vars['system']['requestId']];
	$class = 'system-sort-control system-sort-' . $type;
	$onclick = "xmca.sort({formId: '" . $formId . "', path: '" . $path . "', type: '" . $type . "', prefix: '" . $prefix . "'});";
	
	$output .= '<button id="' . $id . '" class="' . $class . '" onclick="' . $onclick . '">' . \system\Lang::translate('Sort @type', array('type' => $type)) . '</button>';
	return $output;
}
?>