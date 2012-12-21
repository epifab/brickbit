<?php
function smarty_modifier_age($x) {
	if ($x == null) {
		return '';
	}
	$m = (int)date("n") - (int)date("n", $x);
	$y = (int)date("Y") - (int)date("Y", $x);
	$d = (int)date("j") - (int)date("j", $x);

	if ($m < 0 || ($m == 0 && $d < 0)) {
		return $y - 1;
	} else {
		return $y;
	}
}
?>