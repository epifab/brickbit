<?php
namespace system\logic;

abstract class Component {
	//  components stack
	private static $components = array();
	private static $componentsCount = 0;
	
	protected $alias = null;
	protected $action = null;
	protected $url = null;
	protected $urlArgs = null;
	protected $request = null;
	protected $nested = false;
	
	protected $microTime = null;
	
	/**
	 * @var \system\TemplateManager
	 */
	protected $tplManager;
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
	public static function access($component, $action, $urlArgs, $userId=null) {
		if (\is_null($userId)) { 
			$userId = \system\Login::getLoggedUserId();
		}
		if (\method_exists($component, "access" . $action)) {
			return (bool)\call_user_func(array($component, "access" . $action), $urlArgs, $userId);
		} 
		else if (\method_exists($component, "access")) {
			return (bool)\call_user_func(array($component, "access"), $action, $urlArgs, $userId);
		} 
		return true;
	}
	
	protected function onLoad() {
	}
	
	protected function onError($exception) {
		$this->setResponseType(self::RESPONSE_TYPE_ERROR);
		
		\system\HTMLHelpers::makeErrorPage($this->tplManager, $this->datamodel, $exception, $this->getExecutionTime());

//			if ($this->nested) {
//				$pageOutput = \ob_get_clean();
//			} else {
//				$pageOutput = \ob_get_flush();
//			}

//		try {
//			$id = \module\core\model\XmcaLog::saveLog($this->getName(), $pageOutput);
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
	
	public function __construct($action, $url, $urlArgs, $request=null) {
		$this->action = $action;
		$this->url = $url;
		$this->urlArgs = $urlArgs;
		$this->request = \is_null($request) ? $_REQUEST : (array)$request;
		$this->nested = \is_null(self::getCurrentComponent());
		$this->alias = $this->getName();
		if (!\is_null(self::getMainComponent())) {
			'__' . $this->alias .= self::getMainComponent()->alias;
		}
	}
	
	public function getModule() {
		list(,$module) = explode("\\",\get_class($this));
		return $module;
	}
	
	protected function generateNewRequestId() {
		if (!\array_key_exists("xmca", $_SESSION)) {
			$_SESSION["xmca"] = array();
			$_SESSION["xmca"]["requestIds"] = array();
		} else if (!\array_key_exists("requestIds", $_SESSION["xmca"])) {
			$_SESSION["xmca"]["requestIds"] = array();
		}
		if (!\array_key_exists($this->getName(), $_SESSION["xmca"]["requestIds"])) {
			$_SESSION["xmca"]["requestIds"][$this->getName()] = 1;
		} else {
			$_SESSION["xmca"]["requestIds"][$this->getName()]++;
		}
	}
	
	protected function getCurrentRequestId() {
		if (!\array_key_exists("xmca", $_SESSION) || !\array_key_exists("requestIds", $_SESSION["xmca"]) || !\array_key_exists($this->getName(), $_SESSION["xmca"]["requestIds"])) {
			$this->generateRequestId();
		}
		return $this->getName() . $_SESSION["xmca"]["requestIds"][$this->getName()];
	}
	
	protected function getRequestId() {
		if (!\array_key_exists("xmca_request_id", $this->request)) {
			$this->generateNewRequestId();
			return $this->getCurrentRequestId();
		} else {
			return $this->request["xmca_request_id"];
		}
	}
	
	private function getComponentInfo() {
		static $info = null;
		if (\is_null($info)) {
			$info = array(
				'name' => $this->getName(),
				'module' => $this->getModule(),
				'action' => $this->action,
				'url' => $this->url,
				'urlArgs' => $this->urlArgs,
				'request' => $this->request,
				'requestId' => $this->getRequestId(),
				'nested' => $this->nested,
			);
		}
		return $info;
	}
	
	private static function getGeneralInfo() {
		static $settings = null;
		if (\is_null($settings)) {
			$settings = array(
				'website' => array(
					'title' => \config\settings()->SITE_TITLE,
					'subtitle' => \config\settings()->SITE_SUBTITLE,
					'domain' => \config\settings()->DOMAIN,
					'base' => \config\settings()->SITE_ADDRESS,
				),
				'lang' => \system\Lang::getLang(),
				'langs' => \config\settings()->LANGUAGES,
				'theme' => \system\Theme::getTheme(),
				'themes' => \config\settings()->THEMES,
				'ip' => \system\HTMLHelpers::getIpAddress(),
				'ajax' => \system\HTMLHelpers::isAjaxRequest()
			);
		}
	}
	
	private function initView() {
		$this->tplManager = Module::getTemplateManager();
		
		$this->datamodel = array(
			'system' => array(
				'component' => $this->getComponentInfo(),
				'mainComponent' => self::getMainComponent()->getComponentInfo(),
				'info' => self::getGeneralInfo(),
				'mainTemplate' => null,
				'templates' => array(),
				// default response type
				'responseType' => self::RESPONSE_TYPE_READ,
			),
			'page' => array(
				'title' => '',
				'url' => self::getMainComponent()->url,
				'metatag' => array(),
				'js' => array(),
				'css' => array(),
			)
		);
	}
	
	private function setResponseType($responseType) {
		$this->datamodel['system']['responseType'] = $responseType;
	}
	
	private function setMainTemplate($template) {
		$this->datamodel['system']['mainTemplate'] = $template;
	}
	
	protected function addTemplate($key, $template) {
		$this->datamodel['system']['templates'][$key] = $template;
	}
	
	protected function setPageTitle($pageTitle, $adding=false) {
		$this->datamodel['page']['title'] = ($adding && !empty($this->datamodel['page']['title']) ? $this->datamodel['system']['title'] . ' | ' : '') . $pageTitle;
	}
	
	public function getName() {
		return \get_class($this);
	}

	public function getAction() {
		return $this->action;
	}
	
	public function getUrl() {
		return $this->url;
	}
	
	// outlines di default
	protected function getOutline() {
		return null;
//		return $this->nested ? null : (\system\HTMLHelpers::isAjaxRequest() ? "layout/outline-ajax" : "layout/outline");
	}
	
	abstract function getTemplate();

	///<editor-fold defaultstate="collapsed" desc="Metodi standard per inizializzazione di clausole">
	protected function loadStdFilters(\system\model\RecordsetBuilder $builder, $prefix="") {
		$index = $prefix . "filters";
		
		$lastLop = null;

		if (\array_key_exists($index, $this->request) && \is_array($this->request[$index])) {
			$filterGroup = null;
			
			foreach ($this->request[$index] as $filter) {

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
	
	protected function loadStdSorts(\system\model\RecordsetBuilder $builder, $prefix="") {
		$index = $prefix . "sorts";

		$sorts = null;
		if (\array_key_exists($index, $this->request) && \is_array($this->request[$index])) {
			foreach ($this->request[$index] as $sort) {
				
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
	
	protected function loadStdLimits(\system\model\RecordsetBuilder $builder, $pageSize=100, $prefix="") {
		$index = $prefix . "paging";
		
		if (!\array_key_exists($index, $this->request) || !\is_array($this->request[$index])) {
			$this->request[$index] = array();
			$this->request[$index]["size"] = $pageSize;
			$this->request[$index]["page"] = 0;
		} else {
			if (!\array_key_exists("size", $this->request[$index])) {
				$this->request[$index]["size"] = $pageSize;
			}
			if (!\array_key_exists("page", $this->request[$index])) {
				$this->request[$index]["page"] = 0;
			}
		}
		
		if (!\is_numeric($this->request[$index]["page"]) || ((int)$this->request[$index]["page"]) < 0) {
			throw new InternalErrorException("Parametro per la paginazione non valido");
		}
		if (!\is_numeric($this->request[$index]["size"]) || ((int)$this->request[$index]["size"]) < 0) {
			throw new InternalErrorException("Parametro per la paginazione non valido");
		}
		
		if ($this->request[$index]["size"] == 0) {
			return;
		} else {
			$page = intval($this->request[$index]["page"]);
			$size = intval($this->request[$index]["size"]);
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
			
			$this->tplManager->setOutlineTemplate($this->getOutline());
			
			// checking permission
			if (!self::access($this->getName(), $this->action, $this->urlArgs)) {
				throw new \system\AuthorizationException("Utente non autorizzato");
			}
			
			// onLoad event
			$this->onLoad();
			
			// onProcess event
			if (\method_exists($this, "onProcess" . $this->action)) {
				$responseType = \call_user_func("onProcess" . $this->action);
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
					throw new \system\InternalErrorException("Metodo onProcess non valido per il componente " . $this->getName());
			}

			if (!\is_null($responseType)) {
				// Loading temapltes
				$this->tplManager->setMainTemplate($this->getTemplate());
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