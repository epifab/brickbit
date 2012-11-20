<?php
function smarty_function_xmca_control_filter($args, &$smarty) {
	static $count = array();
	
	$vars = $smarty->getTemplateVars();

	$type = array_key_exists("type", $args) ? $args["type"] : "STARTS";
	switch ($type) {
		case "=":
		case "<":
		case ">":
		case "<=":
		case ">=":
		case "<>":
		case "CONTAINS":
		case "STARTS":
		case "ENDS":
			break;
		default:
			throw new system\InternalErrorException("Parametro type non valido");
	}
	
	if (!array_key_exists("path", $args) || !is_string($args["path"])) {
		throw new system\InternalErrorException("Parametro path non valido");
	}
	
	$path = $args["path"];
	$prefix = !array_key_exists("prefix", $args) ? "" : $args["prefix"];
	
	if (!array_key_exists($vars["private"]["requestId"], $count)) {
		$count[$vars["private"]["requestId"]] = 1;
	} else {
		$count[$vars["private"]["requestId"]]++;
	}
	
	$formId = $vars["private"]["formId"];
	$ctrlId = "xmca_" . $vars["private"]["requestId"] . "_filter_ctrl_" . $count[$vars["private"]["requestId"]];

	$jsCall = "xmca.filter({formId: '$formId', path: '$path', rop: '$type', ctrlId: '$ctrlId', prefix: '$prefix'});";
	if (array_key_exists("instant", $args) && $args["instant"]) {
		$input = '<input size="10" id="' . $ctrlId . '" type="text" value="' . @$_REQUEST["filters"][$ctrlId]["value"] . '" onkeyup="' . $jsCall . '"/>';
	} else {
		$input = '<input size="10" id="' . $ctrlId . '" type="text" value="' . @$_REQUEST["filters"][$ctrlId]["value"] . '" onchange="' . $jsCall . '"/>'
			  . '<button class="xmca_control search" onclick="' . $jsCall . '">Cerca</button>'
			  . '<button class="xmca_control cancel" onclick="' . "$('#$ctrlId').val(''); $jsCall" . '">Cancella</button>';
	}
	return $input;
}
?>