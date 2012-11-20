<?php
function smarty_block_xmca_edit_form($args, $content, &$smarty, $repeat) {
	
	if (!$repeat) {
	
		$vars = $smarty->getTemplateVars();

		$formId = $vars["private"]["formId"];
		
		$form = "\n"
		. '<form id="' . $formId . '" name="' . $formId . '" method="post" enctype="multipart/form-data" action="' . $vars["private"]["componentAddr"] .'">'
		// gestione ajax
		. (system\HTMLHelpers::isAjaxRequest() ? '<input type="hidden" name="xmca_ajax_request" value="1"/>' : '')
		// forzo le risposte ad avere gli stessi ID per il form e per i contenuti
		. '<input type="hidden" name="xmca_request_id" value="' . $vars["private"]["requestId"] . '"/>';

		return $form . $content . '</form>';
	}
}
?>