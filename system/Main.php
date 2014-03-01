<?php
namespace system;

use system\Component;

class Main {
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
   * Parses each info.yml looking for active modules
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
    
    \asort($WEIGHTS);
    // discending order: heavier modules will override lighter ones
    foreach ($WEIGHTS as $modules) {
      \asort($modules);
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
    
    // sorting the array by module weight descending
    //  this is because the view classes are designed for templates API,
    //  it makes more sense to search for the right api in hevier modules first
    \asort($VIEW_CLASSES);
    
    return array(
      'modules' => $MODULES,
      'urls' => $URLS,
      'tables' => self::loadModelCfg($MODULES),
      'modelClasses' => $MODEL_CLASSES,
      'templates' => self::loadViewCfg($MODULES),
      'viewClasses' => $VIEW_CLASSES
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
        $TABLES[$tableName]['fields'][$fieldName] = $prevFieldInfo + $field;
      }
      $keys = \cb\array_item('keys', $table, array('default' => array()));
      foreach ($keys as $keyName => $key) {
        if (!\is_array($key)) {
          throw new exceptions\InternalError('Invalid <em>@key</em> key definition for table <em>@table</em>', array(
            '@key' => $keyName, 
            '@table' => $tableName
          ));
        }
        $prevKeyInfo = \cb\array_item($keyName, $TABLES[$tableName]['keys'], array('default' => array()));
        $TABLES[$tableName]['keys'][$keyName] = $prevKeyInfo + $key;
      }
      $relations = \cb\array_item('relations', $table, array('default' => array()));
      foreach ($relations as $relationName => $relation) {
        if (!\is_array($relation)) {
          throw new exceptions\InternalError('Invalid <em>@relation</em> relation definition for table <em>@table</em>', array(
            '@relation' => $relationName,
            '@table' => $tableName
          ));
        }
        $prevRelationInfo = \cb\array_item($relationName, $TABLES[$tableName]['relations'], array('default' => array()));
        $TABLES[$tableName]['relations'][$relationName] = $prevRelationInfo + $relation;
      }
      $virtuals = \cb\array_item('virtuals', $table, array('default' => array()));
      foreach ($virtuals as $virtualName => $virtual) {
        if (!\is_array($virtual)) {
          throw new exceptions\InternalError('Invalid <em>@virtual</em> virtual field definition for table <em>@table</em>', array(
            '@virtual' => $virtualName,
            '@table' => $tableName
          ));
        }
        $prevVirtualInfo = \cb\array_item($virtualName, $TABLES[$tableName]['virtuals'], array('default' => array()));
        $TABLES[$tableName]['virtuals'][$virtualName] = $prevVirtualInfo + $virtual;
      }
    }
  }
  
  /**
   * Generates the model configuration array.
   * Parses each model.yml file defined on every active module.
   * If two modules declare the same table, the two table definitions are
   *  merged.
   * If two modules declare also the same table property (as a field a key or a 
   *  relation) the two definitions are merged as well.
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
   * NB. modules are sorted by weight ascending
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
    
    if (\is_null($configuration) && \config\settings()->CORE_CACHE) {
      $configuration = self::getVariable('system-configuration', null);
    }
    if (\is_null($configuration)) {
      $configuration = self::loadConfiguration();
      if (\config\settings()->CORE_CACHE) {
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
    return !\is_null(self::getComponent($url));
  }
  
  /**
   * Returns info about the component responsible for the URL.
   * This information are defined by info.yml files.
   * This method doesn't take into account unactive modules.
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
  public static function getComponent($url) {
    static $urls = null;
    
    if ($url == \config\settings()->BASE_DIR) {
      $url = '';
    } else if (\substr($url, 0, \strlen(\config\settings()->BASE_DIR)) == \config\settings()->BASE_DIR) {
      $url = \substr($url, \strlen(\config\settings()->BASE_DIR));
    }
    
    if (!empty($url)) {
      $x = \strstr($url, '?', true);
      if ($x !== false) {
        $url = $x;
      }
    }
    
    if (\is_null($urls)) {
      if (\config\settings()->CORE_CACHE) {
        // Url cache
        $urls = self::getVariable("system-urls", array());
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
          if (\config\settings()->CORE_CACHE) {
            self::setVariable("system-urls", $urls);
          }
          break;
        }
      }
    }
    return $urls[$url];
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
      $x = self::getComponent($url);
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
   * Runs the application
   * @param string $url URL
   * @param array $request Application request (default to $_REQUEST)
   */
  public static function run($url, $request = null) {
    if (\is_null($request)) {
      $request = $_REQUEST;
    }
    
    $component = self::getComponent($url);
    
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

      if (!$obj->isNested()) {
        // Allows the theme to do special stuff before modules
        \system\Theme::preRun($obj);
        // Raise event onRun
        self::raiseControllerEvent('onRun', $obj);
        \system\Theme::onRun($obj);
      }

      $obj->process();
    } else {
      \header("HTTP/1.0 404 Not Found");
      die();
    }
  }
  
  /**
   * Returns a cached module configuration value.
   * Runs invokeMethodAll and cached its results.
   * The way this method works is to progressively merge implementing methods
   *  array results.
   * Considering the two following module classes:
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
   * </pre>
   * Assuming both the modules are enabled and 'Module1' has higher priority,
   *  than the following statement:
   * <pre>
   * print_r(Main::moduleConfigArray('x'));
   * </pre>
   * Will print out:
   * <pre>
   * array (
   *   'a' => 'module1',
   *   'b' => 'module2',
   *   'c' => 'module2'
   * )
   * </pre>
   * Please note that unlike 'invokeMethodAll', this function does not accept 
   *  any argument other than $methodName. The controller methods are then 
   *  called without additional arguments.
   * @param string $methodName Method name
   * @return mixed Result
   */
  public static function moduleConfigArray($eventName) {
    $results = self::getVariable('module-config-' . $eventName, null);
    if (\is_null($results)) {
      $results = array();
      $v = self::invokeMethodAll($eventName);
      foreach ($v as $m) {
        if (!\is_array($m)) {
          // skipping all non-array values
          continue;
        }
        $results = $m + $results;
      }
      self::setVariable('module-config-' . $eventName, $results);
    }
    return $results;
  }
  
  /**
   * Returns a cached module configuration value.
   * Runs invokeMethod and cached its results.
   * Unlike 'invokeMethod', this function does not accept any argument other 
   *  than $methodName. The controller methods are then called without additional
   *  arguments.
   * @param string $methodName Method name
   * @return mixed Result
   */
  public static function moduleConfig($methodName) {
    $results = self::getVariable('module-config-' . $methodName, null);
    if (\is_null($results)) {
      $results = \end(self::invokeMethod($methodName));
      self::setVariable('module-config-' . $methodName, $results);
    }
    return $results;
  }
  
  /**
   * Returns a cached module configuration value.
   * Runs invokeMethodAll and cached its results.
   * Unlike 'invokeMethodAll', this function does not accept any argument other 
   *  than $methodName. The controller methods are then called without 
   *  additional arguments.
   * @param string $methodName Method name
   * @return mixed Result
   */
  public static function moduleConfigAll($methodName) {
    $results = self::getVariable('module-config-' . $methodName, null);
    if (\is_null($results)) {
      $results = self::invokeMethodAll($methodName); // last element of the array
      self::setVariable('module-config-' . $methodName, $results);
    }
    return $results;
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
   * Searches for module classes implementing the method and runs every method
   *  following the modules priority order.
   * This function takes an unlimited number of arguments.
   * Every argument (apart the method name) is passed to the module class
   *  method.
   * @param string $methodName Name of the module class method
   * @return Returns an array of non-null values returned by module classes
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
        if (!\is_null($x)) {
          $results[] = $x;
        }
      }
    }
    return $results;
  }
  
  /**
   * Invokes a controller method.
   * Searches for the highest priority module class which implements the method
   *  and calls it. If the method isn't implemented by any module class, it 
   *  returns null.
   * This function takes an unlimited number of arguments.
   * Every argument (apart the method name) is passed to the module class
   *  method.
   * @param string $methodName Name of the module class method
   * @return Returns the implementing method results.
   */
  public static function invokeMethod($methodName) {
    static $modules = null;
    static $methodBinding = array();
    
    if (\is_null($modules)) {
      $configuration = self::configuration();
      $modules = $configuration['modules'];
      \array_reverse($modules);
    }
    
    $args = array();
    if (\func_num_args() > 1) {
      $args = \func_get_args();
      \array_shift($args);
    }

    // Cache
    if (!\array_key_exists($methodBinding, $methodBinding)) {
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
   * Invokes a model method.
   * Searches for model classes implementing the method and runs every method
   *  following the modules priority order.
   * This function takes an unlimited number of arguments.
   * Every argument (apart the method name) is passed to the module class
   *  method.
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
   * Implements the singleton design pattern always returning the same instance.
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
   * Typically this is used for temporary application data.
   * @param string $path Path relative to the data folder
   * @return type
   */
  public static function tempPath($path = '') {
    return \config\settings()->BASE_DIR_ABS . 'temp/' . self::prepareUrl($path);
  }
  
  /**
   * Returns the absolute path to the data folder. 
   * Typically this is used for file upload and other application data.
   * @param string $path Path relative to the data folder
   * @return string Data path
   */
  public static function dataPath($path = '') {
    return \config\settings()->BASE_DIR_ABS . 'data/' . self::prepareUrl($path);
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
  
  /**
   * Get a variable. Variable are stored in the file system via method 
   *  setVariable
   * @param string $name Key
   * @param mixed $default Default value (returned in case the variable is not
   *  defined)
   * @return mixed Value
   */
  public static function getVariable($name, $default = null) {
    if (\file_exists("config/vars/" . $name . ".var")) {
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
   * Set a variable. Variables are stored in the file system and can be accessed
   *  via method getVariable
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
   * Returns a configuration variable.
   * @param string $name Name
   * @param mixed $default Default value (returned if it does not exist)
   * @return mixed Config value
   */
  public static function getCfg($name, $default = null) {
    try {
      return \config\Config::getInstance()->{$name};
    }
    catch (\system\exceptions\Error $ex) {
      return $default;
    }
  }
  
  /**
   * Returns the ciderbit session.
   * Examples: 
   * <code>
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
   * </code>
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
}
