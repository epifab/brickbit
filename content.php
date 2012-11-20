<?php
namespace xmca;

\config\settings()->MAIN;
require_once "config/Config.php";

$component = new \module\core\controller\Content();
$component->process();
?>