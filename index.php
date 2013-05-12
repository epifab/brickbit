<?php
require_once 'config/Config.php';
require_once 'lib/cb.php';
require_once 'lib/drupal.php';

\system\Main::run($_SERVER["REQUEST_URI"]);
?>