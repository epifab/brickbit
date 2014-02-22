<?php
namespace system;

use system\model\RecordsetBuilder;
use system\model\Recordset;
use system\model\RecordsetInterface;

use system\utils\HTMLHelpers;
use system\utils\Lang;
use system\utils\Login;
use system\Theme;
use system\TemplateManager;
use system\utils\Utils;

use system\exceptions\InternalError;
use system\exceptions\AuthorizationError;
use system\exceptions\ValidationError;

abstract class Component {
  //  components stack
  private static $components = array();
  private static $mainComponent = null;
  
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
    return self::$mainComponent;
  }
  /**
   * @return Component
   */
  public static function getCurrentComponent() {
    return \current(self::$components);
  }

  private static function pushComponent(Component $component) {
    if (empty(self::$components)) {
      self::$mainComponent = $component;
    }
    \array_push(self::$components, $component);
  }
  /**
   * @return Component
   */
  private static function popComponent() {
    return \array_pop(self::$components);
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
    if (\is_callable(array($class, "access" . $action))) {
      return (bool)\call_user_func(array($class, "access" . $action), $urlArgs, $request, $user);
    } 
//    else if (\is_callable(array($class, "access"))) {
//      return (bool)\call_user_func(array($class, "access"), $action, $urlArgs, $request, $user);
//    }
    return true;
  }
  
  /**
   * This method is called whenever  
   */
  protected function onInit() {
    
  }
  
  /**
   * Error handler
   * @param \Exception $exception Exception to handle with
   */
  protected function onError($exception) {
    if (!($exception instanceof \system\exceptions\Error)) {
      $exception = new InternalError($exception->getMessage());
    }
    \system\utils\HTMLHelpers::makeErrorPage($this->tplManager, $this->datamodel, $exception, $this->getExecutionTime());

//      if ($this->nested) {
//        $pageOutput = \ob_get_clean();
//      } else {
//        $pageOutput = \ob_get_flush();
//      }

//    try {
//      $id = \module\core\model\ciderbitLog::saveLog($this->name, $pageOutput);
//    } catch (\Exception $ex) {
//      echo "<h1>" . $ex->getMessage() . "</h1>";
//      if ($ex instanceof SqlQueryError) {
//        echo $ex->getHtmlMessage();
//      }
//    }
  }
  
  protected function onProcess() { }
  
  protected function defaultRunHandler() {
    throw new \system\exceptions\PageNotFound();
  }
  
//  /**
//   * Set a value to the datamodel.
//   * @param mixed $key Key string or array representing the path to the key
//   * @param mixed $value Value to add to the datamodel
//   */
//  public function setData($key, $value) {
//    if (\is_array($key)) {
//      $dm =& $this->datamodel;
//      \reset($key);
//      do {
//        $k1 = current($key);
//        $k2 = next($key);
//        if ($k2 === false) {
//          $dm[$k1] = $value;
//        }
//        else if (!\array_key_exists($k1, $dm)) {
//          $dm[$k1] = array();
//        }
//        $dm =& $dm[$k1];
//      } while ($k2);
//    }
//    else {
//      $this->datamodel[$key] = $value;
//    }
//  }
  
  /**
   * Get the full datamodel
   * @return array Datamodel
   */
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
    
    $this->nested = (bool)self::getCurrentComponent();
    self::pushComponent($this);
    
    $this->alias = $this->name;
    if (self::getCurrentComponent()) {
      $this->alias = self::getCurrentComponent()->alias . '__' . $this->alias;
    }
    
    // initializing the view layer
    $this->initView();
    
    // setting the default outline and outline wrapper templates
    $this->setOutlineWrapperTemplate($this->getOutlineWrapperTemplate());
    $this->setOutlineTemplate($this->getOutlineTemplate());
  }
  
  /**
   * Get the request ID
   * @return string Request id
   */
  public function getRequestId() {
    return $this->requestId;
  }
  
  /**
   * Get the request type
   * @return string Request type
   */
  public function getRequestType() {
    return $this->requestType;
  }
  
  private function loadRequestId() {
    if (!isset($this->requestData['system'])
       || !isset($this->requestData['system']['requestId'])
       || !\preg_match('/^[a-zA-Z0-9_-]+$/', $this->requestData['system']['requestId'])) {
      
      if (!isset($_SESSION['system'])) {
        $_SESSION['system'] = array();
      }
      if (!isset($_SESSION['system']['requestIds'])) {
        $_SESSION['system']['requestIds'] = array();
      }
      $_SESSION['system']['requestIds'][$this->name] =
        (isset($_SESSION['system']['requestIds'][$this->name]))
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
        switch (strtoupper((string)$this->requestData['system']['requestType'])) {
          case 'AJAX':
            $this->requestType = 'AJAX';
            break;
          case 'MAIN':
            $this->requestType = 'MAIN';
            break;
          case 'HTML':
          default:
            $this->requestType = 'HTML';
        }
      }
    }
    if (!$this->requestType) {
      $this->requestType = 
        HTMLHelpers::isAjaxRequest()
          ? 'AJAX'
          : 'HTML';
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
        'messages' => array()
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
  }
  
  /**
   * Add a js file
   * @param string $js Path to the js file
   */
  public function addJs($js) {
    if (!\in_array($js, $this->datamodel['page']['js'])) {
      $this->datamodel['page']['js'][] = $js;
    }
  }
  
  /**
   * Add a css file
   * @param string $css Path to the css file
   */
  public function addCss($css) {
    if (!\in_array($css, $this->datamodel['page']['css'])) {
      $this->datamodel['page']['css'][] = $css;
    }
  }
  
  /**
   * Add a message to be displayed
   * @param string $body Message body
   * @param array $args Message arguments
   * @param string $type Message type. Typical values are: success, info, warning, danger
   */
  public function addMessage($body, $args, $type) {
    $this->datamodel['system']['messages'][$type][] = \cb\t($body, $args);
  }
  
  /**
   * Add a meta tag
   * @param string $meta Meta tag
   */
  public function addMeta($meta) {
    $this->datamodel['page']['meta'][] = $meta;
  }
  
  /**
   * Set the main template
   * @param string $template Template name
   */
  public function setMainTemplate($template) {
    $this->tplManager->setMainTemplate($template);
  }
  
  /**
   * Set the outline template
   * @param string $template Template name
   */
  public function setOutlineTemplate($template) {
    $this->tplManager->setOutlineTemplate($template);
  }
  
  /**
   * Set a template
   * @param string $template Template name
   * @param string $region Region where to add the template
   * @param int $weight Templates of a region are sorted by weight (ascending)
   */
  public function addTemplate($template, $region, $weight=0) {
    $this->tplManager->addTemplate($template, $region, $weight);
  }
  
  /**
   * Set an outline wrapper template
   * @param string $template Template name
   */
  public function setOutlineWrapperTemplate($template) {
    $this->tplManager->setOutlineWrapperTemplate($template);
  }
  
  private function setResponseType($responseType) {
    $this->datamodel['system']['responseType'] = $responseType;
  }
  
  /**
   * Set the page title
   * @param string $pageTitle Page title
   * @param boolean $adding Whether to add the $pageTitle to the page title or to replace it
   */
  public function setPageTitle($pageTitle, $adding=false) {
    $this->datamodel['page']['title'] = ($adding && !empty($this->datamodel['page']['title']) ? $this->datamodel['system']['title'] . ' | ' : '') . $pageTitle;
  }
  
  /**
   * Get module name
   * @return string Module name
   */
  final public function getModule() {
    return $this->module;
  }
  
  /**
   * Get component name
   * @return string Component name
   */
  final public function getName() {
    return $this->name;
  }
  
  /**
   * Get component alias
   * @return string Alias
   */
  final public function getAlias() {
    return $this->alias;
  }

  /**
   * Get component action
   * @return string Action
   */
  final public function getAction() {
    return $this->action;
  }
  
  /**
   * Get action URL
   * @return string Action URL
   */
  final public function getUrl() {
    return $this->url;
  }
  
  /**
   * Get URL parameters
   * @return array URL parameters
   */
  final public function getUrlArgs() {
    return $this->urlArgs;
  }
  
  /**
   * Get URL parameter
   * @param int $index URL parameter index (starting from 0)
   * @return mixed URL parameter corrisponding to index or null if it does not exist
   */
  final public function getUrlArg($index) {
    return \array_key_exists($index, $this->urlArgs) ? $this->urlArgs[$index] : null;
  }
  
  /**
   * Get data attached to the request (usually $_REQUEST)
   * @return array Data attached to the request
   */
  final public function getRequestData() {
    return $this->requestData;
  }
  
  /**
   * Check if the component is nested
   * @return boolean True if the component is nested
   */
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
        
        $field = $builder->searchField($path);
        if (\is_null($field)) {
          throw new InternalError('Invalid filter parameter (field @path has not been imported)', array('@path' => $path));
        }
        $field instanceof Field;
        try {
          $progValue = $field->edit2Prog($value);
        } catch (ValidationError $ex) {
          throw $ex;
        }

        $filter = new FilterClause($field, $rop, $progValue);

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
          throw new InternalError('Invalid sort parameter.');
        }
        $field = $builder->searchField($args[0]);
        if (\is_null($field)) {
          throw new InternalError('Invalid filter parameter (field @path has not been imported)', array('@path' => $args[0]));
        }

        $sc = new SortClause($field, $args[1]);
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
      throw new InternalError('Invalid page parameter');
    }
    if (!\is_numeric($this->requestData[$index]["size"]) || ((int)$this->requestData[$index]["size"]) < 0) {
      throw new InternalError('Invalid page parameter');
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

  /**
   * Processes the component.
   * 1) Calls the onInit event
   * 2) Checks permissions
   * 2) Executes the "run action" method if defined in the derived class
   * 3) Displays a template according to the value returned by the "run action" method
   * *) If an error occurs the onError method is executed
   * Remove the current component from the components stack.
   */
  final public function process() {
    
    // initializing request time
    $this->microTime = \microtime(true);

    // initializing the output buffer
    \ob_start();
      
    $pageOutput = "";

    try { // any error
      try { // handle with redirects
        // init event
        $this->onInit();

        // checking permission
        if (!self::access(\get_class($this), $this->action, $this->urlArgs, $this->requestData, \system\utils\Login::getLoggedUser())) {
          throw new AuthorizationError('Sorry, you are not authorized to access this resource.');
        }

        $runMethod = null;
        $runArgs = array();

        // onProcess event
        if (\is_callable(array($this, 'run' . $this->action))) {
          $runMethod = array($this, 'run' . $this->action);
          $runArgs = array();
        }

        if (\is_null($runMethod)) {
          $responseType = $this->defaultRunHandler();
        } else {
          $responseType = \call_user_func_array($runMethod, $runArgs);
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
            throw new InternalError('Invalid action <em>@action</em> response, module <em>@module</em> component <em>@component</em>.', array(
              '@action' => $this->action, 
              '@component' => $this->name,
              '@module' => $this->module
            ));
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

      catch (\system\exceptions\Redirect $ex) {
        \system\Main::run($ex->getUrl());
      }
    }
    
    catch (\Exception $ex) {
      // Uncaught exception
      
      // Cleaning the buffer
      while (\ob_get_clean());

      // onError event
      $this->onError($ex);
    }
    
    self::popComponent();
  }
  
  /**
   * Returns the outline wrapper template (if defined)
   * This may be overriden to specify a default custom outline wrapper template
   * @return string|null Outline wrapper template
   */
  public function getOutlineWrapperTemplate() {
    if (!$this->isNested() && $this->requestType == 'AJAX') {
      return 'outline-wrapper';
    } else {
      return null;
    }
  }
  
  /**
   * Returns the outline template (if defined)
   * This may be overriden to specify a default custom outline template
   * @return string|null Outline template
   */
  public function getOutlineTemplate() {
    if (!$this->isNested() && $this->requestType == 'HTML') {
      return 'outline';
    } else {
      return null;
    }
  }
  
  public function formSubmission($formId) {
    \system\view\Form::getPcheckFormSubmission($formId);
  }
}
