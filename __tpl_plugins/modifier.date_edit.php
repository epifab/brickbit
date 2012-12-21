<?php
function smarty_modifier_date_edit($x, $y=true) {
	if ($x === null) {
		return "";
	}
	return \system\model\MetaDate::stdProg2Edit($x);
}
?>