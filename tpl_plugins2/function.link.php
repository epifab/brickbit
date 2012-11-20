<?php
function smarty_function_link($args, $smarty) {
	$basePath = \config\settings()->BASE_PATH;
	
	if (!\array_key_exists("file", $args)) {
		throw new \system\InternalErrorException();
	}
	
	if (\array_key_exists("base", $args)) {
		switch ($args["base"]) {
			case "theme":
				$basePath .= $smarty->getThemePath();
				break;
			
			case "module":
				$basePath .= "module/";
				break;
		}
	}
}
?>