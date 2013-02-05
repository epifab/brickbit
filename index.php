<?php
require_once "config/Config.php";
require_once "config/shortcuts.php";

\system\Main::run($_SERVER["REQUEST_URI"]);
?>