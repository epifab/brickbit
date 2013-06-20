<?php
namespace config;

require 'system/error/Error.php';
require 'system/error/InternalError.php';

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
			"dark"	
		),
		"DEFAULT_THEME" => "standard",
		 
		// links
		"BASE_DIR" => "/",
		
		"IP_ADDRESS" => "",
		
		"SITE_TITLE" => "Cydersoft.it",
		"SITE_SUBTITLE" => "Cydersoft | OO MVC PHP Framework",
		 
		"DEFAULT_PAGE_TITLE" => "Cydersoft",
		
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
		 
		"SITE_TITLE" => "Episoft.it | DEV",
		 
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
				throw new \system\error\InternalError('No entry @name in config', array('@name' => $name));
				break;
		}
	}
	
	
	// caricamento automatico di classi del framework ciderbit
	public static function autoload($name) {
		$path = \str_replace('\\', DIRECTORY_SEPARATOR, $name);
		if (\file_exists($path . ".php")) {
			require_once $path . ".php";
		}
//		if (count($namespaces) == 3 && $namespaces[0] == "ciderbit") {
//			switch ($namespaces[1]) {
//				case "model":
//				case "controller":
//				case "system":
//					$fileName = $namespaces[1] . "/" . $namespaces[2] . ".php";
//					if (\file_exists($fileName)) {
//						require_once $namespaces[1] . "/" . $namespaces[2] . ".php";
//					}
//					break;
//				case "lang":
//					$fileName = "lang/";
//					break;
//			}
//		}
	}
}

//if (\array_key_exists(\session_name(), $_REQUEST)) {
//	@\session_id($_REQUEST[\session_name()]);
//}

\spl_autoload_register('\config\Config::autoload');

\system\utils\Lang::setLang(\strpos($_SERVER["HTTP_HOST"], ".")
	? substr($_SERVER["HTTP_HOST"], 0, \strpos($_SERVER["HTTP_HOST"], ".")) 
	: $_SERVER["HTTP_HOST"]
);

$domains = \array_reverse(\explode('.', $_SERVER['HTTP_HOST']));
if (\count($domains) >= 3) {
	\session_set_cookie_params(0, '/', '.' . $domains[1] . '.' . $domains[0]);
//	\ini_set('session.cookie_domain', '.' . $domains[1] . '.' . $domains[0]);
}

@\session_start();
