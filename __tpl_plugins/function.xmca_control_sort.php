<?php
function smarty_function_xmca_control_sort($args, &$smarty) {
	static $count = array();
	
	$vars = $smarty->getTemplateVars();
	
	if (!array_key_exists("type", $args) || $args["type"] == "asc") {
		$type = "asc";
	} else if ($args["type"] == "desc") {
		$type = "desc";
	} else {
		throw new system\InternalErrorException("Parametro type non valido");
	}
	
	if (!array_key_exists("path", $args) || !is_string($args["path"])) {
		throw new system\InternalErrorException("Parametro path non valido");
	}

	$path = $args["path"];
	
	if (!array_key_exists($vars["private"]["requestId"], $count)) {
		$count[$vars["private"]["requestId"]] = 1;
	} else {
		$count[$vars["private"]["requestId"]]++;
	}

	$prefix = !array_key_exists("prefix", $args) ? "" : $args["prefix"];

	$formId = $vars["private"]["formId"];
	$ctrlId = "xmca_" . $vars["private"]["requestId"] . "_sort_ctrl_" . $count[$vars["private"]["requestId"]];

	return "<button id=\"$ctrlId\" class=\"xmca_control $type\" onclick=\"xmca.sort({formId: '$formId', path: '$path', type: '$type', prefix: '$prefix'});\">Sort $type</button>";
}
?>