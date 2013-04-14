<?php
function t($sentence, $args=null) {
	return \system\Lang::translate($sentence, $args);
}
function setlog($key, $message, $type=\system\Utils::LOG_INFO) {
	return \system\Utils::log($key, $message, $type);
}
?>