<?php
require_once 'lib/cb.php';

require_once 'system/Bootstrap.php';

use system\Bootstrap;
use system\Main;
use system\session\Session;

// Initialize environment
Bootstrap::init();

// Initialize session
$session = Session::getInstance();

// Runs the main component
Main::run();

$session->commit();
