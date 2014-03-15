<?php
namespace system;

use system\Component;

class Main {
  private static $timeRequestStack = array();
  private static $componentStack = array();
  
  /**
   * @return int UNIX timestamp of the request
   */
  public static function getTimeRequest() {
    return \round(\end(self::$timeRequestStack), 0);
  }
  
  /**
   * @param boolean $absolute TRUE if you wish to get the number of seconds from
   *  the first time the 'run' method is called.
   * @return float Number of seconds of the execution time (returns a float 
   *  rounded to the 3rd decimal (representing microseconds)
   */
  public static function getExecutionTime($absolute = false) {
    if (empty(self::$timeRequestStack)) {
      return false;
    }
    return \round(\microtime() - ($absolute ? self::$timeRequestStack[0] : \end(self::$timeRequestStack)), 3);
  }
  
  /**
   * Loads the module configuration by parsng its info.yml file.
   * @param string $moduleName Module name
   * @param string $moduleNs Module namespace
   * @param string $moduleDir Module directory
   * @return array Module info or null if something goes wrong
   */
  private static function loadModuleInfo($moduleName, $moduleNs, $moduleDir) {
    if (\is_dir($moduleDir)) {
      $infoPath = $moduleDir . 'info.yml';
      if (\file_exists($infoPath)) {
        try {
          $info = \system\yaml\Yaml::parse($infoPath);
          return array(
            'name' => $moduleName,
            'dir' => $moduleDir,
            'ns' => $moduleNs,
            'info' => $info
          );
        } catch (\Exception $ex) {
          // skip this module
        }
      }
    }
    return null;
  }
  
  /**
   * Generates the module configuration array.
   * <p>Parses each info.yml looking for active modules.</p>
   * @return array
   */
  private static function loadConfiguration() {
    
    $WEIGHTS = array();
    
    $MODULES = array();
    $MODEL_CLASSES = array();
    $VIEW_CLASSES = array();
    $CONTROLLER_CLASSES = array();
    $URLS = array();
    
    $modulesNs = 'module';
    $modulesDir = 'module';
    
    $modules = array();

    $d = \opendir($modulesDir);
    while (($moduleName = \readdir($d))) {
      if ($moduleName != 'system') {
        $module = self::loadModuleInfo(
          $moduleName, 
          $modulesNs . '\\' . $moduleName . '\\',
          $modulesDir . '/' . $moduleName . '/'
        );
        if ($module) {
          $modules[$moduleName] = $module;
        }
      }
    }
    
    $modules['system'] = self::loadModuleInfo('system', 'system\\', 'system/');
    
    foreach ($modules as $module) {
      $moduleName = $module['name'];
      $moduleDir = $module['dir'];
      $moduleNs = $module['ns'];
      $moduleInfo = $module['info'];
      
      try {
        if (!\is_array($moduleInfo)) {
          throw new \system\exceptions\InternalError('Unable to parse <em>@name</em> module info.', array('@name' => $moduleName));
        }

        $enabled = \cb\array_item('enabled', $moduleInfo, array('default' => true));

        if (!$enabled) {
          // just ignore the module
          continue;
        }

        $moduleClass = $moduleNs . \cb\array_item('class', $moduleInfo, array('required' => true));

        if (!\class_exists($moduleClass)) {
          throw new \system\exceptions\InternalError('Class <em>@name</em> does not exist.', array('@name' => $moduleClass));
        }

        $weight = (int)\cb\array_item('weight', $moduleInfo, array('default' => 0));

        // model class
        $modelNs = $moduleNs . (isset($moduleInfo['modelNs'])
          ? $moduleInfo['modelNs'] . '\\'
          : '');
        $modelClass = \cb\array_item('modelClass', $moduleInfo, array(
          'default' => null,
          'prefix' => $modelNs
        ));
        if (!\is_null($modelClass) && !\class_exists($modelClass)) {
          throw new \system\exceptions\InternalError('Class <em>@name</em> does not exist.', array('@name' => $modelClass));
        }
        // templates class (API)
        $viewNs = $moduleNs . (isset($moduleInfo['viewNs'])
          ? $moduleInfo['viewNs'] . '\\'
          : '');
        $viewClass = \cb\array_item('viewClass', $moduleInfo, array(
          'default' => null,
          'prefix' => $viewNs
        ));
        if (!\is_null($viewClass) && !\class_exists($viewClass)) {
          throw new \system\exceptions\InternalError('Class <em>@name</em> does not exist.', array('@name' => $viewClass));
        }

        $templatesPath = \cb\array_item('templatesPath', $moduleInfo, array(
          'default' => null,
          'prefix' => $moduleDir
        ));
        if (!\is_null($templatesPath) && !\is_dir($templatesPath)) {
          throw new \system\exceptions\InternalError('Directory <em>@path</em> not found', array('@path' => $templatesPath));
        }
        $templatesPath = str_replace('\\', '/', $templatesPath);
        if (substr($templatesPath, -1) != '/') {
          $templatesPath .= '/';
        }

        // components namespace
        $componentsNs = $moduleNs . (isset($moduleInfo['componentsNs'])
          ? $moduleInfo['componentsNs'] . '\\'
          : '');

        $components = \cb\array_item('components', $moduleInfo, array('default' => array()));
        foreach ($components as $componentName => $component) {
          $componentClass = \cb\array_item('class', $component, array(
              'required' => true, 
              'prefix' => $componentsNs
          ));
          if (!\class_exists($componentClass)) {
            throw new \system\exceptions\InternalError('Class <em>@name</em> does not exist.', array('@name' => $componentClass));
            unset($components[$componentName]);
            continue;
          }
          $components[$componentName] = array(
            'name' => $componentName,
            'class' => $componentClass,
            'pages' => \cb\array_item('pages', $component, array('default' => array()))
          );
          foreach ($component['pages'] as $i => $page) {
            $components[$componentName]['pages'][$i] = array(
              'url' => \cb\array_item('url', $page, array('required' => true)),
              'name' => $componentName,
              'action' => \cb\array_item('action', $page, array('required' => true))
            );

            $rules = array(
              "@strid" => "[a-zA-Z0-9\-_]+",
              "@urlarg" => "[a-zA-Z0-9\-_.]+",
            );

            foreach ($rules as $ruleName => $replace) {
              $components[$componentName]['pages'][$i]['url'] = \str_replace(
                $ruleName,
                $replace,
                $components[$componentName]['pages'][$i]['url']
              );
            }
          }
        }

        $WEIGHTS[$weight][$moduleName] = array(
          'name' => $moduleName,
          'path' => $moduleDir,
          'ns' => $moduleNs,
          'class' => $moduleClass,
          'weight' => $weight,
          'modelNs' => $modelNs,
          'modelClass' => $modelClass,
          'viewNs' => $viewNs,
          'viewClass' => $viewClass,
          'templatesPath' => $templatesPath,
          'componentsNs' => $componentsNs,
          'components' => $components
        );

      } catch (\Exception $ex) {
        throw $ex;
      }
    }
    \closedir($d);
    
    \ksort($WEIGHTS);
    // discending order: heavier modules will override lighter ones
    foreach ($WEIGHTS as $weight => $modules) {
      foreach ($modules as $module) {
        $MODULES[$module['name']] = $module;
        foreach ($module['components'] as $component) {
          foreach ($component['pages'] as $i => $page) {
            $URLS[$page['url']] = array(
              'module' => array(
                'name' => $module['name'],
                'class' => $module['class']
              ),
              'component' => array(
                'name' => $component['name'],
                'class' => $component['class']
              ),
              'action' => $page['action']
            );
          }
        }
        $CONTROLLER_CLASSES[] = $module['class'];
        if ($module['modelClass']) {
          $MODEL_CLASSES[] = $module['modelClass'];
        }
        if ($module['viewClass']) {
          $VIEW_CLASSES[] = $module['viewClass'];
        }
      }
    }
    
    // We want to search for URLS according to modules weight descending
    
    return array(
      'modules' => $MODULES,
      'urls' => \array_reverse($URLS),
      'tables' => self::loadModelCfg($MODULES),
      'modelClasses' => $MODEL_CLASSES,
      'templates' => self::loadViewCfg($MODULES),
      'viewClasses' => \array_reverse($VIEW_CLASSES)
    );
  }
  
  /**
   * Incrementally merges tables definition into $TABLES.
   * @param array $tables Current table definition
   * @param array $TABLES Incremental tables definition (will be updated by 
   *  merging with the $tables parameter)
   * @throws exceptions\InternalError In case something looks wrong in the table
   *  definition
   */
  private static function setTables($tables, &$TABLES) {
    foreach ($tables as $tableName => $table) {
      if (!\array_key_exists($tableName, $TABLES)) {
        $TABLES[$tableName] = array(
          'fields' => array(),
          'keys' => array(),
          'relations' => array(),
          'virtuals' => array()
        );
      }
      $fields = \cb\array_item('fields', $table, array('default' => array()));
      foreach ($fields as $fieldName => $field) {
        if (!\is_array($field)) {
          throw new exceptions\InternalError('Invalid <em>@field</em> field definition for table <em>@table</em>', array(
            '@field' => $fieldName, 
            '@table' => $tableName
          ));
        }
        $prevFieldInfo = \cb\array_item($fieldName, $TABLES[$tableName]['fields'], array('default' => array()));
        $TABLES[$tableName]['fields'][$fieldName] = $field + $prevFieldInfo;
      }
      $keys = \cb\array_item('keys', $table, array('default' => array(), 'type' => 'array'));
      foreach ($keys as $keyName => $key) {
        $TABLES[$tableName]['keys'][$keyName] = $key;
      }
      
      $relations = \cb\array_item('relations', $table, array('default' => array(), 'type' => 'array'));
      foreach ($relations as $relationName => $relation) {
        $TABLES[$tableName]['relations'][$relationName] = $relation;
      }
      
      $virtuals = \cb\array_item('virtuals', $table, array('default' => array(), 'type' => 'array'));
      foreach ($virtuals as $virtualName => $virtual) {
        $TABLES[$tableName]['virtuals'][$virtualName] = $virtual;
      }
    }
  }
  
  /**
   * Generates the model configuration array.
   * <p>Parses each model.yml file defined on every active module.</p>
   * <p>If two modules declare the same table, the two table definitions are
   *  merged.</p>
   * <p>If two modules declare also the same table property (as a field a key or 
   *  a relation) the two definitions are merged as well.</p>
   * @params array $modules Modules configuration array
   * @return array Associative array (<table name> => <table definition>, ...)
   */
  private static function loadModelCfg($modules) {
    $TABLES = array();
    
    foreach ($modules as $module) {
      if (\file_exists($module['path'] . 'model.yml')) {
        try {
          $model = \system\yaml\Yaml::parse($module['path'] . "model.yml");
          self::setTables(
            \cb\array_item('tables', $model, array('required' => true)),
            $TABLES
          );
        } catch (\Exception $ex) {
          throw $ex;
        }
      }
    }
    
    return $TABLES;
  }
  
  /**
   * Generates the view configuration array.
   * @param array $modules Modules configuration array
   * @return array List of available templates 
   *  [template name] => [template path]
   */
  private static function loadViewCfg($modules) {
    $TEMPLATES = array();
    // loop over active modules templates keeping a associative array <template name> => <template path>
    // so modules with higher priority can override templates
    // moreover, it will be easier for the template manager to check a template really exists and to look for a template 
    foreach ($modules as $module) {
      if ($module['templatesPath']) {
        $d = \opendir($module['templatesPath']);
        while (($fileName = \readdir($d))) {
          if (\substr($fileName, -8) == '.tpl.php') {
            $templateName = \substr($fileName, 0, -8); // strip .tpl.php
            $TEMPLATES[$templateName] = $module['templatesPath'] . $fileName;
          }
        }
        \closedir($d);
      }
    }

    $themeTplPath = \system\Theme::getAbsPath('templates/');
    if (!\is_null($themeTplPath) && \is_dir($themeTplPath)) {
      $d = \opendir($themeTplPath);
      while (($fileName = \readdir($d))) {
        if (\substr($fileName, -8) == '.tpl.php') {
          $templateName = \substr($fileName, 0, -8);
          $TEMPLATES[$templateName] = $themeTplPath . $fileName;
        }
      }
      \closedir($d);
    }
    return $TEMPLATES;
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
  public static function configuration() {
    static $configuration = null;
    
    if (\is_null($configuration) && self::setting('coreCache', true)) {
      $configuration = self::getVariable('system-configuration', null);
    }
    if (\is_null($configuration)) {
      $configuration = self::loadConfiguration();
      if (self::setting('coreCache')) {
        self::setVariable('system-configuration', $configuration);
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
   * @throws \system\exceptions\InternalError In case the module does not exist
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
      throw new \system\exceptions\InternalError('Module <em>@name</em> not found.', array('@name' => $moduleName));
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
   * @throws \system\exceptions\InternalError In case the template doesn't exist
   */
  public static function getTemplate($templateName) {
    if (\is_null($templateName)) {
      return null;
    }
    $config = self::configuration();
    if (isset($config['templates'][$templateName])) {
      return $config['templates'][$templateName];
    } else {
      throw new \system\exceptions\InternalError('Template <em>@name</em> not found.', array('@name' => $templateName));
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
   * @throws \system\exceptions\InternalError In case the table does not exist
   *  or its defining module isn't enabled.
   */
  public static function getTable($tableName) {
    if (self::tableExists($tableName)) {
      $c = self::configuration();
      return $c['tables'][$tableName];
    } else {
      throw new \system\exceptions\InternalError('Table <em>@name</em> not found.', array('@name' => $tableName));
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
    
    if ($url == self::getBaseDir()) {
      $url = '';
    } else if (\substr($url, 0, \strlen(self::getBaseDir())) == self::getBaseDir()) {
      $url = \substr($url, \strlen(self::getBaseDir()));
    }
    
    if (!empty($url)) {
      $x = \strstr($url, '?', true);
      if ($x !== false) {
        $url = $x;
      }
    }
    
    if (\is_null($urls)) {
      if (self::setting('coreCache')) {
        // Url cache
        $urls = self::getVariable('system-urls', array());
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
            self::setVariable("system-urls", $urls);
          }
          break;
        }
      }
    }
    return $urls[$url];
  }
  
  /**
   * Get the currently active component
   * @return \system\Component The most recent component which is currently 
   *  running or NULL if any component isn't running.
   */
  public static function getActiveComponent() {
    return \end(self::$componentStack);
  }
  
  /**
   * Get the main active component
   * @return \system\Component The oldest running component or NULL if any 
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
      $user = \system\utils\Login::getLoggedUser();
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
      $obj->initDatamodel(Main::invokeMethodAllMerge('initDatamodel'));
      
      if (!$obj->isNested()) {
        // Allows the theme to do special stuff before modules
        \system\Theme::preRun($obj);
        // Raise event onRun
        self::raiseControllerEvent('onRun', $obj);
        \system\Theme::onRun($obj);
      }

      $obj->process();
    }
    catch (\Exception $ex) {
      \array_pop(self::$componentStack);
      \array_pop(self::$timeRequestStack);
      throw $ex;
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
    
    if (!\array_key_exists($methodName, $results) && $staticCache && self::variableExists('system-static-method-all-merge-' . $methodName)) {
      // Cache level 2
      $results[$methodName] = self::getVariable('system-static-method-all-merge-' . $methodName);
    }
    
    if (!\array_key_exists($methodName, $results)) {
      $results[$methodName] = self::invokeMethodAllMerge($methodName, $staticCache);
      if ($staticCache) {
        self::setVariable('system-static-method-all-merge-' . $methodName, $results[$methodName]);
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
   *  file system (cache is implemented by using getVariable and setVariable 
   *  methods)
   * @return mixed Result
   */
  public static function invokeStaticMethod($methodName, $staticCache = true) {
    static $results = array(); // Cache level 1
    
    if (!\array_key_exists($methodName, $results) && $staticCache && self::variableExists('system-static-method-' . $methodName)) {
      // Cache level 2
      $results[$methodName] = self::getVariable('system-static-method-' . $methodName);
    }
    
    if (!\array_key_exists($methodName, $results)) {
      $results[$methodName] = self::invokeMethod($methodName);
      if ($staticCache) {
        self::setVariable('system-static-method-' . $methodName, $results[$methodName]);
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
   *  file system (cache is implemented by using getVariable and setVariable 
   *  methods)
   * @return mixed Result
   */
  public static function invokeStaticMethodAll($methodName, $staticCache = true) {
    static $results = array(); // Cache level 1
    
    if (!\array_key_exists($methodName, $results) && $staticCache && self::variableExists('system-static-method-all-' . $methodName)) {
      // Cache level 2
      $results[$methodName] = self::getVariable('system-static-method-all-' . $methodName);
    }
    
    if (!\array_key_exists($methodName, $results)) {
      $results[$methodName] = self::invokeMethodAll($methodName);
      if ($staticCache) {
        self::setVariable('system-static-method-all-' . $methodName, $results[$methodName]);
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
   * @return Returns the implementing method results.
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
   * @return Returns an array of non-null values returned by module classes
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
   * @param string $methodName Name of the module class method
   * @return Returns an array of non-null values returned by model classes
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
   * @return \system\view\TemplateManager Template manager
   */
  public static function getTemplateManager() {
    static $tpl = null;
    if (\is_null($tpl)) {
      $tpl = new \system\view\TemplateManager();
    }
    return $tpl;
  }
  
  /**
   * Returns the absolute path to the temp folder. 
   * <p>Typically this is used for temporary application data.</p>
   * @param string $path Path relative to the data folder
   * @return type
   */
  public static function tempPath($path = '') {
    return self::getBaseDirAbs() . 'temp/' . self::prepareUrl($path);
  }
  
  /**
   * Returns the absolute path to the data folder. 
   * <p>Typically this is used for file upload and other application data.</p>
   * @param string $path Path relative to the data folder
   * @return string Data path
   */
  public static function dataPath($path = '') {
    return self::getBaseDirAbs() . 'data/' . self::prepareUrl($path);
  }
  
  /**
   * Returns an internal URL
   * @param string $path Path is assumed to be relative to the ciderbit root 
   *  directory.
   * @return string URL
   */
  public static function getUrl($path) {
    return self::getBaseDir() . self::prepareUrl($path);
  }
  
  /**
   * Removes the initial slash and replace backslashes with shashes
   * @param string $path Path
   * @return string Path
   */
  private static function prepareUrl($path) {
    $path = str_replace('\\', '/', $path);
    return (!empty($path) && substr($path, 0, 1) == '/')
      ? substr($path, 1)
      : $path;
  }
  
  private static function urlIsExternal($uri) {
    // Return an external link if $path contains an allowed absolute URL. Only
    // call the slow drupal_strip_dangerous_protocols() if $path contains a ':'
    // before any / ? or #. Note: we could use url_is_external($path) here, but
    // that would require another function call, and performance inside url() is
    // critical.
    $colonpos = strpos($path, ':');
    return ($colonpos !== FALSE && !preg_match('![/?#]!', substr($path, 0, $colonpos)) && self::urlStripDangerousProtocols($path) == $path);
  }
  
  private static function urlStripDangerousProtocols($uri) {
    static $allowedProtocols;

    if (!isset($allowedProtocols)) {
      $allowedProtocols = array_flip(
        self::getVariable('system-filter-allowed-protocols', array(
          'ftp', 'http', 'https', 'irc', 'mailto', 'news', 'nntp', 'rtsp', 
          'sftp', 'ssh', 'tel', 'telnet', 'webcal'
        ))
      );
    }

    // Iteratively remove any invalid protocol found.
    do {
      $before = $uri;
      $colonpos = strpos($uri, ':');
      if ($colonpos > 0) {
        // We found a colon, possibly a protocol. Verify.
        $protocol = substr($uri, 0, $colonpos);
        // If a colon is preceded by a slash, question mark or hash, it cannot
        // possibly be part of the URL scheme. This must be a relative URL, which
        // inherits the (safe) protocol of the base document.
        if (preg_match('![/?#]!', $protocol)) {
          break;
        }
        // Check if this is a disallowed protocol. Per RFC2616, section 3.2.3
        // (URI Comparison) scheme comparison must be case-insensitive.
        if (!isset($allowedProtocols[strtolower($protocol)])) {
          $uri = substr($uri, $colonpos + 1);
        }
      }
    } while ($before != $uri);

    return $uri;
  }
  
  /**
   * Checks whether a cached variable exist.
   * @param string $name Variable name
   * @return boolean TRUE if the variable exists
   */
  public static function variableExists($name) {
    return \file_exists("config/vars/{$name}.var");
  }
  
  /**
   * Gets a cached variable. Variable are stored in the file system via method 
   *  setVariable.
   * @param string $name Key
   * @param mixed $default Default value (returned in case the variable is not
   *  defined)
   * @return mixed Value
   */
  public static function getVariable($name, $default = null) {
    if (self::variableExists($name)) {
      $fp = \fopen("config/vars/" . $name . ".var", "r");
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
   *  accessed via method getVariable.
   * @param string $name Key
   * @param mixed $value Value
   */
  public static function setVariable($name, $value) {
    $content = \serialize($value);

    $fp = \fopen("config/vars/" . $name . ".var", "w");
    \fwrite($fp, $content);
    \fclose($fp);
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
          $_SESSION['ciderbit'][$module][$key]= $default;
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
  public static function &getSession($module, $key, $default = null) {
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
  public static function unsetSession($module, $key=null) {
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
   * @return \system\Settings Application settings
   */
  public static function settings() {
    return \system\Settings::getInstance();
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
   * @return string Path to the base directory
   */
  public static function getBaseDirAbs() {
    return \str_replace('\\', '/', \dirname(\dirname(__FILE__))) . '/';
  }
  
  /**
   * @return string Path to the base directory relative to the web root
   */
  public static function getBaseDir() {
    return self::setting('baseDir', '/');
  }
  
  /**
   * @return string Domain e.g. 'en.ciderbit.local'
   */
  public static function getDomain() {
    return $_SERVER['HTTP_HOST'];
  }
  
  /**
   * @return string Home URL
   */
  public static function getBaseUrl() {
    return self::getDomain() . self::getBaseDir();
  }
}
