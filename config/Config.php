<?php
namespace config;

require 'system/exceptions/BaseException.php';
require 'system/exceptions/Error.php';
require 'system/exceptions/InternalError.php';

/**
 * @return Config
 */
function settings() {
  return Config::getInstance();
}

/**
 * Config class for the ciderbit application
 * ciderbit PHP Generator 0.1 - Auto generated code
 * @author episoft
 */
class Config {
  // DBMS supportati
  const DBMS_MYSQL = 1;
  const DBMS_MSSQL = 2;
  
  /**
   * @var Config
   */
  private static $instance;
  
  private $data = array(
    // data access
    "DB_USER" => "",
    "DB_PASS" => "",
    "DB_HOST" => "",
    "DB_NAME" => "",
    "DB_TYPE" => "",
     
    "RECORD_MODE_LOGS" => true,

    // languages
    "LANGUAGES" => array(
      "en",
      "it",
      "de",
    ),
    "DEFAULT_LANG" => "en",
    
    "THEMES" => array(
      "standard",
      "ciderbit"  
    ),
    "DEFAULT_THEME" => 'ciderbit',
     
    // links
    "BASE_DIR" => "/",
    
    "IP_ADDRESS" => "",
    
    "SITE_TITLE" => "CIDERbit",
    "SITE_SUBTITLE" => "CiderBit | OO MVC PHP Framework",
     
    "DEFAULT_PAGE_TITLE" => "CiderBit",
    
    "EMAIL_INFO" => "",
    "EMAIL_WEBMASTER" => "",
    
    "RECORD_MODE_LOGGED" => true,
    "DEBUG_MODE" => true,
     
    "TPL_CACHE" => true,
    "TPL_CACHE_DIR" => "temp/tpl_cache",
    
    "CORE_CACHE" => true,
  );

  private $dataTest = array(
    "DB_HOST" => "localhost",
    "DB_USER" => "root",
    "DB_PASS" => "",
    "DB_NAME" => "cider",
    "DB_TYPE" => Config::DBMS_MYSQL,
     
    "DEBUG_MODE" => true,
    "CORE_CACHE" => false,
  );
  
  private $dataProd = array(
    "DB_HOST" => "62.149.150.138",
    "DB_USER" => "Sql487621",
    "DB_PASS" => "aead487a",
    "DB_NAME" => "Sql487621_4",
    "DB_TYPE" => Config::DBMS_MYSQL,
  );
  
  /**
   * @return Config
   */
  public static function getInstance() {
    if (\is_null(self::$instance)) {
      self::$instance = new self();
    }
    return self::$instance;
  }

  private function __construct() {
    $this->data["IP_ADDRESS"] = \system\utils\HTMLHelpers::getIpAddress();
    $this->data["PRODUCTION"] = $this->data["IP_ADDRESS"] != "127.0.0.1";
    if ($this->data["PRODUCTION"]) {
      $dataExtension = $this->dataProd;
    } else {
      $dataExtension = $this->dataTest;
    }
    
    foreach ($dataExtension as $k => $v) {
      $this->data[$k] = $v;
    }
  }
  
  public function __get($name) {
    switch ($name) {
      case "SITE_ADDRESS":
        return $this->DOMAIN . $this->data["BASE_DIR"];
        break;
      
      case "BASE_DIR_ABS":
        return dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR;
        break;
      
      case "DOMAIN":
        return $_SERVER["HTTP_HOST"];
        break;
      
      default:
        if (\array_key_exists($name, $this->data)) {
          return $this->data[$name];
        }
        throw new \system\exceptions\InternalError('No entry @name in config', array('@name' => $name));
        break;
    }
  }
  
  public static function exceptions(\Exception $ex) {
    echo '<h3>' . $ex->getMessage() . '</h3>';
//    var_dump($ex);
//    echo \system\utils\Utils::backtraceInfo();
    if ($ex instanceof \system\exceptions\Error) {
      echo '<div>' . $ex->getDetails() . '</div>';
      echo '<div>' . \system\utils\Utils::backtraceInfo($ex->getTrace()) . '</div>';
    }
//    try {
//      echo \system\utils\Utils::backtraceInfo();
//    } catch (\Exception $e) {
//      echo $e->getMessage();
//    }
    die();
  }
  
  public static function errors($code, $description, $file, $line) {
    $level = 0;
    switch ($code) {
      case E_NOTICE:
      case E_USER_NOTICE:
        $level = \system\LOG_NOTICE;
        break;
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
    
    \system\Main::invokeMethodAll('watchdog', '<p><strong>@description</strong><p>File: @file, line: @line</p>', array(
      '@description' => $description, 
      '@file' => $file, 
      '@line' => $line
    ), $level);

//    if ($level == 'error') {
//      echo '<h1>' . $description . '</h1>';
//      echo '<p>File: ' . $file . ' Line: ' . $line . '</p>';
//      echo \system\utils\Utils::backtraceInfo();
//      die();
//    }
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

//if (\array_key_exists(\session_name(), $_REQUEST)) {
//  @\session_id($_REQUEST[\session_name()]);
//}

\set_error_handler('\config\Config::errors');
\set_exception_handler('\config\Config::exceptions');
\spl_autoload_register('\config\Config::autoload');

\system\utils\Lang::setLang(\strpos($_SERVER["HTTP_HOST"], ".")
  ? substr($_SERVER["HTTP_HOST"], 0, \strpos($_SERVER["HTTP_HOST"], ".")) 
  : $_SERVER["HTTP_HOST"]
);

$domains = \array_reverse(\explode('.', $_SERVER['HTTP_HOST']));
if (\count($domains) >= 3) {
  \session_set_cookie_params(0, '/', '.' . $domains[1] . '.' . $domains[0]);
//  \ini_set('session.cookie_domain', '.' . $domains[1] . '.' . $domains[0]);
}
