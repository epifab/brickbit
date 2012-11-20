<?php
function smarty_modifier_lang($x) {
	$args = '$x';
	for ($i = 1; $i < func_num_args(); $i++) {
		$args .= ', func_get_arg(' . $i . ')';
	}
	$translated = '?';
	try {
		eval('$translated = \system\Lang::get(' . $args . ');');
	} catch (\Exception $ex) { }
	return $translated;
}
?>