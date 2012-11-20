<?php
namespace system\logic;
use system;

abstract class Component {
	protected $nested = false;
	
	protected $request = null;
	protected $prefix = null;
	
	protected $tplFolder = "view/";
	
	protected $id = 0;
	
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
		
	/**
	 * @var TemplateManager
	 */
	protected $tplManager;
	/**
	 * @var mixed[]
	 */
	protected $datamodel = array();

	public static function checkPermission($args) {
		return true;
	}
	
	abstract protected function getName();

	abstract protected function getTemplate();
	
	abstract protected function onProcess();
	
	public function __construct($request=null, $prefix="") {

		if (\is_null($request)) {
			$this->nested = false;
			$this->request = $_REQUEST;
			$this->prefix = "";
		} else {
			$this->nested = true;
			$this->request = $request;
			$this->prefix = $prefix;
		}
		
		$this->initView();
	}
	
	protected function getId() {
		if ($this->id == 0) {
			$query = "SELECT id FROM xmca_component WHERE name = " . MetaString::stdProg2Db($this->getName());
			$this->id = DataLayerCore::getInstance()->executeScalar($query, __FILE__, __LINE__);
		}
		return $this->id;
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
	
	private function initView() {
		$this->tplManager = new \system\TemplateManager();
//		die(Module::getPath($this->getModule(), "templates"));
		
		$this->tplManager->setTemplateDir (array(
//			Module::getPath($this->getModule(), "templates"),
			$this->tplManager->getThemePath("templates")
		));
		
		$this->datamodel = array();
		$this->datamodel["private"] = array();
		$this->datamodel["private"]["requestTime"] = \time();
		$this->datamodel["private"]["componentName"] = $this->getName();
		$this->datamodel["private"]["componentExt"] = \config\settings()->COMPONENT_EXTENSION;
		$this->datamodel["private"]["componentAddr"] = $this->getName() . "." . \config\settings()->COMPONENT_EXTENSION;
		$this->datamodel["private"]["requestId"] = $this->getRequestId();
		$this->datamodel["private"]["formId"] = "xmca_" . $this->datamodel["private"]["requestId"] . "_form";
		$this->datamodel["private"]["contId"] = "xmca_" . $this->datamodel["private"]["requestId"] . "_cont";
		$this->datamodel["private"]["homeAddr"] = \config\settings()->SITE_ADDRESS . "." . \config\settings()->COMPONENT_EXTENSION;
		$this->datamodel["private"]["siteName"] = \config\settings()->SITE_TITLE;
		$this->datamodel["private"]["siteAddr"] = \config\settings()->SITE_ADDRESS;
		$this->datamodel["private"]["siteDesc"] = \config\settings()->SITE_TITLE;
		$this->datamodel["private"]["themePath"] = $this->tplManager->getThemePath();
		$this->datamodel["private"]["pageTitle"] = \config\settings()->SITE_TITLE;
		$this->datamodel["private"]["self"] = strpos($_SERVER["REQUEST_URI"], "?") ? substr($_SERVER["REQUEST_URI"], 0, strpos($_SERVER["REQUEST_URI"], "?")) : $_SERVER["REQUEST_URI"];
		$this->datamodel["private"]["request"] = $this->request;
		$this->datamodel["private"]["isAjaxRequest"] = \system\HTMLHelpers::isAjaxRequest();
		$this->datamodel["private"]["component"] = $this;
		$this->datamodel["private"]["login"] = \system\Login::getInstance();
		$this->datamodel["private"]["baseHref"] = "/www/";
		$this->datamodel["private"]["defaultLang"] = \config\settings()->DEFAULT_LANG;
		$this->datamodel["private"]["languages"] = \config\settings()->LANGUAGES;
		$this->datamodel["private"]["language"] = \system\Lang::getInstance()->getLangId();
	}
	
	protected function setPageTitle($pageTitle, $adding=false) {
		$this->datamodel["private"]["pageTitle"] = ($adding ? $this->datamodel["private"]["pageTitle"] . " | " : "") . $pageTitle;
	}
	
//	public static function checkComponentPermission($moduleName, $componentName) {
//		$dl = \system\model\DataLayerCore::getInstance();
//		
//		$userId = \system\Login::getLoggedUserId();
//		
//		$query =
//			"SELECT COUNT(*)"
//			. " FROM xmca_component c"
//			. " INNER JOIN xmca_module m"
//			. " WHERE c.name = " . MetaString::stdProg2Db($componentName)
//			. " AND m.name = " . MetaString::stdProg2Db($moduleName);
//		if ($dl->executeScalar($query, __FILE__, __LINE__) == 0) {
//			// Nessun componente trovato nel DB: componente pubblico
//			return true;
//		}
//		
//		else if (empty($userId)) {
//			// Componente trovato nel DB ma utente anonimo: accesso negato
//			return false;
//		}
//		
//		else {
//			// Componente trovato nel DB e utente loggato.
//			// Controllo la corrispondenza con almeno uno dei gruppi di appartenenza dell'utente
//			$query = 
//				"SELECT"
//				. " COUNT(*) AS allowed"
//				. " FROM xmca_user_group xug"
//				. " LEFT JOIN xmca_group_component xgc ON xgc.group_id = xug.group_id"
//				. " LEFT JOIN xmca_component xc ON xc.id = xgc.component_id"
//				. " WHERE"
//				. " xug.user_id = " . MetaInteger::stdProg2Db($userId)
//				. " AND xc.name = " . MetaString::stdProg2Db($componentName);
//			return $dl->executeScalar($query, __FILE__, __LINE__) > 0;
//		}
//	}
	
	// outlines di default
	protected function getOutline() {
		return $this->nested ? null : (\system\HTMLHelpers::isAjaxRequest() ? "layout/OutlineAjax" : "layout/Outline");
	}
	
	///<editor-fold defaultstate="collapsed" desc="Metodi standard per inizializzazione di clausole">
	protected function loadStdFilters(RecordsetBuilderInterface $builder, $prefix="") {
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
	
	protected function loadStdSorts(RecordsetBuilderInterface $builder, $prefix="") {
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
	
	protected function loadStdLimits(RecordsetBuilderInterface $builder, $pageSize=100, $prefix="") {
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

	private function processSteps() {
		// Controllo i permessi per il componente
//		if (!self::checkComponentPermission($this->getName())) {
//			throw new AuthorizationException("Utente non autorizzato");
//		}
		if (!\call_user_func(array(\get_class($this), "checkPermission"), $_REQUEST)) {
			throw new AuthorizationException("Utente non autorizzato");
		}
		
		$this->tplManager->setOutlineTemplate($this->getOutline());

		// Lancio l'evento onProcess
		$responseType = $this->onProcess();
		
		switch ($responseType) {
			case Component::RESPONSE_TYPE_READ:
			case Component::RESPONSE_TYPE_NOTIFY:
			case Component::RESPONSE_TYPE_FORM:
			case Component::RESPONSE_TYPE_ERROR:
				$this->datamodel["private"]["responseType"] = $responseType;
				break;
			case null:
				break;
			default:
				throw new InternalErrorException("Metodo onProcess non valido per il componente " . $this->getName());
		}

		$this->tplManager->setMainTemplate($this->getTemplate());
		
		if (!\is_null($responseType)) {
			// Processo il template
			$this->tplManager->process($this->datamodel);
		}
	}
	
	final public function process() {
		$pageOutput = "";

		$mtime_start = \microtime(true);
			
//		\ob_start();
			
		try {
			
			if ($this->nested) {
				$this->processSteps();
			} else {
				$this->processSteps();
//				$pageOutput = \ob_get_flush();
			}
			
		}
		
		catch (\Exception $ex) {
			// Eccezione non gestita

			$mtime_end = \microtime(true);


//			while (\ob_get_clean());

//			\ob_start();

			$this->datamodel["private"]["responseType"] = Component::RESPONSE_TYPE_ERROR;
			\system\HTMLHelpers::makeErrorPage($this->tplManager, $this->datamodel, $ex,	\round($mtime_end - $mtime_start, 3));

//			if ($this->nested) {
//				$pageOutput = \ob_get_clean();
//			} else {
//				$pageOutput = \ob_get_flush();
//			}


			try {
				$id = \module\core\model\XmcaLog::saveLog($this->getName(), $pageOutput);
			} catch (\Exception $ex) {
				echo "<h1>" . $ex->getMessage() . "</h1>";
				if ($ex instanceof DataLayerException) {
					echo $ex->getHtmlMessage();
				}
				// Eccezione non gestita
			}
		}
	}
}
?>