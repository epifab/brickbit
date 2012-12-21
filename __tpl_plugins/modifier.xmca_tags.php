<?php
function smarty_modifier_xmca_tags($x) {
	if (empty($x)) {
		return '';
	}
	
	$s = '';
	foreach ($x as $y) {
		$s = (empty($s) ? '' : $s . ', ') . $y->tag->value;
	}
	return $s;
}
?>