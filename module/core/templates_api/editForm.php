<?php
namespace \module\core\templates_api;

function editForm($content, $params) {
	if (!$repeat) {
		
		$vars = $smarty->getTemplateVars();
		
		$formId = 'system-edit-form-' . $vars['system']['component']['requestId'];
		
		$form = "\n"
		. '<form id="' . $formId . '" name="' . $formId . '" method="post" enctype="multipart/form-data" action="' . $vars['system']['component']['url'] .'">'
		// forzo le risposte ad avere gli stessi ID per il form e per i contenuti
		. '<input type="hidden" name="system[requestId]" value="' . $vars['system']['component']['requestId'] . '"/>';

		return $form . $content . '</form>';
	}
}
?>