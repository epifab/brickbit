<?php
function smarty_function_xmca_nested_component($args, Smarty_Internal_Data &$smarty) {
	if (!array_key_exists("component", $args) || !is_string($args["component"])) {
		throw new system\InternalErrorException("Parametro component non valido");
	}
	if (!array_key_exists("args", $args) || !is_array($args["args"])) {
		throw new system\InternalErrorException("Parametro args non valido");
	}
	if (!array_key_exists("prefix", $args) || !is_string($args["prefix"])) {
		throw new system\InternalErrorException("Parametro prefix non valido");
	}

	\system\logic\Module::run($args["component"], $args["args"], $args["prefix"]);
}
?>