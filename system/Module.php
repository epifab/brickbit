<?php
namespace system;

use system\Main;
use system\Theme;
use system\yaml\Yaml;

class Module {
  /**
   * Generates the module configuration array.
   * <p>Parses each info.yml looking for active modules.</p>
   * @return array
   */
  public static function loadConfiguration() {
    
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
          throw new InternalError('Unable to parse <em>@name</em> module info.', array('@name' => $moduleName));
        }

        $enabled = \cb\array_item('enabled', $moduleInfo, array('default' => true));

        if (!$enabled) {
          // just ignore the module
          continue;
        }

        $moduleClass = $moduleNs . \cb\array_item('class', $moduleInfo, array('required' => true));

        if (!\class_exists($moduleClass)) {
          throw new InternalError('Class <em>@name</em> does not exist.', array('@name' => $moduleClass));
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
          throw new InternalError('Class <em>@name</em> does not exist.', array('@name' => $modelClass));
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
          throw new InternalError('Class <em>@name</em> does not exist.', array('@name' => $viewClass));
        }

        $templatesPath = \cb\array_item('templatesPath', $moduleInfo, array(
          'default' => null,
          'prefix' => $moduleDir
        ));
        if (!\is_null($templatesPath) && !\is_dir($templatesPath)) {
          throw new InternalError('Directory <em>@path</em> not found', array('@path' => $templatesPath));
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
            throw new InternalError('Class <em>@name</em> does not exist.', array('@name' => $componentClass));
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
          $info = Yaml::parse($infoPath);
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
      
      if (isset($table['class'])) {
        $TABLES[$tableName]['class'] = $table['class'];
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
          $model = Yaml::parse($module['path'] . "model.yml");
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

    $themeTplPath = Theme::getAbsPath('templates/');
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
   * Returns the path for a resource inside the module directory.
   * The path is relative to the project root directory.
   * @param string $moduleName Module name
   * @param string $path Path relative to the module directory
   * @return string Path
   */
  public static function getPath($moduleName, $path = '') {
    return Main::modulePath($moduleName, $path);
  }
  
  /**
   * Returns the absolute path for a given resource inside the module directory.
   * @param string $moduleName Module name
   * @param string $path Path relative to the module directory
   * @return string Path
   */
  public static function getAbsPath($moduleName, $path = '') {
    $module = Main::getModule($moduleName);
    return Main::getBaseDir() . $module['path'] . $path;
  }
  
  /**
   * Returns the module namespace.
   * Usage example:
   * <code>
   * // The following code will print: \module\core\controller\
   * echo \system\Module::getNamespace('core', 'controller');
   * </code>
   * @param type $moduleName
   * @return string
   */
  public static function getNamespace($moduleName) {
    $subpaths = func_get_args();
    unset($subpaths[0]);
    $module = Main::getModule($moduleName);
    $namespace = $module['ns'];
    foreach ($subpaths as $subpath) {
      $namespace .= $subpath . '\\';
    }
    return $namespace;
  }
}
