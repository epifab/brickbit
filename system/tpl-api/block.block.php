<?php
function smarty_block_block($params, $content, &$smarty, &$repeat) {
	static $blocks = array();
	
	if ($repeat) {
		// Neither to load a url nor to nest a block should be allowed when already in a block
		Blocks::getInstance()->openBlock();
	} else {
		list($formId, $output) = smarty_block_block_form($smarty);
		
		if (!\array_key_exists($formId, $blocks)) {
			$blocks[$formId] = 1;
		} else {
			$blocks[$formId]++;
		}
		$blockId = 'system-block-' . $vars['system']['requestId'] . '-' . $blocks[$formId];
		///// formName <=> blockClass
		$blockClass = 'system-block system-block-' . $vars['system']['requestId'];
		
		$content = '<div id="' . $blockId . '" class="' . $blockClass . '">' . $content . '</div>';
		
		Blocks::getInstance()->closeBlock($name, $classes, $content);
		
		return $content;
	}
}

function smarty_block_block_form($smarty) {
	static $formIds = array();
	
	$vars = $smarty->getTemplateVars();

	if (array_key_exists($vars['system']['requestId'], $formIds)) {
		return $formIds[$vars['system']['requestId']];
	}
	else {
		$formId = 'system-block-form-' . $vars['system']['requestId'];
		$formName = 'system-block-' . $vars['system']['requestId'];
		
		$formIds[$vars['system']['requestId']] = array($formId, $formName, '');
	
		$form =
			'<form class="system-block-form" id="' . $formId . '" name="' . $formName . '" method="post" action="' . $vars['system']['component']['url'] .'">'
			// forzo le risposte ad avere gli stessi ID per il form e per i contenuti
			. '<input type="hidden" name="system_request_id" value="' . $vars['system']['requestId'] . '"/>';
		
		foreach ($vars['system']['component']['request'] as $key => $value) {
			if ($key == 'system_request_id') {
				continue;
			}
			$args = array();
			system\Utils::arg2Input($args, $key, $value);
			foreach ($args as $k => $v) {
				$form .= '<input type="hidden" name="' . \system\Utils::escape($k, '"') . '" value="' . \system\Utils::escape($k, '"') . '"/>';
			}
		}
		$form .= '</form>';
		
		// the form code is returned only once
		return array($formId, $formName, $form);
	}
}
?>