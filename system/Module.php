<?php
namespace system;

abstract class Module {
  /**
   * Returns the path for a resource inside the module directory.
   * The path is relative to the project root directory.
   * @param string $moduleName Module name
   * @param string $path Path relative to the module directory
   * @return string Path
   */
  public static function getPath($moduleName, $path = '') {
    $module = \system\Main::getModule($moduleName);
    return $module['path'] . '/' . $path;
  }
  
  /**
   * Returns the absolute path for a given resource inside the module directory.
   * @param string $moduleName Module name
   * @param string $path Path relative to the module directory
   * @return string Path
   */
  public static function getAbsPath($moduleName, $path = '') {
    $module = \system\Main::getModule($moduleName);
    return \config\settings()->BASE_DIR . $module['path'] . $path;
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
    $module = \system\Main::getModule($moduleName);
    $namespace = $module['ns'];
    foreach ($subpaths as $subpath) {
      $namespace .= $subpath . '\\';
    }
    return $namespace;
  }
}
