<?php
require_once 'lib/cb.php';
require_once 'lib/kint-master/Kint.class.php';

require_once 'config/Config.php';
require_once 'system/shortcuts.php';

$session = \system\session\Session::getInstance();
//if ($session->expire_time < \time()) {
//	$session->destruct();
//}

\system\Main::run($_SERVER['REQUEST_URI']);

$session->commit();
