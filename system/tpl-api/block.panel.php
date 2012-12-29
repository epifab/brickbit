<?php
function smarty_block_panel($params, $content, &$smarty, &$repeat) {
	if ($repeat) {
		// Neither to load a url nor to nest a panel should be allowed when already in a panel
		system\view\Panels::getInstance()->openPanel();
	} else {
		list(, $formName, $output) = system\view\Panels::getForm($smarty);
		
		$panelName = system\Utils::getParam('name', $params, array('required' => true));
		$panelClass = 
			'system-panel system-panel-' . $panelName . ' ' . $formName
			. system\Utils::getParam('class', $params, array('prefix' => ' ', 'default' => ''));
		$vars = $smarty->getTemplateVars();
		
		$panelId = 'system-panel-' . $vars['system']['component']['requestId'] . '-' . $panelName;
		
		$content = '<div id="' . $panelId . '" class="' . $panelClass . '">' . $content . '</div>';
		
		system\view\Panels::getInstance()->closePanel($panelId, $panelName, $panelClass, $content);
		
		return $output . $content;
	}
}
?>