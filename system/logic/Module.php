<?php
namespace system\logic;

abstract class Module {
	public static function install() {
		
	}
	public static function uninstall() {
		
	}
	
	public static function getPath($module, $subpath=null) {
		return "module/" . $module . "/" . (\is_null($subpath) ? "" : $subpath . "/");
	}
	
	public static function getNamespace($module, $subnamespace=null) {
		return "\\module\\" . $module . "\\" . (is_null($subnamespace) ? "" : $subnamespace . "\\");
	}
	
	public static function getModules() {
		static $modules = null;
		if (\is_null($modules)) {
			$modules = array();
			$fp = \opendir("module/");
			while (($namespace = \readdir($fp))) {
				if (substr($namespace,0,1) == '.') {
					continue;
				}
				$path = self::getPath($namespace);
				
				if (is_dir($path)) {
					$fp2 = \opendir(self::getPath($namespace));
					while (($fileName = \readdir($fp2))) {
						$ext = \system\File::getExtension($fileName, true);
						$module = \system\File::stripExtension($fileName);
						if ($ext == "php" && \strtolower($module) == \strtolower($namespace)) {
							$modules[] = array(
								"namespace" => self::getNamespace($namespace),
								"class" => $module
							);
						}
					}
				}
			}
		}
		return $modules;
	}
	
	public static function getModulesByComponent($component) {
		static $modules = array();
//		static $count = 0;
//		if ($count > 0) {
//			return null;
//		}
//		$count++;
		if (!\array_key_exists($component, $modules)) {
			$allModules = self::getModules();
			$modules[$component] = array();
			foreach ($allModules as $module) {
				if (\class_exists($module["namespace"] . "controller\\" . $component)) {
					$modules[$component][] = $module;
				}
			}
		}
		return $modules[$component];
	}
	
	public static function getComponent($component) {
		$modules = self::getModulesByComponent($component);
		if (empty($modules)) {
			return null;
		} else {
			return array(
				"namespace" => $modules[0]["namespace"] . "controller\\",
				"class" => $component
			);
		}
	}
	
	public static function getModulesByEvent($event) {
		static $modules = array();
		if (!\array_key_exists($event, $modules)) {
			$modules[$event] = array();
			foreach (self::getModules() as $module) {
				if (\is_callable(array($module["namespace"] . $module["class"], $event))) {
					$modules[$event][] = $module;
				}
			}
		}
		return $modules[$event];
	}
	
	public static function exists($module) {
		self::getModules();
		foreach (self::getModules() as $m) {
			if ($m["class"] == $module) {
				return true;
			}
		}
	}
	
	public static function raise($name, $args) {
		if (substr($name, 0, 2) == "on") {
			$modules = self::getModulesByEvent($name);
			foreach ($modules as $module) {
				\call_user_func(array($module["namespace"] . $module["class"], $name), $args);
			}
		}
	}
	
	public static function run($name, $request=null, $prefix="") {
		$component = self::getComponent($name);
//		\call_user_func(array($component["namespace"] . $component["class"], "process"),$_REQUEST);
//		$modules = self::getModulesByComponent($name);
		if (!empty($component)) {
			$x = $component["namespace"] . $component["class"];
			$o = new $x($request, $prefix);
			$o->process($name);
		}
	}
}
?>