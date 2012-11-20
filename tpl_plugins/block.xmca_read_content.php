<?php
function smarty_block_xmca_read_content($args, $content, &$smarty, &$repeat) {
	if (!$repeat) {
		$vars = $smarty->getTemplateVars();

		$contId = $vars["private"]["contId"];

		if (!array_key_exists("element", $args)) {
			$element = "div";
		} else {
			$element = $args["element"];
		}
		return '<' . $element .' id="' . $contId .'" class="xmca_content">' . $content . '</' . $element . '>';
	}
}
?>