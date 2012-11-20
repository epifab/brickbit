<?php
function smarty_modifier_filename($x) {
	return \system\File::getSafeFilename($x);
}
?>