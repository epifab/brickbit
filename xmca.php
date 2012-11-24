<?php
require_once "config/Config.php";

$aliases = system\Utils::get("system_aliases", array());

if (array_key_exists($_SERVER["REQUEST_URI"], $aliases)) {
	$component = new $aliases[$_SERVER["REQUEST_URI"]]();
	$component->process();
}

else {
	$m = array();
	$regex = '/(?:\/([a-zA-Z0-9_-]+)+)\.([a-zA-Z]+)(?:[#?].*)?$/';
	if (preg_match($regex, $_SERVER["REQUEST_URI"], $m)) {
		list(,$componentName,$ext) = $m;

		if ($ext == \config\settings()->COMPONENT_EXTENSION) {
			if (!\system\logic\Module::run($componentName)) {
				$_REQUEST["url"] = $componentName;
				\system\logic\Module::run("Page");
			}
		}
	}
}
?>