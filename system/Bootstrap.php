<?php
namespace system;

// Shortcuts
const LOG_ERROR = 4;
const LOG_WARNING = 3;
const LOG_NOTICE = 2;
const LOG_DEBUG = 1;

const RESPONSE_TYPE_READ = 'READ';
const RESPONSE_TYPE_FORM = 'FORM';
const RESPONSE_TYPE_NOTIFY = 'NOTIFY';
const RESPONSE_TYPE_ERROR = 'ERROR';

const DBMS_MYSQL = 'mysql';
const DBMS_MSSQL = 'sqlserver';

class Bootstrap {
  public static function init() {
//    \set_error_handler('\system\Bootstrap::errorHandler');
//    \set_exception_handler('\system\Bootstrap::exceptionHandler');
    \spl_autoload_register('\system\Bootstrap::autoload');
    
    SystemApi::onInit('onInit');
  }
  
  public static function exceptionHandler(\Exception $ex) {
    return self::errorHandler(E_ERROR, $ex->getMessage(), $ex->getFile(), $ex->getLine());
  }
  
  public static function errorHandler($code, $description, $file, $line) {
    $level = 0;
    switch ($code) {
      case E_NOTICE:
      case E_USER_NOTICE:
      case E_CORE_WARNING:
      case E_DEPRECATED:
      case E_USER_DEPRECATED:
      case E_USER_WARNING:
      case E_WARNING:
        $level = \system\LOG_WARNING;
        break;
      case E_COMPILE_ERROR:
      case E_CORE_ERROR:
      case E_ERROR:
      case E_COMPILE_WARNING:
      case E_USER_ERROR:
      case E_PARSE:
      case E_RECOVERABLE_ERROR:
      case E_STRICT:
      case E_ALL:
        $level = \system\LOG_ERROR;
        break;
    }
    
    SystemApi::watchdog(
      'system', 
      '<p><strong>@description</strong><p>File: @file, line: @line</p>', array(
        '@description' => $description, 
        '@file' => $file,
        '@line' => $line
      ), $level
    );
    
    if ($level == \system\LOG_ERROR) {
      die();
    }
  }
  
  // caricamento automatico di classi del framework ciderbit
  public static function autoload($name) {
    $path = \str_replace('\\', DIRECTORY_SEPARATOR, $name);
    if (\file_exists($path . ".php")) {
      require_once $path . ".php";
    }
//    if (count($namespaces) == 3 && $namespaces[0] == "ciderbit") {
//      switch ($namespaces[1]) {
//        case "model":
//        case "controller":
//        case "system":
//          $fileName = $namespaces[1] . "/" . $namespaces[2] . ".php";
//          if (\file_exists($fileName)) {
//            require_once $namespaces[1] . "/" . $namespaces[2] . ".php";
//          }
//          break;
//        case "lang":
//          $fileName = "lang/";
//          break;
//      }
//    }
  }
}