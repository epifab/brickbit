<?php
namespace ciderbit;

require_once "install/Config.php";
require_once "install/Install.php";

use module\core\controller\Install;

$componentClassName = new Install();
$controller = new $componentClassName();
$controller->process();
?>