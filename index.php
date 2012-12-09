<?php
require_once "config/Config.php";

\system\logic\Module::run($_SERVER["REQUEST_URI"]);
?>