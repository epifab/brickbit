<?php
function smarty_function_xmca_control_paging($args, &$smarty) {
	if (!array_key_exists("source", $args)) {
		throw new system\InternalErrorException("Parametro source non trasmesso o non valido");
	}
	$source = $args["source"];

	if (!$source instanceof \system\model\RecordsetBuilderInterface) {
		if (is_array($source)) {
			if (count($source) == 0) {
				return '';
			}
			$recordset = $source[0];
			if (!$recordset instanceof \system\model\RecordsetInterface) {
				throw new system\InternalErrorException("Parametro source non valido");
			}
			$source = $recordset->getBuilder();
		} else if ($source instanceof \system\model\RecordsetInterface) {
			$source = $source->getBuilder();
		} else {
			throw new system\InternalErrorException("Parametro source non valido");
		}
	}
	
	$size = $source->getLimit()->getLimit();
	
	$page = $source->getLimit()->getOffset() / $size;

	$vars = $smarty->getTemplateVars();
	$formId = $vars["private"]["formId"];

	$numPages = $source->countPages($size);
	if ($numPages == 1) {
		return '';
	}
	$r = '<ul class="paging">';
	for ($i = 0; $i < $numPages; $i++) {
		if ($i == $page) {
//			$r .= '<li><button>' . ($i+1) . '</button></li>';
//			$r .= '<li>' . ($i+1) . '</li>';
		} else {
			$r .= '<li><button onclick="xmca.paging(\'' . $formId . '\', ' . $i . ')" class="xmca_control">' . ($i+1) . '</button></li>';
		}
	}
	$r .= '</ul>';
	return $r;
}
?>