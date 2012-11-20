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
		list(,$name,$ext) = $m;

		if ($ext == \config\settings()->COMPONENT_EXTENSION) {
			$component = \system\logic\Module::getComponent($name);
			if ($component) {
				\system\logic\Module::run($name);
//				call_user_func(array($modules[0]["namespace"] . $modules[0]["class"], "run"), $name);
			} else {
				$_REQUEST["url"] = $name;
				\system\logic\Module::run("Page");
			}
		}
	}
//	require "404.php";
}
?>