<?php
function smarty_modifier_t($string, $args=array()) {
	return system\Lang::translate($string, $args);
}
?>