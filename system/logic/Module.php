<?php
namespace system\logic;

abstract class Module {
	private static $events = array(
		"onSessionInit",
		"onLogin",
		"onLogout",
		"onPageLoad",
		"onComponentLoad",
		"chron",
		"onCreate",
		"onRead",
		"onUpdate",
		"onDelete"
	);
	
//	public function install() {
//		
//	}
//	public function uninstall() {
//		
//	}
	
	/**
	 * Get the modules configuration array
	 * @param type $rebuild If true the configuration will be generated even when it already exists
	 */
	private static function getConfiguration($rebuild=false) {
		static $modulesConf = null;
		if (\is_null($modulesConf)) {
			$modulesConf = $rebuild ? null : \system\Utils::get("system-modules", null);
			if (\is_null($modulesConf)) {
				$modulesConf = self::initConfiguration();
				\system\Utils::set("system-modules", $modulesConf);
			}
		}
		return $modulesConf;
	}
	
	/**
	 * Initialize the modules configuration array
	 * @return array
	 * @throws \system\InternalErrorException
	 */
	private static function initConfiguration() {
		$modulesInfo = array();
		
		$fp = \opendir("module");
		
		while (($moduleName = \readdir($fp))) {
			
			if (substr($moduleName,0,1) == '.') {
				continue;
			}
			
			echo $moduleName . "\n";
			
			$moduleDir = self::getPath($moduleName);
			
			if (is_dir($moduleDir)) {
				
				if (\file_exists($moduleDir . "module.yml")) {
					try {
						$moduleInfo = \system\yaml\Yaml::parse($moduleDir . "module.yml");
						
						$moduleInfo["components"] = array();
						$moduleInfo["events"] = array();
						
						$enabled = \array_key_exists("enabled", $moduleInfo) ? (bool)$moduleInfo["enabled"] : true;
						$priority = \array_key_exists("priority", $moduleInfo) ? (int)$moduleInfo["priority"] : 0;
						$componentsNs = \array_key_exists("componentsNs", $moduleInfo) ? (string)$moduleInfo["componentsNs"] : "component";
						$customEvents = \array_key_exists("customEvents", $moduleInfo) ? (array)$moduleInfo["customEvents"] : array();
						
						if (!$enabled) {
							continue;
						}
						
						if (!\array_key_exists("class", $moduleInfo)) {
							throw new \system\InternalErrorException("Bad module config file: class is missing");
						}
						
						$moduleInfo["namespace"] = self::getNamespace($moduleName);
						$moduleInfo["class"] = $moduleInfo["namespace"] . $moduleInfo["class"];
						
						if (!\class_exists($moduleInfo["class"], true)) {
							throw new \system\InternalErrorException("Module class not found");
						}
						
						foreach (self::$events as $eventName) {
							$moduleInfo["events"][$eventName] = \method_exists($moduleInfo["class"], $eventName);
						}
						foreach ($customEvents as $eventName) {
							$moduleInfo["events"][$eventName] = \method_exists($moduleInfo["class"], $eventName);
						}
						
						// Opening the components folder
						$dp = \opendir(self::getPath($moduleName, $componentsNs));
						
						while ($fileName = \readdir($dp)) {
							// Scanning all the php files inside the components folder
							if (\system\File::getExtension($fileName) == "php") {
								$componentName = \system\File::stripExtension($fileName);
								$componentClass = self::getNamespace($moduleName, $componentsNs) . $componentName;
								// Making sure the file is a class
								if (\class_exists($componentClass, true)) {
									$moduleInfo["components"][$componentName] = $componentClass;
								}
							}
						}
						
						$modulesInfo[$priority][$moduleName] = $moduleInfo;
						
					} catch (\Exception $ex) {
						continue;
					}
				}
			}
		}
		
			
		$configuration = array(
			"components" => array(),
			"events" => array()
		);

		\krsort($modulesInfo);
		foreach ($modulesInfo as $modules) {
			\ksort($modules);
			// now modulesInfo array contains all the modules events and components
			// and it's sorted by module priority and module name
			foreach ($modules as $module) {
				foreach ($module["events"] as $eventName => $isImplemented) {
					if (!\array_key_exists($eventName, $configuration["events"])) {
						$configuration["events"][$eventName] = array();
					}
					if ($isImplemented) {
						$configuration["events"][$eventName][] = $module["class"];
					}
				}
				foreach ($module["components"] as $componentName => $componentClass) {
					if (!\array_key_exists($componentName, $configuration["components"])) {
						$configuration["components"][$componentName] = $componentClass;
					}
				}
			}
		}
			
		\ksort($configuration["events"]);
		\ksort($configuration["components"]);
			
		return $configuration;
	}
	
	public static function getPath($module, $subpath=null) {
		return "module/" . $module . "/" . (\is_null($subpath) ? "" : $subpath . "/");
	}
	
	public static function getNamespace($module, $subnamespace=null) {
		return '\module\\' . $module . '\\' . (is_null($subnamespace) ? '' : $subnamespace . '\\');
	}
	
	public static function getComponent($componentName) {
		$configuration = self::getConfiguration();
		if (\array_key_exists($componentName, $configuration["components"])) {
			return $configuration["components"][$componentName];
		} else {
			return null;
		}
	}
	
	public static function exists($module) {
		$configuration = self::getConfiguration();
		return \array_key_exists($module, $configuration["modules"]);
	}
	
	public static function raise($eventName) {
		$configuration = self::getConfiguration();
		if (\array_key_exists($eventName, $configuration["events"])) {
			foreach ($configuration["events"][$eventName] as $class) {
				\func_num_args() == 1
					? \call_user_func(array($class, $eventName))
					: \call_user_func_array(array($class, $eventName), \array_shift(\func_get_args()));
			}
		}
	}
	
	public static function run($componentName, $request=null, $prefix="") {
		$componentClass = self::getComponent($componentName);
		if (\is_null($componentClass)) {
			return false;
		}
		$component = new $componentClass($request, $prefix);
		$component->process();
		return true;
	}
}
?>