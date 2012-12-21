<?php
namespace system\logic;

use system\model\RecordsetBuilder;
use system\model\Recordset;
use system\model\RecordsetInterface;

use system\model\MetaBoolean;
use system\model\MetaDate;
use system\model\MetaDateTime;
use system\model\MetaInteger;
use system\model\MetaOptions;
use system\model\MetaReal;
use system\model\MetaString;
use system\model\MetaTime;
use system\model\MetaVirtual;

use system\HTMLHelpers;
use system\Lang;
use system\Login;
use system\Theme;
use system\TemplateManager;
use system\Utils;

use system\AuthorizationException;
use system\ConversionException;
use system\InternalErrorException;
use system\ValidationException;

abstract class Component {
	//  components stack
	private static $components = array();
	private static $componentsCount = 0;
	
	private $name = null;
	private $alias = null;
	private $action = null;
	private $url = null;
	private $urlArgs = null;
	private $requestData = null;
	private $nested = false;
	
	protected $microTime = null;
	
	/**
	 * @var TemplateManager
	 */
	private $tplManager;
	/**
	 * @var mixed[]
	 */
	protected $datamodel = array();
	
	/**
	 * Invio di una form per inserimento o modifica
	 */
	const RESPONSE_TYPE_FORM = "FORM";
	/**
	 * Messaggio di notifica per inserimento, modifica o cancellazione completata
	 */
	const RESPONSE_TYPE_NOTIFY = "NOTIFY";
	/**
	 * Invio di dati
	 */
	const RESPONSE_TYPE_READ = "READ";
	/**
	 * Invio di dati relativi ad un errore
	 */
	const RESPONSE_TYPE_ERROR = "ERROR";
		
	public function getRequestTime() {
		return \round($this->microTime, 0);
	}
	
	public function getExecutionTime() {
		return \round(\microtime(true) - $this->microTime, 3);
	}
	
	/**
	 * @return Component[]
	 */
	public static function getComponents() {
		return self::$components;
	}
	/**
	 * @return Component
	 */
	public static function getMainComponent() {
		return (self::$componentsCount > 0) ? self::$components[0] : null;
	}
	/**
	 * @return Component
	 */
	public static function getCurrentComponent() {
		return (self::$componentsCount > 0) ? self::$components[self::$componentsCount] : null;
	}

	private static function pushComponent(Component $component) {
		self::$components[self::$componentsCount++] = $component;
	}
	/**
	 * @return Component
	 */
	private static function popComponent() {
		return (self::$componentsCount > 0) ? self::$components[--self::$componentsCount] : null;
	}

	/**
	 * Check user access for component/action(args)
	 * Can be overriden by component extending classes declaring
	 *   accessYourAction($urlArgs, $userId)
	 *   access($action, $urlArgs, $userId)
	 * @param string $action action
	 * @param string $urlArgs url arguments
	 * @return boolean true if 
	 */
	public static function access($component, $action, $urlArgs, $request, $userId=null) {
		if (\is_null($userId)) { 
			$userId = Login::getLoggedUserId();
		}
		if (\method_exists($component, "access" . $action)) {
			return (bool)\call_user_func(array($component, "access" . $action), $urlArgs, $request, $userId);
		} 
		else if (\method_exists($component, "access")) {
			return (bool)\call_user_func(array($component, "access"), $action, $urlArgs, $request, $userId);
		} 
		return true;
	}
	
	protected function init() {
	}
	
	protected function onError($exception) {
		$this->setResponseType(self::RESPONSE_TYPE_ERROR);
		
		HTMLHelpers::makeErrorPage($this->tplManager, $this->datamodel, $exception, $this->getExecutionTime());

//			if ($this->nested) {
//				$pageOutput = \ob_get_clean();
//			} else {
//				$pageOutput = \ob_get_flush();
//			}

//		try {
//			$id = \module\core\model\XmcaLog::saveLog($this->name, $pageOutput);
//		} catch (\Exception $ex) {
//			echo "<h1>" . $ex->getMessage() . "</h1>";
//			if ($ex instanceof DataLayerException) {
//				echo $ex->getHtmlMessage();
//			}
//		}
	}
	
	/**
	 * Can be overriden by
	 *   onProcessMyAction
	 *   onProcess 
	 */
	protected function onProcess() {
		return self::RESPONSE_TYPE_READ;
	}
	
	public function __construct($name, $module, $action, $url, $urlArgs, $request=null) {
		$this->name = $name;
		$this->module = $module;
		$this->action = $action;
		$this->url = $url;
		$this->urlArgs = $urlArgs;
		$this->requestData = \is_null($request) ? $_REQUEST : (array)$request;
		$this->nested = \is_null(self::getCurrentComponent());
		$this->alias = $this->name;
		if (!\is_null(self::getMainComponent())) {
			'__' . $this->alias .= self::getMainComponent()->alias;
		}
	}
	
	private function generateNewRequestId() {
		if (!\array_key_exists('system', $_SESSION)) {
			$_SESSION['system'] = array();
			$_SESSION['system']['requestIds'] = array();
		} else if (!\array_key_exists('requestIds', $_SESSION['system'])) {
			$_SESSION['system']['requestIds'] = array();
		}
		if (!\array_key_exists($this->name, $_SESSION['system']['requestIds'])) {
			$_SESSION['system']['requestIds'][$this->name] = 1;
		} else {
			$_SESSION['system']['requestIds'][$this->name]++;
		}
	}
	
	protected function getCurrentRequestId() {
		if (!\array_key_exists('system', $_SESSION) || !\array_key_exists('requestIds', $_SESSION['system']) || !\array_key_exists($this->name, $_SESSION['system']['requestIds'])) {
			$this->generateRequestId();
		}
		return $this->name . $_SESSION['system']['requestIds'][$this->name];
	}
	
	protected function getRequestId() {
		if (!\array_key_exists("xmca_request_id", $this->requestData)) {
			$this->generateNewRequestId();
			return $this->getCurrentRequestId();
		} else {
			return $this->requestData["xmca_request_id"];
		}
	}
	
	private function getComponentInfo() {
		static $info = null;
		if (\is_null($info)) {
			$info = array(
				'name' => $this->name,
				'module' => $this->getModule(),
				'action' => $this->action,
				'url' => $this->url,
				'urlArgs' => $this->urlArgs,
				'requestData' => $this->requestData,
				'requestId' => $this->getRequestId(),
				'nested' => $this->nested,
				'alias' => $this->alias
			);
		}
		return $info;
	}
	
	private static function getWebsiteInfo() {
		static $settings = null;
		if (\is_null($settings)) {
			$settings = array(
				'title' => \config\settings()->SITE_TITLE,
				'subtitle' => \config\settings()->SITE_SUBTITLE,
				'domain' => \config\settings()->DOMAIN,
				'base' => \config\settings()->SITE_ADDRESS,
			);
		}
		return $settings;
	}
	
	private function initView() {
		$this->tplManager = Module::getTemplateManager();
		
		$this->datamodel = array(
			'system' => array(
				'component' => $this->getComponentInfo(),
				'mainComponent' => self::getMainComponent()->getComponentInfo(),
				'mainTemplate' => null,
				'templates' => array(),
				// default response type
				'responseType' => self::RESPONSE_TYPE_READ,
				'ajax' => HTMLHelpers::isAjaxRequest(),
				'ipAddress' => HTMLHelpers::getIpAddress(),
				'lang' => Lang::getLang(),
				'langs' => \config\settings()->LANGUAGES,
				'theme' => Theme::getTheme(),
				'themes' => \config\settings()->THEMES
			),
			'user' => Login::getLoggedUser(),
			'website' => $this->getWebsiteInfo(),
			'page' => array(
				'title' => '',
				'url' => self::getMainComponent()->url,
				'meta' => array(),
				'js' => array(),
				'css' => array(),
			)
		);
	}
	
	protected function addJs($js) {
		if (!\in_array($js, $this->datamodel['page']['js'])) {
			$this->datamodel['page']['js'][] = $js;
		}
	}
	
	protected function addCss($css) {
		if (!\in_array($css, $this->datamodel['page']['css'])) {
			$this->datamodel['page']['css'][] = $css;
		}
	}
	
	protected function addMeta($meta) {
		$this->datamodel['page']['meta'][] = $meta;
	}
	
	protected function setMainTemplate($template) {
		$this->datamodel['system']['mainTemplate'] = $template;
	}
	
	protected function setOutlineTemplate($template) {
		$this->tplManager->setOutlineTemplate($template);
	}
	
	private function setResponseType($responseType) {
		$this->datamodel['system']['responseType'] = $responseType;
	}
	
	protected function addTemplate($key, $template) {
		$this->datamodel['system']['templates'][$key] = $template;
	}
	
	protected function setPageTitle($pageTitle, $adding=false) {
		$this->datamodel['page']['title'] = ($adding && !empty($this->datamodel['page']['title']) ? $this->datamodel['system']['title'] . ' | ' : '') . $pageTitle;
	}
	
	final public function getModule() {
		return $this->module;
	}
	
	final public function getName() {
		return $this->name;
	}
	
	final public function getAlias() {
		return $this->alias;
	}

	final public function getAction() {
		return $this->action;
	}
	
	final public function getUrl() {
		return $this->url;
	}
	
	final public function getUrlArgs() {
		return $this->urlArgs;
	}
	
	final public function getRequestData() {
		return $this->requestData;
	}
	
	final public function isNested() {
		return $this->nested;
	}

	///<editor-fold defaultstate="collapsed" desc="Metodi standard per inizializzazione di clausole">
	protected function loadStdFilters(RecordsetBuilder $builder, $prefix="") {
		$index = $prefix . "filters";
		
		$lastLop = null;

		if (\array_key_exists($index, $this->requestData) && \is_array($this->requestData[$index])) {
			$filterGroup = null;
			
			foreach ($this->requestData[$index] as $filter) {

				if (empty($filter) || !\is_array($filter) || !\array_key_exists("path", $filter) || empty($filter["path"])) {
					continue;
				}
				
				$path = $filter["path"];
				
				if (!\array_key_exists("rop", $filter)) {
					$rop = "=";
				} else {
					$rop = $filter["rop"];
				}
				if (!\array_key_exists("value", $filter)) {
					$value = "";
				} else {
					$value = $filter["value"];
				}
				if (!\array_key_exists("lop", $filter) || empty($filter["lop"])) {
					$lastLop = "AND";
				} else {
					$lastLop = $filter["lop"];
				}
				
				$metaType = $builder->searchMetaType($path);
				if (\is_null($metaType)) {
					throw new InternalErrorException("Parametro per il filtro non valido (campo $path non trovato)");
				}
				$metaType instanceof MetaType;
				try {
					$progValue = $metaType->edit2Prog($value);
				} catch (system\ValidationException $ex) {
					throw $ex;
				}

				$filter = new FilterClause($metaType, $rop, $progValue);

				if (\is_null($filterGroup)) {
					$filterGroup = new FilterClauseGroup($filter);
				} else {
					$filterGroup->addClauses($lastLop, $filter);
				}
			}
			
			$builder->setFilter($filterGroup);
		}
	}
	
	protected function loadStdSorts(RecordsetBuilder $builder, $prefix="") {
		$index = $prefix . "sorts";

		$sorts = null;
		if (\array_key_exists($index, $this->requestData) && \is_array($this->requestData[$index])) {
			foreach ($this->requestData[$index] as $sort) {
				
				if (empty($sort)) {
					continue;
				}
				
				$args = @\explode("|", $sort);
				if (\count($args) != 2) {
					throw new InternalErrorException("Parametro per l'ordinamento non valido");
				}
				$metaType = $builder->searchMetaType($args[0]);
				if (\is_null($metaType)) {
					throw new InternalErrorException("Parametro per il filtro non valido (campo '{$args[0]}' non trovato)");
				}

				$sc = new SortClause($metaType, $args[1]);
				if (\is_null($sorts)) {
					$sorts = new SortClauseGroup($sc);
				} else {
					$sorts->addClauses($sc);
				}
			}
			$builder->setSort($sorts);
		}
	}
	
	protected function loadStdLimits(RecordsetBuilder $builder, $pageSize=100, $prefix="") {
		$index = $prefix . "paging";
		
		if (!\array_key_exists($index, $this->requestData) || !\is_array($this->requestData[$index])) {
			$this->requestData[$index] = array();
			$this->requestData[$index]["size"] = $pageSize;
			$this->requestData[$index]["page"] = 0;
		} else {
			if (!\array_key_exists("size", $this->requestData[$index])) {
				$this->requestData[$index]["size"] = $pageSize;
			}
			if (!\array_key_exists("page", $this->requestData[$index])) {
				$this->requestData[$index]["page"] = 0;
			}
		}
		
		if (!\is_numeric($this->requestData[$index]["page"]) || ((int)$this->requestData[$index]["page"]) < 0) {
			throw new InternalErrorException("Parametro per la paginazione non valido");
		}
		if (!\is_numeric($this->requestData[$index]["size"]) || ((int)$this->requestData[$index]["size"]) < 0) {
			throw new InternalErrorException("Parametro per la paginazione non valido");
		}
		
		if ($this->requestData[$index]["size"] == 0) {
			return;
		} else {
			$page = intval($this->requestData[$index]["page"]);
			$size = intval($this->requestData[$index]["size"]);
			$builder->setLimit(new LimitClause($size, $page*$size));
		}
	}
	///</editor-fold>

	final public function process() {
		
		// adding the component to the stack
		self::pushComponent($this);
		
		// initializing request time
		$this->microTime = \microtime(true);

		// initializing the output buffer
		\ob_start();
			
		$pageOutput = "";

		try {
			$this->initView();
			
			// checking permission
			if (!self::access($this->name, $this->action, $this->urlArgs, $this->requestData)) {
				throw new AuthorizationException("Utente non autorizzato");
			}
			
			// init event
			$this->init();
			
			// onProcess event
			if (\method_exists($this, "on" . $this->action)) {
				$responseType = \call_user_func(array($this, "on" . $this->action));
			} else {
				$responseType = $this->onProcess();
			}

			switch ($responseType) {
				case self::RESPONSE_TYPE_READ:
				case self::RESPONSE_TYPE_NOTIFY:
				case self::RESPONSE_TYPE_FORM:
				case self::RESPONSE_TYPE_ERROR:
					$this->setResponseType($responseType);
					break;
				case null:
					break;
				default:
					throw new InternalErrorException("Metodo onProcess non valido per il componente " . $this->name);
			}

			if (!\is_null($responseType)) {
				// Adding the output to the buffer
				$this->tplManager->process($this->datamodel);
			}

			if (!$this->nested) {
				// Displays the output
				$pageOutput = \ob_get_flush();
			}
		}
		
		catch (\Exception $ex) {
			// Uncaught exception
			
			// Cleaning the buffer
//			while (\ob_get_clean());

			// onError event
			$this->onError($ex);
		}
		
		self::popComponent();
	}
}
?>