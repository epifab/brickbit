<?php
function smarty_function_pager($params, &$smarty) {
	$source = system\Utils::getParam('source', $params, array('required' => true));

	if (!($source instanceof \system\model\RecordsetBuilderInterface)) {
		if (is_array($source)) {
			if (count($source) == 0) {
				return '';
			}
			$recordset = $source[0];
			if (!$recordset instanceof \system\model\RecordsetInterface) {
				throw new system\InternalErrorException(\system\Lang::translate('Invalid @name parameter', array('@name' => 'source')));
			}
			$source = $recordset->getBuilder();
		} else if ($source instanceof \system\model\RecordsetInterface) {
			$source = $source->getBuilder();
		} else {
			throw new system\InternalErrorException(\system\Lang::translate('Invalid @name parameter', array('@name' => 'source')));
		}
	}
	
	$size = $source->getLimit()->getLimit();
	$page = $source->getLimit()->getOffset() / $size;

	list($formId, $formName, $output) = system\view\Panels::getForm();

	$numPages = $source->countPages($size);
	if ($numPages == 1) {
		return '';
	}
	$output .= '<ul class="system-pager">';
	for ($i = 0; $i < $numPages; $i++) {
		if ($i == $page) {
//			$r .= '<li><button>' . ($i+1) . '</button></li>';
//			$r .= '<li>' . ($i+1) . '</li>';
		} else {
			$output .= '<li><button onclick="ciderbit.paging(\'' . $formId . '\', ' . $i . ')">' . ($i+1) . '</button></li>';
		}
	}
	$output .= '</ul>';
	return $output;
}
?>