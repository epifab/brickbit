<?php
function smarty_block_xmca_restricted_area($args, $content, &$smarty, $repeat) {
	if (!array_key_exists("component", $args)) {
		throw new \system\InternalErrorException("Componente non trasmesso");
	}
	if (!array_key_exists("args", $args)) {
		$args["args"] = array();
	}
	
	if (!$repeat) {
		$component = \system\logic\Module::getComponent($args["component"]);
		if (!is_null($component)) {
			$auth = true;
			// Autorizzazione generica componenti
//			$auth = $auth && \system\logic\Component::checkComponentPermission($module, $args["component"]);
			// Autorizzazione specifica componente
			$auth = $auth && call_user_func(array($component["namespace"] . $component["class"], "checkPermission"), $args["args"]);

			return $auth ? $content : '';
		}
	}
}
?>