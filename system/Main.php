<?php
namespace system;

use system\Component;
use system\Settings;
use system\ThemeApi;
use system\exceptions\InternalError;
use system\utils\Login;
use system\view\TemplateManager;

class Main {
  private static $timeRequestStack = array();
  private static $componentStack = array();
  
  /**
   * Time of the request
   * @return int UNIX timestamp of the request
   */
  public static function getTimeRequest() {
    return \round(\end(self::$timeRequestStack), 0);
  }
  
  /**
   * Gets the execution time
   * @param boolean $absolute TRUE if you wish to get the number of seconds from
   *  the first time the 'run' method is called.
   * @return float Number of seconds of the execution time (returns a float 
   *  rounded to the 3rd decimal (representing microseconds)
   */
  public static function getExecutionTime($absolute = false) {
    if (empty(self::$timeRequestStack)) {
      return false;
    }
    return \round(\microtime(true) - ($absolute ? self::$timeRequestStack[0] : \end(self::$timeRequestStack)), 3);
  }

  /**
   * Returns the entire application configuration.
   * 
   * <p>NB. modules are sorted by weight ascending.</p>
   * 
   * <pre>
   *    'modules' :
   *      [module name] :
   *        'name' : [module name]
   *        'path' : [module path]
   *        'class' : [module class]
   *        'modelClass' : [model class]
   *        'viewClass' : [view class]
   *        'components' :
   *          [component name] :ss
   *            'pages' :
   *              0 :
   *                'url' : [url]
   *                'action' : [action]
   *              ...
   *        'events' :
   *          [event name]
   *          ...
   *      ...
   *    'urls' :
   *      [url] :
   *        'module' :
   *          'class' : [module class]
   *          'name' : [module name]
   *        'component' :
   *          'class' : [component class]
   *          'name' : [component name]
   *        'action' : [action]
   *    'events' :
   *      [event name] :
   *        [component name]
   *        ...
   *    'tables' :
   *      [table name] :
   *        'fields' :
   *          ...
   *        'keys' :
   *          ...
   *        'relations' :
   *          ...
   *        'virtuals' :
   *          ...
   *    'modelClasses' :
   *      [model class]
   *      ...
   *    'templates' :
   *      [template name] : [template path]
   *      ...
   *    'viewClasses' :
   *      [view class]
   *      ...
   * </pre>
   * @return array
   */
  private static function configuration() {
    static $configuration = null;
    
    if (\is_null($configuration)) {
      if (self::setting('coreCache', true)) {
        $configuration = self::getCache('system-configuration', null);
      }
      if (\is_null($configuration)) {
        $configuration = SystemConfiguration::loadConfiguration();
        if (self::setting('coreCache')) {
          self::setCache('system-configuration', $configuration);
        }
      }
    }
    return $configuration;
  }
  
  /**
   * Checks if the module exists.
   * @param string $moduleName Module name
   * @return boolean TRUE if the module exists (and enabled)
   */
  public static function moduleExists($moduleName) {
    $c = self::configuration();
    return \array_key_exists($moduleName, $c['modules']);
  }
  
  /**
   * Returns the module configuration.
   * @param string $moduleName Module name
   * @return array Module configuration array
   * @throws InternalError In case the module does not exist
   *  or is not enabled.
   */
  public static function getModule($moduleName) {
    if (\is_null($moduleName)) {
      return null;
    }
    $config = self::configuration();
    if (isset($config['modules'][$moduleName])) {
      return $config['modules'][$moduleName];
    } else {
      throw new InternalError('Module <em>@name</em> not found.', array('@name' => $moduleName));
    }
  }

  /**
   * Checks if a template exists.
   * @param string $templateName Template name
   * @return boolean TRUE if the template exists (and the possible module which
   *  contains the template is enabled).
   */
  public static function templateExists($templateName) {
    $c = self::configuration();
    return \array_key_exists($templateName, $c['templates']);
  }
  
  /**
   * Returns a template file path.
   * @param string $templateName Template name
   * @return string Template full path
   * @throws InternalError In case the template doesn't exist
   */
  public static function getTemplate($templateName) {
    if (\is_null($templateName)) {
      return null;
    }
    $config = self::configuration();
    if (isset($config['templates'][$templateName])) {
      return $config['templates'][$templateName];
    } else {
      throw new InternalError('Template <em>@name</em> not found.', array('@name' => $templateName));
    }
  }

  /**
   * Checks if a table exists.
   * @param string $tableName Table name
   * @return boolean TRUE if at least one active module define this table in its
   *  model.yml configuration file.
   */
  public static function tableExists($tableName) {
    $c = self::configuration();
    return \array_key_exists($tableName, $c['tables']);
  }
  
  /**
   * Returns a table configuration.
   * @param string $tableName
   * @return array Table configuration
   * @throws InternalError In case the table does not exist
   *  or its defining module isn't enabled.
   */
  public static function getTable($tableName) {
    if (self::tableExists($tableName)) {
      $c = self::configuration();
      return $c['tables'][$tableName];
    } else {
      throw new InternalError('Table <em>@name</em> not found.', array('@name' => $tableName));
    }
  }
  
  /**
   * Checks if the URL matches any pattern defined in any active module.
   * @param string $url URL
   * @return boolean TRUE if at least one active module component is associated
   *  to this url.
   */
  public static function urlExists($url) {
    return !\is_null(self::getComponentInfoByUrl($url));
  }
  
  /**
   * Returns info about the component responsible for the URL.
   * <p>This information are defined by info.yml files.</p>
   * <p>This method doesn't take into account unactive modules.</p>
   * <ul>
   *   <li>'module': module name</li>
   *   <li>'name': component name</li>
   *   <li>'class': component class</li>
   *   <li>'action': action to be invoked for the given URL</li>
   *   <li>'urlArgs': URL arguments, extracted according to the 'info.yml'
   *  configuration file</li>
   * </ul>
   * @param string $url URL
   * @return array Component info (null in case the URL doesn't match any 
   *  pattern defined by any active module)
   */
  public static function getComponentInfoByUrl($url) {
    static $urls = null;
    
    if (false !== ($x = \strstr($url, '?', true))) {
      // Removing get parameters
      $url = $x;
    }

    $basePath = self::stripFinalSlash(self::getBaseDir() . self::getVirtualBaseDir());
    
    if (\substr($url, 0, \strlen($basePath)) != $basePath) {
      return null;
    }
    // Stripping base dir and virtual path
    $url = \substr($url, \strlen($basePath));
    
    if (!empty($url) && substr($url, 0, 1) == '/') {
      $url = substr($url, 1);
    }
    
    if (empty($url)) {
      // substr may return FALSE if $url == $basePath
      $url = '';
    }
    
    if (\is_null($urls)) {
      if (self::setting('coreCache')) {
        // Url cache
        $urls = self::getCache('system-urls', array());
      } else {
        $urls = array();
      }
    }
    if (!\array_key_exists($url, $urls)) {
      $urls[$url] = null;
      $configuration = self::configuration();
      foreach ($configuration['urls'] as $regexp => $info) {
        if (\preg_match('@^' . $regexp . '$@', $url, $m)) {
          \array_shift($m);
          $urls[$url] = array(
            'module' => $info['module']['name'],
            'name' => $info['component']['name'],
            'class' => $info['component']['class'],
            'action' => $info['action'],
            'urlArgs' => $m
          );
          if (self::setting('coreCache')) {
            self::setCache("system-urls", $urls);
          }
          break;
        }
      }
    }
    return $urls[$url];
  }
  
  /**
   * Get the currently active component
   * @return Component The most recent component which is currently 
   *  running or NULL if any component isn't running.
   */
  public static function getActiveComponent() {
    return \end(self::$componentStack);
  }
  
  /**
   * Get the main active component
   * @return Component The oldest running component or NULL if any 
   *  component isn't running.
   */
  public static function getActiveComponentMain() {
    return empty(self::$componentStack) ? null : self::$componentStack[0];
  }
  
  /**
   * Checks if the user has access to a given URL
   * @param string $url URL
   * @param object $user User
   * @return boolean TRUE if the user has access to the URL
   */
  public static function checkAccess($url, $user=false) {
    if ($user === false) {
      $user = Login::getLoggedUser();
    }
    if (self::urlExists($url)) {
      $x = self::getComponentInfoByUrl($url);
      return Component::access(
        $x['class'],
        $x['action'],
        $x['urlArgs'],
        $user
      );
    } else {
      return true;
    }
  }
  
  /**
   * Runs the component associated with the URL if any. Makes
   * @param string $url URL (default to REQUEST_URI)
   * @param array $request Application request (default to $_REQUEST)
   */
  public static function run($url = null, $request = null) {
    if (\is_null($url)) {
      $url = $_SERVER['REQUEST_URI'];
    }
    
    if (\is_null($request)) {
      $request = $_REQUEST;
    }
    
    $component = self::getComponentInfoByUrl($url);
    
    if (!\is_null($component)) {
      $componentClass = $component['class'];
      $obj = new $componentClass(
        $component['name'],
        $component['module'],
        $component['action'],
        $url,
        $component['urlArgs'],
        $request
      );
    }
    else {
      // No component associated with this URL
      $obj = new DefaultComponent($url);
    }
    
    \array_push(self::$timeRequestStack, \microtime(true));
    \array_push(self::$componentStack, $obj);

    try {
      $obj->initDatamodel(SystemApi::initDatamodel());
      
      if (!$obj->isNested()) {
        // Allows the theme to do special stuff before modules
        ThemeApi::preRun($obj);
        // Raise event onRun
        SystemApi::onRun($obj);
        ThemeApi::onRun($obj);
      }

      $obj->process();
    }
    catch (\Exception $ex) {
      \array_pop(self::$componentStack);
      \array_pop(self::$timeRequestStack);
      throw $ex;
    }
    
    if (self::setting('debug', false)) {
      self::pushMessage(\cb\t('Execution time component <em>@component</em>::<em>@action</em>: @time sec.</b>', array(
        '@component' => $obj->getName(),
        '@action' => $obj->getAction(),
        '@time' => self::getExecutionTime()
      )));
    }
    
    \array_pop(self::$componentStack);
    \array_pop(self::$timeRequestStack);
  }
  
  /**
   * Returns a cached module configuration value.
   * <p>Runs invokeMethodAll and caches its results.</p>
   * <p>The way this method works is to progressively merge implementing methods
   *  array results.</p>
   * <p>Considering the two following module classes:</p>

   * <p>Unlike 'invokeMethodAllMerge', this function does not accept any 
   *  additional argument to pass to the controller methods.</p>
   * @param string $methodName Method name
   * @return mixed Result
   */
  public static function invokeStaticMethodAllMerge($methodName, $staticCache = true) {
    static $results = array(); // Cache level 1
    
    if (!\array_key_exists($methodName, $results) && $staticCache && self::cacheExists('system-static-method-all-merge-' . $methodName)) {
      // Cache level 2
      $results[$methodName] = self::getCache('system-static-method-all-merge-' . $methodName);
    }
    
    if (!\array_key_exists($methodName, $results)) {
      $results[$methodName] = self::invokeMethodAllMerge($methodName, $staticCache);
      if ($staticCache) {
        self::setCache('system-static-method-all-merge-' . $methodName, $results[$methodName]);
      }
    }
    return $results[$methodName];
  }
  
  /**
   * Returns a cached module configuration value.
   * <p>Runs invokeMethod and caches its results.</p>
   * <p>Unlike 'invokeMethod', this function does not accept any additional 
   *  argument to pass to the controller methods.</p>
   * @param string $methodName Method name
   * @param boolean $staticCache TRUE if you wish to caches the results on the
   *  file system (cache is implemented by using getCache and setCache 
   *  methods)
   * @return mixed Result
   */
  public static function invokeStaticMethod($methodName, $staticCache = true) {
    static $results = array(); // Cache level 1
    
    if (!\array_key_exists($methodName, $results) && $staticCache && self::cacheExists('system-static-method-' . $methodName)) {
      // Cache level 2
      $results[$methodName] = self::getCache('system-static-method-' . $methodName);
    }
    
    if (!\array_key_exists($methodName, $results)) {
      $results[$methodName] = self::invokeMethod($methodName);
      if ($staticCache) {
        self::setCache('system-static-method-' . $methodName, $results[$methodName]);
      }
    }
    return $results[$methodName];
  }
  
  /**
   * Returns a cached module configuration value.
   * <p>Runs invokeMethodAll and caches its results.</p>
   * <p>Unlike 'invokeMethodAll', this function does not accept any additional 
   *  argument to pass to the controller methods.</p>
   * @param string $methodName Method name
   * @param boolean $staticCache TRUE if you wish to caches the results on the
   *  file system (cache is implemented by using getCache and setCache 
   *  methods)
   * @return mixed Result
   */
  public static function invokeStaticMethodAll($methodName, $staticCache = true) {
    static $results = array(); // Cache level 1
    
    if (!\array_key_exists($methodName, $results) && $staticCache && self::cacheExists('system-static-method-all-' . $methodName)) {
      // Cache level 2
      $results[$methodName] = self::getCache('system-static-method-all-' . $methodName);
    }
    
    if (!\array_key_exists($methodName, $results)) {
      $results[$methodName] = self::invokeMethodAll($methodName);
      if ($staticCache) {
        self::setCache('system-static-method-all-' . $methodName, $results[$methodName]);
      }
    }
    return $results[$methodName];
  }

  /**
   * Alias of invokeMethodAll
   * @param string $eventName Method name
   */
  public static function raiseControllerEvent($eventName) {
    return \call_user_func_array(array('self', 'invokeMethodAll'), \func_get_args());
  }
  
  /**
   * Invokes a controller method.
   * <p>Searches for the highest priority module class which implements the 
   *  method and calls it. If the method isn't implemented by any module class, 
   *  it returns null.</p>
   * <p>This function takes an unlimited number of arguments.</p>
   * <p>Every argument (apart from the method name) is passed to the module 
   *  class method.</p>
   * 
   * <p>Usage example:</p>
   * <code>
   * <pre>
   *  class Module1 {
   *    public static function x() {
   *      return array(
   *        'a' => 'module1',
   *        'b' => 'module1'
   *      );
   *    }
   *  }
   *  class Module2 {
   *    public static function x() {
   *      return array(
   *        'b' => 'module2',
   *        'c' => 'module2'
   *      );
   *    }
   *  }
   *  class Module3 {
   *    public static function x() {
   *      return 'module3';
   *    }
   *  }
   *  class Module4 {
   *    public static function x() {
   *      return null;
   *    }
   *  }
   * </pre>
   * </code>
   * 
   * <p>Assuming modules are all enabled and priority order is 
   *  Module1 > Module2 > Module3 > Module4, then:</p>
   * 
   * <pre>print_r(Main::invokeMethodAllMerge('x'));</pre>
   * 
   * <p>Will print out:</p>
   * 
   * <pre>
   * array('a' => 'module1', 'b' => 'module1')
   * </pre>
   * @param string $methodName Name of the module class method
   * @return array Returns the implementing method results.
   */
  public static function invokeMethod($methodName) {
    static $modules = null;
    static $methodBinding = array();
    
    if (\is_null($modules)) {
      $configuration = self::configuration();
      // Modules are ordered according to their weight ascending
      // We want to search for the right method to run following the descending
      //  order...
      $modules = $configuration['modules'];
      \rsort($modules);
    }
    
    $args = array();
    if (\func_num_args() > 1) {
      $args = \func_get_args();
      \array_shift($args);
    }

    // Cache
    if (!\array_key_exists($methodName, $methodBinding)) {
      foreach ($modules as $module) {
        $class = $module['class'];
        if (\is_callable(array($class, $methodName))) {
          $method = array($class, $methodName);
          break;
        }
      }
      $methodBinding[$methodName] = $method;
    }
    
    return (!empty($methodBinding[$methodName]))
      ? \call_user_func_array($methodBinding[$methodName], $args)
      : null;
  }
  
  /**
   * Invokes a controller method.
   * <p>Searches for module classes implementing the method and runs every
   *  method following the modules priority order.</p>
   * <p>This function takes an unlimited number of arguments.</p>
   * <p>Every argument (apart from the method name) is passed to the module 
   *  class method.</p>
   * 
   * <p>Usage example:</p>
   * <code>
   * <pre>
   *  class Module1 {
   *    public static function x() {
   *      return array(
   *        'a' => 'module1',
   *        'b' => 'module1'
   *      );
   *    }
   *  }
   *  class Module2 {
   *    public static function x() {
   *      return array(
   *        'b' => 'module2',
   *        'c' => 'module2'
   *      );
   *    }
   *  }
   *  class Module3 {
   *    public static function x() {
   *      return 'module3';
   *    }
   *  }
   *  class Module4 {
   *    public static function x() {
   *      return null;
   *    }
   *  }
   * </pre>
   * </code>
   * 
   * <p>Assuming modules are all enabled and priority order is 
   *  Module1 > Module2 > Module3 > Module4, then:</p>
   * 
   * <pre>print_r(Main::invokeMethodAllMerge('x'));</pre>
   * 
   * <p>Will print out:</p>
   * 
   * <pre>
   * array (
   *   0 => null
   *   1 => 'module3',
   *   2 => array('b' => 'module2', 'c' => 'module2'),
   *   3 => array('a' => 'module1', 'b' => 'module1'),
   * )
   * </pre>
   * @param string $methodName Name of the module class method
   * @return Returns an array of values returned by module classes
   */
  public static function invokeMethodAll($methodName) {
    // Cache - array ('method name' => callable[])
    static $methodBinding = array();
    
    if (!\array_key_exists($methodName, $methodBinding)) {
      $methodBinding[$methodName] = array();
      
      $configuration = self::configuration();
    
      foreach ($configuration['modules'] as $module) {
        $class = $module['class'];
        if (\is_callable(array($class, $methodName))) {
          $methodBinding[$methodName][] = array($class, $methodName);
        }
      }
    }
    
    $results = array();
    if (!empty($methodBinding[$methodName])) {
      // Arguments to be passed
      $args = array();
      if (\func_num_args() > 1) {
        $args = \func_get_args();
        \array_shift($args);
      }

      foreach ($methodBinding[$methodName] as $handler) {
        $x = \call_user_func_array($handler, $args);
        $results[] = $x;
      }
    }
    return $results;
  }
  
  /**
   * Invokes a controller method.
   * <p>Searches for module classes implementing the method and runs every
   *  method following the modules priority order.</p>
   * <p>This function takes an unlimited number of arguments.</p>
   * <p>Every argument (apart from the method name) is passed to the module 
   *  class method.</p>
   * <p>
   * This method progressively merges results rather than returning their list.
   * To get the list containing every result please use invokeMethodAll.
   * <br/>
   * If an invoked controller method doesn't return an array, it will not be 
   *  used to produce the resulting array.
   * </p>
   * 
   * <p>Usage example:</p>
   * <code>
   * <pre>
   *  class Module1 {
   *    public static function x() {
   *      return array(
   *        'a' => 'module1',
   *        'b' => 'module1'
   *      );
   *    }
   *  }
   *  class Module2 {
   *    public static function x() {
   *      return array(
   *        'b' => 'module2',
   *        'c' => 'module2'
   *      );
   *    }
   *  }
   *  class Module3 {
   *    public static function x() {
   *      return 'module3';
   *    }
   *  }
   *  class Module4 {
   *    public static function x() {
   *      return null;
   *    }
   *  }
   * </pre>
   * </code>
   * 
   * <p>Assuming modules are all enabled and priority order is 
   *  Module1 > Module2 > Module3 > Module4, then:</p>
   * 
   * <pre>print_r(Main::invokeMethodAllMerge('x'));</pre>
   * 
   * <p>Will print out:</p>
   * 
   * <pre>
   * array (
   *   'a' => 'module1',
   *   'b' => 'module1',
   *   'c' => 'module2'
   * )
   * </pre>
   * @param string $methodName Name of the module class method
   * @return array Returns an array of non-null values returned by module classes
   */
  public static function invokeMethodAllMerge($methodName) {
    // Cache - array ('method name' => callable[])
    static $methodBinding = array();
    
    if (!\array_key_exists($methodName, $methodBinding)) {
      $methodBinding[$methodName] = array();
      
      $configuration = self::configuration();
    
      foreach ($configuration['modules'] as $module) {
        $class = $module['class'];
        if (\is_callable(array($class, $methodName))) {
          $methodBinding[$methodName][] = array($class, $methodName);
        }
      }
    }
    
    $results = array();
    if (!empty($methodBinding[$methodName])) {
      // Arguments to be passed
      $args = array();
      if (\func_num_args() > 1) {
        $args = \func_get_args();
        \array_shift($args);
      }

      foreach ($methodBinding[$methodName] as $handler) {
        $x = \call_user_func_array($handler, $args);
        if (\is_array($x)) {
          $results = $x + $results;
        }
      }
    }
    return $results;
  }
  
  /**
   * Invokes a model method.
   * <p>Searches for model classes implementing the method and runs every method
   *  following the modules priority order.</p>
   * <p>This function takes an unlimited number of arguments.</p>
   * <p>Every argument (apart from the method name) is passed to the module 
   *  class method.</p>
   * @param string $methodName Name of the model class method
   * @return array Returns an array of non-null values returned by model classes
   */
  public static function raiseModelEvent($methodName) {
    // Cache - array ('method name' => callable[])
    static $methodBinding = array();
    
    if (!\array_key_exists($methodName, $methodBinding)) {
      $methodBinding[$methodName] = array();
      
      $configuration = self::configuration();
    
      foreach ($configuration['modelClasses'] as $class) {
        if (\is_callable(array($class, $methodName))) {
          $methodBinding[$methodName][] = array($class, $methodName);
        }
      }
    }
    
    $results = array();
    if (!empty($methodBinding[$methodName])) {
      // Arguments to be passed
      $args = array();
      if (\func_num_args() > 1) {
        $args = \func_get_args();
        \array_shift($args);
      }

      foreach ($methodBinding[$methodName] as $handler) {
        $x = \call_user_func_array($handler, $args);
        if (!\is_null($x)) {
          $results[] = $x;
        }
      }
    }
    return $results;
  }
  
  /**
   * Invokes a theme method.
   * <p>Runs the method $methodName in the current theme class.</p>
   * <p>This function takes an unlimited number of arguments.</p>
   * <p>Every argument (apart from the method name) is passed to the theme
   *  class method.</p>
   * @param string $methodName Name of the theme class method
   */
  public static function raiseThemeEvent($methodName) {
    $args = \func_get_args();
    array_shift($args);
    $cname = '\theme\\' . self::getTheme() . '\\Theme';
    if (\class_exists($cname) && \is_callable($cname . '::' . $methodName)) {
      \call_user_func_array($cname . '::' . $methodName, $args);
    }
  }
  
  /**
   * Returns every 'model class' declard in active modules.
   * @return array List of model classes
   */
  public static function getModelClasses() {
    $c = self::configuration();
    return $c['modelClasses'];
  }
  
  /**
   * Returns every 'view class' declared in active modules.
   * @return array List of classes
   */
  public static function getViewClasses() {
    $c = self::configuration();
    return $c['viewClasses'];
  }

  /**
   * Returns an instance of the template manager.
   * <p>Implements the singleton design pattern always returning the same 
   *  instance.</p>
   * @return TemplateManager Template manager
   */
  public static function getTemplateManager() {
    static $tpl = null;
    if (\is_null($tpl)) {
      $tpl = new TemplateManager();
    }
    return $tpl;
  }
  
  /**
   * Returns the absolute path to the temp folder. 
   * <p>Typically this is used for temporary application data.</p>
   * @param string $path Subpath
   * @return string Temp path
   */
  public static function tempPathAbs($path = '') {
    return self::getPathAbsolute('temp/' . self::stripInitialSlash($path));
  }
  
  /**
   * Returns the relative path to the temp folder. 
   * <p>Typically this is used for temporary application data.</p>
   * @param string $path Subpath
   * @return string Temp path
   */
  public static function tempPathRel($path = '') {
    return self::getPathRelative('temp/' . self::stripInitialSlash($path));
  }
  
  /**
   * Returns the internal path to the temp folder. 
   * <p>Typically this is used for temporary application data.</p>
   * @param string $path Subpath
   * @return string Temp path
   */
  public static function tempPath($path = '') {
    return self::getPathInternal('temp/' . self::stripInitialSlash($path));
  }
  
  /**
   * Returns the absolute path to the data folder. 
   * <p>Typically this is used for file upload and other application data.</p>
   * @param string $path Subpath
   * @return string Data path
   */
  public static function dataPathAbs($path = '') {
    return self::getPathAbsolute(self::stripFinalSlash(self::setting('filesDir', 'data')) . '/' . self::stripInitialSlash($path));
  }
  
  /**
   * Returns the relative path to the data folder. 
   * <p>Typically this is used for file upload and other application data.</p>
   * @param string $path Subpath
   * @return string Data path
   */
  public static function dataPathRel($path = '') {
    return self::getPathRelative(self::stripFinalSlash(self::setting('filesDir', 'data')) . '/' . self::stripInitialSlash($path));
  }
  
  /**
   * Returns the internal path to the data folder. 
   * <p>Typically this is used for file upload and other application data.</p>
   * @param string $path Subpath
   * @return string Data path
   */
  public static function dataPath($path = '') {
    return self::getPathInternal(self::stripFinalSlash(self::setting('filesDir', 'data')) . '/' . self::stripInitialSlash($path));
  }
  
  /**
   * Returns the absolute path for a resource inside the module directory.
   * @param string $moduleName Module name
   * @param string $path Path relative to the module directory
   * @return string Path
   */
  public static function modulePathAbs($moduleName, $path = '') {
    $module = self::getModule($moduleName);
    return self::getPathAbsolute($module['path'] . self::stripInitialSlash($path));
  }
  
  /**
   * Returns the relative path for a resource inside the module directory.
   * @param string $moduleName Module name
   * @param string $path Path relative to the module directory
   * @return string Path
   */
  public static function modulePathRel($moduleName, $path = '') {
    $module = self::getModule($moduleName);
    return self::getPathRelative($module['path'] . self::stripInitialSlash($path));
  }
  
  /**
   * Returns the internal path for a resource inside the module directory.
   * @param string $moduleName Module name
   * @param string $path Path relative to the module directory
   * @return string Path
   */
  public static function modulePath($moduleName, $path = '') {
    $module = self::getModule($moduleName);
    return self::getPathInternal($module['path'] . self::stripInitialSlash($path));
  }
  
  /**
   * Returns the module namespace.
   * Usage example:
   * <code>
   * // The following code will print: \module\core\controller\
   * echo \system\Module::getNamespace('core', 'controller');
   * </code>
   * @param string $moduleName Module name
   * @return string Module namespace
   */
  public static function moduleNamespace($moduleName) {
    $subpaths = func_get_args();
    unset($subpaths[0]);
    $module = self::getModule($moduleName);
    $namespace = $module['ns'];
    foreach ($subpaths as $subpath) {
      $namespace .= $subpath . '\\';
    }
    return $namespace;
  }
  
  /**
   * Returns the absolute path for a resource inside the theme directory.
   * @param string $path Path relative to the theme directory
   * @return string Path
   */
  public static function themePathAbs($path = '') {
    return self::getPathAbsolute('theme/' . self::getTheme() . '/' . self::stripInitialSlash($path));
  }
  
  /**
   * Returns the relative path for a resource inside the theme directory.
   * @param string $path Path relative to the theme directory
   * @return string Path
   */
  public static function themePathRel($path = '') {
    return self::getPathRelative('theme/' . self::getTheme() . '/' . self::stripInitialSlash($path));
  }
  
  /**
   * Returns the internal path for a resource inside the theme directory.
   * @param string $path Path relative to the theme directory
   * @return string Path
   */
  public static function themePath($path = '') {
    return self::getPathInternal('theme/' . self::getTheme() . '/' . self::stripInitialSlash($path));
  }
  
  /**
   * Returns the theme
   * @return string Theme in use
   */
  public static function getTheme() {
    if (!self::setting('theme', false)) {
      throw new InternalError('Undefined theme');
    }
    return self::setting('theme');
  }
  
  /**
   * Returns an internal path.
   * @param string $path Path is assumed to be internal
   * @return string Internal path
   */
  public static function getPathInternal($path) {
    str_replace('\\', '/', $path);
    return self::stripInitialSlash($path);
  }
  
  /**
   * Returns a virtual path.
   * @param string $path Path is assumed to be internal
   * @param string $virtualDir Virtual dir (can be specified to change virtual 
   *  directry)
   * @return string Virtual path
   */
  public static function getPathVirtual($path, $virtualDir = null) {
    $virtualDir = empty($virtualDir)
      ? self::getVirtualBaseDir()
      : self::stripTrailingSlashes($virtualDir) . '/';
    return self::getBaseDir() . $virtualDir . self::getPathInternal($path);
  }
  
  /**
   * Returns a URL based on the specified language
   * @param string $lang Language
   * @param string $path Path [optional]
   * @return string URL
   * @throws InternalError
   */
  public static function getLangUrl($lang, $path = '') {
    $langs = self::setting('languages', array());
    if (!isset($langs[$lang])) {
      throw new InternalError('Language <em>@lang</em> not found', array('@lang' => $lang));
    }
    return self::stripFinalSlash($langs[$lang]) . '/' . $path;
  }
  
  /**
   * Returns a list of available languages
   * @return array
   */
  public static function getLanguages() {
    return \array_keys(self::setting('languages', array()));
  }
  
  /**
   * Returns a path relative to the ciderbit root directory
   * @param string $path Path is assumed to be internal
   * @return string Relative path
   */
  public static function getPathRelative($path) {
    return self::getBaseDir() . self::getPathInternal($path);
  }

  /**
   * Returns the real path
   * @param string $path Path is assumed to be internal
   * @return string Real path
   */
  public static function getPathAbsolute($path) {
    str_replace('\\', '/', $path);
    return self::getBaseDirAbs() . self::getPathInternal($path);
  }
  
  /**
   * Removes the initial and the final slashes from a string
   * @param string $path Path
   * @return string Path
   */
  private static function stripTrailingSlashes($path) {
    return self::stripFinalSlash(self::stripInitialSlash($path));
  }
  
  /**
   * Removes the initial slash and replace backslashes with shashes
   * @param string $path Path
   * @return string Path
   */
  private static function stripInitialSlash($path) {
    return (!empty($path) && substr($path, 0, 1) == '/')
      ? substr($path, 1)
      : $path;
  }
  
  /**
   * Removes the final slash and replace backslashes with shashes
   * @param string $path Path
   * @return string Path
   */
  private static function stripFinalSlash($path) {
    return (!empty($path) && substr($path, -1, 1) == '/')
      ? substr($path, 0, -1)
      : $path;
  }

  private static function cachePath($name) {
    $cacheDir = self::setting('cacheDir');
    return !empty($cacheDir) ? $cacheDir . $name . '.var' : false;
  }
  
  /**
   * Checks whether a cached variable exist.
   * @param string $name Variable name
   * @return boolean TRUE if the variable exists
   */
  public static function cacheExists($name) {
    $cacheDir = self::setting('cacheDir');
    return !empty($cacheDir) 
      ? \file_exists(self::cachePath($name))
      : false;
  }
  
  /**
   * Gets a cached variable. Variable are stored in the file system via method 
   *  setCache.
   * @param string $name Key
   * @param mixed $default Default value (returned in case the variable is not
   *  defined)
   * @return mixed Value
   */
  public static function getCache($name, $default = null) {
    if (self::cacheExists($name)) {
      $fp = \fopen(self::cachePath($name), 'r');
      $content = "";
      while ($s = \fread($fp, 4096)) {
        $content .= $s;
      }
      fclose($fp);
      return \unserialize($content);
    }
    else {
      return $default;
    }
  }
  
  /**
   * Caches a variable. Variables are stored in the file system and can be 
   *  accessed via method getCache.
   * @param string $name Variable name
   * @param mixed $value Value
   */
  public static function setCache($name, $value) {
    $content = \serialize($value);
    $cacheDir = self::setting('cacheDir');
    if (!empty($cacheDir)) {
      $fp = \fopen(self::cachePath($name), 'w');
      \fwrite($fp, $content);
      \fclose($fp);
    }
  }
  
  /**
   * Deletes a cached variable.
   * @param string $name Variable name
   */
  public static function delCache($name) {
    if (self::cacheExists($name)) {
      \unlink(self::cachePath($name));
    }
  }
  
  /**
   * Flushes the cache
   */
  public static function flushCache() {
    if (self::setting('cacheDir')) {
      foreach (\glob(self::setting('cacheDir') . '/*') as $file) {
        if (\is_file($file) && \substr($file, -4) == '.var') {
          \unlink($file);
        }
      }
    }
  }
  
  /**
   * Returns the ciderbit session.
   * <p>Examples:
   * <pre><code>
   * // Returns the entire ciderbit session
   * session();
   * 
   * // Returns the entire 'core' module array
   * // If it hasn't been initialized yet, it will be set to a empty array
   * session('core');
   * 
   * // Returns the 'test' variable in the 'core' module array
   * // If it hasn't been initialized yet, it will be set to the $default 
   * //  parameter value
   * session('system', 'test');
   * 
   * NB.
   * This method always returns a reference. This means that the following code:
   * $x = &Main::session('test', 'x');
   * $x = 'asd';
   * echo Main::session('test', 'x');
   * Will print out 'asd'
   * </code></pre>
   * </p>
   * @param string $module Module [optional, if not passed the whole ciderbit 
   *  session is returned]
   * @param string $key Key [optional, if not passed the whole module session
   *  is returned]
   * @param mixed $default Default key value
   * @return mixed Session
   */
  public static function &session($module = null, $key = null, $default = null) {
    if (!isset($_SESSION['ciderbit'])) {
      $_SESSION['ciderbit'] = array();
    }
    if (!empty($module)) {
      // Module has been transmitted
      if (!isset($_SESSION['ciderbit'][$module])) {
        // Initialize if does not exist
        $_SESSION['ciderbit'][$module] = array();
      }
      if (!empty($key)) {
        // Key has been transmitted
        if (!isset($_SESSION['ciderbit'][$module][$key])) {
          // Initialize if does not exist
          $_SESSION['ciderbit'][$module][$key] = $default;
        }
        // Return the key value
        return $_SESSION['ciderbit'][$module][$key];
      }
      else {
        // Return the module array
        return $_SESSION['ciderbit'][$module];
      }
    }
    else {
      // Return the whole ciderbit session
      return $_SESSION['ciderbit'];
    }
  }
  
  /**
   * Get a session variable.
   * @param string $module Module name
   * @param string $key Variable name
   * @param mixed $default Default value
   * @return mixed Variable value
   */
  public static function getSession($module, $key, $default = null) {
    return self::session($module, $key, $default);
  }
  
  /**
   * Set a session variable
   * @param string $module Module name
   * @param string $key Variable name
   * @param mixed $value Value
   */
  public static function setSession($module, $key, $value) {
    $var = &self::session($module, $key);
    $var = $value;
  }
  
  /**
   * Delete a session variable
   * @param string $module Module name
   * @param string $key Variable name
   */
  public static function delSession($module, $key=null) {
    if (empty($key)) {
      $session = &self::session();
      unset($session[$module]);
    }
    else {
      $session = &self::session($module);
      unset($session[$key]);
    }
  }

  /**
   * Returns all system messages
   * @return array Messages
   */
  private static function &getMessages() {
    return self::session('system', 'messages', array());
  }
  
  /**
   * Pushes a system message (FIFO queue)
   * @param array $message Message
   */
  public static function pushMessage($message, $class = 'info') {
    $messages = &self::getMessages();
    
    if (\is_array($message) || \is_object($message)) {
      $message =
        '<pre>'
        . utils\Utils::varDump($message)
        . '</pre>';
    }
    
    $messages[] = array(
      'class' => $class,
      'message' => $message
    );
  }

  /**
   * Consumes a system message (FIFO queue)
   * @return array Message (or NULL if the queue is empty)
   */
  public static function popMessage() {
    $messages = &self::getMessages();
    
    return (!empty($messages))
      ? \array_shift($messages)
      : null;
  }
  
  /**
   * Counts system messages
   * @return int Number of messages to be consumed
   */
  public static function countMessages() {
    return \count(self::getMessages());
  }
  
  /**
   * Gets application settings
   * @return Settings Application settings
   */
  public static function settings() {
    return Settings::getInstance();
  }
  
  /**
   * Gets a global setting
   * @param string $name Name
   * @param mixed $default Default value to be returned for undefined settings
   * @return mixed Global setting value or $default for undefined settings
   */
  public static function setting($name, $default = null) {
    return self::settings()->get($name, $default);
  }
  
  /**
   * Gets a site setting
   * @param string $name Name
   * @param mixed $default Default value to be returned for undefined settings
   * @return mixed Global setting value or $default for undefined settings
   */
  public static function getSetting($name, $value) {
    self::settings()->set($name, $value);
  }
  
  /**
   * Sets a site setting
   * @param string $name Name
   * @param mixed $value Value to set
   */
  public static function setSetting($name, $value) {
    self::settings()->set($name, $value);
  }
  
  /**
   * @return string Path to the base directory
   */
  private static function getBaseDirAbs() {
    return \str_replace('\\', '/', \dirname(\dirname(__FILE__))) . '/';
  }
  
  /**
   * @return string Path to the base directory relative to the web root
   */
  private static function getBaseDir() {
    $baseDir = str_replace('\\', '/', self::setting('baseDir', '/'));
    if (empty($baseDir) || $baseDir == '/') {
      return '/';
    }
    else {
      return '/' . self::stripTrailingSlashes($baseDir) . '/';
    }
  }
  
  /**
   * @return string Virtual directory appended to the URL
   */
  private static function getVirtualBaseDir() {
    $baseDir = str_replace('\\', '/', self::setting('virtualDir', ''));
    if (empty($baseDir) || $baseDir == '/') {
      return '';
    }
    else {
      return self::stripTrailingSlashes($baseDir) . '/';
    }
  }
  
  /**
   * @return string Domain e.g. 'en.ciderbit.local'
   */
  public static function getDomain() {
    return $_SERVER['HTTP_HOST'];
  }
  
  /**
   * @return string Domain e.g. 'en.ciderbit.local'
   */
  public static function getRequestUri() {
    return self::stripInitialSlash($_SERVER['REQUEST_URI']);
  }
  
  /**
   * @return string Home URL
   */
  public static function getBaseUrl() {
    return self::getDomain() . self::getBaseDir();
  }
}
