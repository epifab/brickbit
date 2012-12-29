<?php
function smarty_block_panels($params, $content, &$smarty, &$repeat) {
	if ($repeat) {
//		system\view\Panels::getInstance(
//			system\Utils::getParam('name', $params, array('default' => null)),
//			system\Utils::getParam('class', $params, array('default' => null))
//		);
	} else {
		// erase content
		$content = '';
		
		$panels = system\view\Panels::getInstance()->getPanels();
		
		foreach ($panels as $panel) {
			$content .= $panel;
		}
		
		return $content;
	}
}
?>