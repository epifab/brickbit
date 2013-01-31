<?php
require_once "config/Config.php";
require_once "config/shortcuts.php";

\system\logic\Module::run($_SERVER["REQUEST_URI"]);
?>