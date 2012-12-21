<?php
function smarty_block_xmca_read_form($args, $content, &$smarty, $repeat) {
	
	if (!$repeat) {
	
		$vars = $smarty->getTemplateVars();

		$formId = $vars["private"]["formId"];
		$contId = $vars["private"]["contId"];
		
	}
}

function arg2Input(&$results, $prefix, $value) {
	if (is_object($value)) {
//		$s = serialize($value);
//		$prefix = "xmca_objects[" . $prefix . "]";
//		$results[$prefix] = $s;
//		return;
	} else if (!is_array($value)) {
		$results[$prefix] = $value;
	} else {
		foreach ($value as $k => $v) {
			arg2Input($results, $prefix . "[" . $k . "]", $v);
		}
	}
}
?>