<?php
function smarty_function_dummy_text($args) {
	$size = array_key_exists("size", $args) ? $args["size"] : 447;
	
	$dummy = "Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.";
	
	while (strlen($dummy) < $size) {
		$dummy .= $dummy;
	}
	
	$dummy = substr($dummy, 0, $size);
	
	return $dummy;
}
?>