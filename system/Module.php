<?php
namespace system;

abstract class Module {
  private static function getPathArr($moduleName, array $subpaths) {
    $module = \system\Main::getModule($moduleName);
    $path = $module['path'];
    foreach ($subpaths as $subpath) {
      $path .= $subpath . '/';
    }
    return $path;
  }
  
  public static function getPath($moduleName) {
    $args = func_get_args();
    unset($args[0]);
    return self::getPathArr($moduleName, $args);
  }
  
  public static function getAbsPath($module) {
    $args = func_get_args();
    unset($args[0]);
    return \config\settings()->BASE_DIR . self::getPathArr($module, $args);
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
