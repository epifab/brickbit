<?php
namespace system;

use system\utils\HTMLHelpers;
use system\TemplateManager;
use system\exceptions\AuthorizationError;
use system\exceptions\Error;
use system\exceptions\InternalError;
use system\exceptions\PageNotFound;
use system\exceptions\Redirect;
use system\exceptions\UnderDevelopment;
use system\utils\Login;

abstract class Component {
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

  /**
   * @var TemplateManager
   */
  private $tplManager;
  /**
   * @var mixed[]
   */
  protected $datamodel = array();

  /**
   * Check user access for component/action(args)
   * Can be overriden by component extending classes declaring
   *   accessYourAction($urlArgs, $userId)
   *   access($action, $urlArgs, $userId)
   * @param string $class Component class
   * @param string $action Action
   * @param string $urlArgs URL arguments
   * @param object $user User
   * @return boolean True if $user is able to perform $action($urkArgs)
   */
  public static function access($class, $action, $urlArgs, $user) {
    if (\is_callable(array($class, "access" . $action))) {
      return (bool)\call_user_func(array($class, "access" . $action), $urlArgs, $user);
    }
    return false;
  }

  /**
   * Allows extending class to do something when the component is processed
   */
  protected function onInit() {

  }

  /**
   * Error handler
   * @param \Exception $exception Exception to handle with
   */
  protected function onError($exception) {
    if (!($exception instanceof Error)) {
      $exception = new InternalError($exception->getMessage());
    }
    HTMLHelpers::makeErrorPage($this->tplManager, $this->datamodel, $exception, Main::getExecutionTime());

//      if ($this->nested) {
//        $pageOutput = \ob_get_clean();
//      } else {
//        $pageOutput = \ob_get_flush();
//      }

//    try {
//      $id = \module\core\model\brickbitLog::saveLog($this->name, $pageOutput);
//    } catch (\Exception $ex) {
//      echo "<h1>" . $ex->getMessage() . "</h1>";
//      if ($ex instanceof SqlQueryError) {
//        echo $ex->getHtmlMessage();
//      }
//    }
  }

  /**
   * Default process handler
   * @throws PageNotFound
   */
  protected function defaultRunHandler() {
    throw new PageNotFound();
  }

  /**
   * Gets the full datamodel
   * @return array Datamodel
   */
  public function &getDataModel() {
    return $this->datamodel;
  }

  /**
   * @param string $name Component name
   * @param string $module Module name
   * @param string $action Action
   * @param string $url URL
   * @param array $urlArgs URL arguments
   * @param array $request Request
   */
  public function __construct($name, $module, $action, $url, $urlArgs, $request=null) {
    $this->name = $name;
    $this->module = $module;
    $this->action = $action;

    $this->url = $url;
    $this->urlArgs = $urlArgs;
    $this->requestData = \is_null($request) ? $_REQUEST : (array)$request;

    $this->loadRequestId();
    $this->loadRequestType();

    $this->nested = (bool)Main::getActiveComponent();

    $this->alias = $this->name;
    if (Main::getActiveComponent()) {
      $this->alias = Main::getActiveComponent()->alias . '__' . $this->alias;
    }

    $this->tplManager = Main::getTemplateManager();

    // setting the default outline and outline wrapper templates
    $this->setOutlineWrapperTemplate($this->getOutlineWrapperTemplate());
    $this->setOutlineTemplate($this->getOutlineTemplate());
  }

  /**
   * Gets the request ID
   * @return string Request id
   */
  public function getRequestId() {
    return $this->requestId;
  }

  /**
   * Gets the request type
   * @return string Request type
   */
  public function getRequestType() {
    return $this->requestType;
  }

  private function loadRequestId() {
    if (!isset($this->requestData['system'])
       || !isset($this->requestData['system']['requestId'])
       || !\preg_match('/^[a-zA-Z0-9_-]+$/', $this->requestData['system']['requestId'])) {

      $requestIds = &Main::session('system', 'requestIds', array());
      $requestIds[$this->name] = isset($requestIds[$this->name])
          ? $requestIds[$this->name] + 1
          : 1;

      $this->requestId = $this->name . $requestIds[$this->name];
    }
    else {
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

  public function initDatamodel(array $datamodel = array()) {
    $this->datamodel = $datamodel;
  }

  /**
   * Adds a js file to the list on the datamodel
   * @param string $js Path to the js file
   */
  public function addJs($js) {
    if (!\in_array($js, $this->datamodel['page']['js'])) {
      $this->datamodel['page']['js']['script'][] = $js;
    }
  }

  public function addJsData($key, $data) {
    $this->datamodel['page']['js']['data'][$key] = \json_encode($data);
  }

  /**
   * Adds a css file to the list on the datamodel
   * @param string $css Path to the css file
   */
  public function addCss($css) {
    if (!\in_array($css, $this->datamodel['page']['css'])) {
      $this->datamodel['page']['css'][] = $css;
    }
  }

  /**
   * Adds a meta tag to the list on the datamodel
   * @param string $meta Meta tag
   */
  public function addMeta($meta) {
    $this->datamodel['page']['meta'][] = $meta;
  }

  /**
   * Sets the main template
   * @param string $template Template name
   */
  public function setMainTemplate($template) {
    $this->tplManager->setMainTemplate($template);
  }

  /**
   * Sets the outline template
   * @param string $template Template name
   */
  public function setOutlineTemplate($template) {
    $this->tplManager->setOutlineTemplate($template);
  }

  /**
   * Sets a template
   * @param string $template Template name
   * @param string $region Region where to add the template
   * @param int $weight Templates of a region are sorted by weight (ascending)
   */
  public function addTemplate($template, $region, $weight=0) {
    $this->tplManager->addTemplate($template, $region, $weight);
  }

  /**
   * Sets an outline wrapper template
   * @param string $template Template name
   */
  public function setOutlineWrapperTemplate($template) {
    $this->tplManager->setOutlineWrapperTemplate($template);
  }

  private function setResponseType($responseType) {
    $this->datamodel['system']['responseType'] = $responseType;
  }

  /**
   * Sets the page title
   * @param string $pageTitle Page title
   * @param boolean $adding Whether to add the $pageTitle to the page title or to replace it
   */
  public function setPageTitle($pageTitle, $adding=false) {
    $this->datamodel['page']['title'] = ($adding && !empty($this->datamodel['page']['title']) ? $this->datamodel['system']['title'] . ' | ' : '') . $pageTitle;
  }

  /**
   * Gets module name
   * @return string Module name
   */
  final public function getModule() {
    return $this->module;
  }

  /**
   * Gets component name
   * @return string Component name
   */
  final public function getName() {
    return $this->name;
  }

  /**
   * Gets component alias
   * @return string Alias
   */
  final public function getAlias() {
    return $this->alias;
  }

  /**
   * Gets component action
   * @return string Action
   */
  final public function getAction() {
    return $this->action;
  }

  /**
   * Gets action URL
   * @return string Action URL
   */
  final public function getUrl() {
    return $this->url;
  }

  /**
   * Gets URL parameters
   * @return array URL parameters
   */
  final public function getUrlArgs() {
    return $this->urlArgs;
  }

  /**
   * Gets URL parameter
   * @param int $index URL parameter index (starting from 0)
   * @return mixed URL parameter corrisponding to index or null if it does not exist
   */
  final public function getUrlArg($index) {
    return \array_key_exists($index, $this->urlArgs) ? $this->urlArgs[$index] : null;
  }

  /**
   * Gets data attached to the request (usually $_REQUEST)
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

  /**
   * Processes the component.
   * 1) Calls the onInit event
   * 2) Checks permissions
   * 2) Executes the "run action" method if defined on the derived class
   * 3) Displays a template according to the value returned by the "run action" method
   * *) If an error occurs the onError method is executed
   * Remove the current component from the components stack.
   */
  final public function process() {

    // initializing the output buffer
    \ob_start();

    $pageOutput = "";

    try { // any error
      try { // handle with redirects
        // init event
        $this->onInit();

        // checking permission
        if (!self::access(\get_class($this), $this->action, $this->urlArgs, Login::getLoggedUser())) {
          throw new AuthorizationError();
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
          case \system\RESPONSE_TYPE_READ:
          case \system\RESPONSE_TYPE_NOTIFY:
          case \system\RESPONSE_TYPE_FORM:
          case \system\RESPONSE_TYPE_ERROR:
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

      catch (Redirect $ex) {
        Main::run($ex->getUrl());
      }

      catch (\Exception $ex) {
        $this->onError($ex);
      }
    }

    catch (AuthorizationError $ex) {
      $this->setResponseType(\system\RESPONSE_TYPE_ERROR);
      // Cleaning the buffer
      while (\ob_get_clean());
      \header("HTTP/1.1 403 Forbidden");
      $this->setPageTitle('Access denied');
      $this->setMainTemplate('403');
      try {
        $this->tplManager->process($this->datamodel);
      }
      catch (\Exception $ex) { }
    }

    catch (PageNotFound $ex) {
      $this->setResponseType(\system\RESPONSE_TYPE_ERROR);
      // Cleaning the buffer
      while (\ob_get_clean());
      \header("HTTP/1.0 404 Not Found");
      $this->setMainTemplate('404');
      $this->setPageTitle('Resource not found');
      try {
        $this->tplManager->process($this->datamodel);
      }
      catch (\Exception $ex) { }
    }

    catch (UnderDevelopment $ex) {
      $this->setResponseType(\system\RESPONSE_TYPE_ERROR);
      // Cleaning the buffer
      while (\ob_get_clean());
      \header("HTTP/1.1 501 Not implemented");
      $this->setMainTemplate('501');
      $this->setPageTitle('Under development');
      try {
        $this->tplManager->process($this->datamodel);
      }
      catch (\Exception $ex) { }
    }

    catch (\Exception $ex) {
      $this->setResponseType(\system\RESPONSE_TYPE_ERROR);

      // Cleaning the buffer
      while (\ob_get_clean());

      // onError event
      $this->onError($ex);
    }
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
}
