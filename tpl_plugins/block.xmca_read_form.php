<?php
function smarty_block_xmca_read_form($args, $content, &$smarty, $repeat) {
	
	if (!$repeat) {
	
		$vars = $smarty->getTemplateVars();

		$formId = $vars["private"]["formId"];
		$contId = $vars["private"]["contId"];
		
		$form = "\n"
		. '<form class="xmca_reload_form" id="' . $formId . '" name="' . $contId . '" method="post" action="' . $vars["private"]["componentAddr"] .'">'
		// forzo le risposte ad avere gli stessi ID per il form e per i contenuti
		. '<input type="hidden" name="xmca_request_id" value="' . $vars["private"]["requestId"] . '"/>';

		foreach ($vars["private"]["request"] as $key => $value) {
			
			if ($key == "xmca_request_id"	) {
				continue;
			}

			$r = array();
			arg2Input($r, $key, $value);

			foreach ($r as $k => $v) {
				$form .= '<input type="hidden" name="' . $k . '" value="' . $v . '"/>';
			}
		}
		return $form . $content . '</form>';
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