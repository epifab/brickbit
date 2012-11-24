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
			\system\logic\Module::run($componentName);
			
//			$component = \system\logic\Module::getComponent($name);
//			if (\system\logic\Module::$component) {
//				\system\logic\Module::run($name);
//			} else {
//				$_REQUEST["url"] = $name;
//				\system\logic\Module::run("Page");
//			}
		}
	}
//	require "404.php";
}
?>