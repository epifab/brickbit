<?php
namespace system\logic;

use system\model\RecordsetBuilder;
use system\model\Recordset;
use system\model\RecordsetInterface;

use system\HTMLHelpers;
use system\Lang;
use system\Login;
use system\Theme;
use system\TemplateManager;
use system\Utils;

use system\AuthorizationException;
use system\InternalErrorException;
use system\ValidationException;

abstract class Component {
	//  components stack
	private static $components = array();
	private static $componentsCount = 0;
	
	private $name = null;
	private $module = null;
	private $alias = null;
	private $action = null;
	private $url = null;
	private $urlArgs = null;
	private $requestData = null;
	private $requestId = null;
	private $requestType = null;
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
	public static function access($class, $action, $urlArgs, $request, $user) {
		if (\method_exists($class, "access" . $action)) {
			return (bool)\call_user_func(array($class, "access" . $action), $urlArgs, $request, $user);
		} 
//		else if (\method_exists($class, "access")) {
//			return (bool)\call_user_func(array($class, "access"), $action, $urlArgs, $request, $user);
//		}
		return true;
	}
	
	protected function onInit() {
	}
	
	protected function onError($exception) {

		\system\HTMLHelpers::makeErrorPage($this->tplManager, $this->datamodel, $exception, $this->getExecutionTime());

//			if ($this->nested) {
//				$pageOutput = \ob_get_clean();
//			} else {
//				$pageOutput = \ob_get_flush();
//			}

//		try {
//			$id = \module\core\model\ciderbitLog::saveLog($this->name, $pageOutput);
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
	
	public function setData($key, $value) {
		if (\is_array($key)) {
			$dm =& $this->datamodel;
			\reset($key);
			do {
				$k1 = current($key);
				$k2 = next($key);
				if (!$k2) {
					$dm[$k1] = $value;
				}	else if (!\array_key_exists($k1, $dm)) {
					$dm[$k1] = array();
				}
				$dm =& $dm[$k1];
			} while ($k2);
		}
	}
	
	public function getDataModel() {
		return $this->datamodel;
	}
	
	public function __construct($name, $module, $action, $url, $urlArgs, $request=null) {
		$this->name = $name;
		$this->module = $module;
		$this->action = $action;
		$this->url = $url;
		$this->urlArgs = $urlArgs;
		$this->requestData = \is_null($request) ? $_REQUEST : (array)$request;
		$this->loadRequestId();
		$this->loadRequestType();
		$this->nested = \is_null(self::getCurrentComponent());
		$this->alias = $this->name;
		if (!\is_null(self::getCurrentComponent())) {
			$this->alias = self::getCurrentComponent()->alias . '__' . $this->alias;
		}
		$this->initView();
	}
	
	public function getRequestId() {
		return $this->requestId;
	}
	
	public function getRequestType() {
		return $this->requestType;
	}
	
	private function loadRequestId() {
		if (!\array_key_exists('system', $this->requestData) 
			 || !\array_key_exists('requestId', $this->requestData['system'])
			 || !\preg_match('/^[a-zA-Z0-9_-]+$/', $this->requestData['system']['requestId'])) {
			
			if (!\array_key_exists('system', $_SESSION)) {
				$_SESSION['system'] = array();
			}
			if (!\array_key_exists('requestIds', $_SESSION['system'])) {
				$_SESSION['system']['requestIds'] = array();
			}
			$_SESSION['system']['requestIds'][$this->name] =
				(\array_key_exists($this->name, $_SESSION['system']['requestIds']))
					? $_SESSION['system']['requestIds'][$this->name] + 1
					: 1;
			
			$this->requestId = $this->name . $_SESSION['system']['requestIds'][$this->name];
		} else {
			$this->requestId = $this->requestData['system']['requestId'];
		}
	}
	
	private function loadRequestType() {
		if (\array_key_exists('system', $this->requestData)) {
			if (\array_key_exists('requestType', $this->requestData['system'])) {
				switch ((string)$this->requestData['system']['requestType']) {
					case 'PAGE':
					case 'MAIN':
					case 'PAGE-PANELS':
					case 'MAIN-PANELS':
						$this->requestType = $this->requestData['system']['requestType'];
						break;
				}
			}
		}
		if (!$this->requestType) {
			$this->requestType = 
				HTMLHelpers::isAjaxRequest()
					? 'MAIN'
					: 'PAGE';
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
				'requestId' => $this->requestId,
				'requestType' => $this->requestType,
				'requestData' => $this->requestData,
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
				'defaultLang' => \config\settings()->DEFAULT_LANG,
			);
		}
		return $settings;
	}
	
	private function initView() {
		$this->tplManager = \system\Main::getTemplateManager();
		
		$mainComponent = self::getMainComponent();
		if (\is_null($mainComponent)) {
			$mainComponent = $this;
		}
		
		$this->datamodel = array(
			'system' => array(
				'component' => $this->getComponentInfo(),
				'mainComponent' => $mainComponent->getComponentInfo(),
				// default response type
				'responseType' => self::RESPONSE_TYPE_READ,
				'ajax' => HTMLHelpers::isAjaxRequest(),
				'ipAddress' => HTMLHelpers::getIpAddress(),
				'lang' => Lang::getLang(),
				'langs' => \config\settings()->LANGUAGES,
				'theme' => Theme::getTheme(),
				'themes' => \config\settings()->THEMES,
//				'panelName' => \system\Utils::getParam('system_panel_name', $this->requestData, array('default' => 'main')),
//				'panelClass' => \system\Utils::getParam('system_panel_class', $this->requestData, array('default' => null)),
			),
			'user' => Login::getLoggedUser(),
			'website' => $this->getWebsiteInfo(),
			'page' => array(
				'title' => '',
				'url' => $mainComponent->url,
				'meta' => array(),
				'js' => array(),
				'css' => array(),
			)
		);
		
		\system\view\Panels::getInstance(
			\system\Utils::getParam('system_panel_name', $this->requestData, array('default' => 'main')),
			\system\Utils::getParam('system_panel_class', $this->requestData, array('default' => null))
		);
	}
	
	public function addJs($js) {
		if (!\in_array($js, $this->datamodel['page']['js'])) {
			$this->datamodel['page']['js'][] = $js;
		}
	}
	
	public function addCss($css) {
		if (!\in_array($css, $this->datamodel['page']['css'])) {
			$this->datamodel['page']['css'][] = $css;
		}
	}
	
	public function addMeta($meta) {
		$this->datamodel['page']['meta'][] = $meta;
	}
	
	public function setMainTemplate($template) {
		$this->tplManager->setMainTemplate($template);
	}
	
	public function setOutlineTemplate($template) {
		$this->tplManager->setOutlineTemplate($template);
	}
	
	public function addTemplate($template, $region, $weight=0) {
		$this->tplManager->addTemplate($template, $region, $weight);
	}
	
	public function setOutlineWrapperTemplate($template) {
		$this->tplManager->setOutlineWrapperTemplate($template);
	}
	
	private function setResponseType($responseType) {
		$this->datamodel['system']['responseType'] = $responseType;
	}
	
	public function setPageTitle($pageTitle, $adding=false) {
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
	
	final public function getUrlArg($index) {
		return \array_key_exists($index, $this->urlArgs) ? $this->urlArgs[$index] : null;
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
			// init event
			$this->onInit();
			
			// checking permission
			if (!self::access(\get_class($this), $this->action, $this->urlArgs, $this->requestData, \system\Login::getLoggedUser())) {
				throw new AuthorizationException(\t('Sorry, you are not authorized to access this resource.'));
			}
			
			// onProcess event
			if (\method_exists($this, "run" . $this->action)) {
				$responseType = \call_user_func(array($this, "run" . $this->action));
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
					throw new InternalErrorException(Lang::translate("Invalid action <em>@action</em> response in module <em>@module</em> component <em>@component</em>.", array('@action' => $this->action, '@component' => $this->name, '@module' => $this->module)));
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