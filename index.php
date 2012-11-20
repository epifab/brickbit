<?php
namespace xmca;

require_once "config/Config.php";

//$componentClassName = "xmca\\controller\\" . \config\settings()->SITE_ADDRESS;
$_REQUEST["url"] = "Home";
$controller = new \module\core\controller\Page();
$controller->process();
?>