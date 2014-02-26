<?php
namespace system;

abstract class Module {
  public static function getPath($moduleName, $path = '') {
    $module = \system\Main::getModule($moduleName);
    return $module['path'] . '/' . $path;
  }
  
  public static function getAbsPath($moduleName, $path = '') {
    $module = \system\Main::getModule($moduleName);
    return \config\settings()->BASE_DIR . $module['path'] . '/' . $path;
  }
  
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
