<?php
function smarty_modifier_xmca_tags_link($x) {
	if (empty($x)) {
		return '';
	}
	
	$s = '';
	foreach ($x as $y) {
		$s = (empty($s) ? '' : $s . ', ') 
			. '<a href="' . \config\settings()->SITE_ADDRESS . 'tags/' . \system\HTMLHelpers::getTagUrl($y->tag->value) . '.html" class="' . $y->tag->rate . ' ' . $y->tag->size . '">' . $y->tag->value . '</a>';
	}
	return $s;
}
?>