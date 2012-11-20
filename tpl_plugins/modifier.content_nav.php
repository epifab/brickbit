<?php
function smarty_modifier_content_nav($x) {
	$contents[] = $x;
	while ($x->content) {
		$x = $x->content;
		$contents[] = $x;
	}
	array_reverse($contents);
	$result = "";
	$first = true;
	foreach ($contents as $content) {
		$first ? $first = false : $result .= ' | ';
		$result .= '<a href="Content/' . $content->url . '">' . $content->title . '</a>';
	}
	return $result;
}
?>