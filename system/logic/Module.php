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
//				\system\Utils::set("system-modules", $modulesConf);
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
		$dynController = array();
		$dynModel = \system\yaml\Yaml::parse("system/model/tables.yml");
		
		$fp = \opendir("module");
		
		while (($moduleName = \readdir($fp))) {
			
			if (substr($moduleName,0,1) == '.') {
				continue;
			}
			
//			echo $moduleName . "\n";
			
			$moduleDir = self::getPath($moduleName);
			$moduleNs = self::getNamespace($moduleName);
			
			if (\is_dir($moduleDir)) {
				
				if (\file_exists($moduleDir . "module.yml")) {
					
					try {
						
						$moduleInfo = \system\yaml\Yaml::parse($moduleDir . "module.yml");
						$moduleInfo["events"] = array();
						
						$enabled = \array_key_exists("enabled", $moduleInfo) ? (bool)$moduleInfo["enabled"] : true;
						$priority = -\array_key_exists("weight", $moduleInfo) ? (int)$moduleInfo["weight"] : 0;
						$componentsNs = self::getNamespace($moduleName, \array_key_exists("componentsNs", $moduleInfo) ? (string)$moduleInfo["componentsNs"] : "components");
						$customEvents = \array_key_exists("customEvents", $moduleInfo) ? (array)$moduleInfo["customEvents"] : array();
						$components = \array_key_exists("components", $moduleInfo) ? (array)$moduleInfo["components"] : array();

						if ($enabled) {
							if (!\array_key_exists("class", $moduleInfo)) {
								throw new \system\InternalErrorException("Bad module config file: class is missing");
							}

							$moduleInfo["namespace"] = $moduleNs;
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
							$componentsInfo = array();
							foreach ($components as $component) {
								$componentClass = $componentsNs . @$component["class"];
								$componentsInfo[$componentClass] = array();
								foreach (@$component["pages"] as $page) {
									$action = (string)@$page["action"];
									$regexp = (string)@$page["url"];

									$rules = array(
										"@strid" => "[a-zA-Z0-9_-]+",
										"@urlarg" => "[^/]+",
									);

									foreach ($rules as $search => $replace) {
										$regexp = str_replace($search, $replace, $regexp);
									}

									$componentsInfo[$componentClass][$regexp] = array(
										"class" => $componentClass,
										"action" => $action
									);
								}
							}
							$moduleInfo["components"] = $componentsInfo;

							if (!\array_key_exists($priority, $dynController)) {
								$dynController[$priority] = array();
							}
							$dynController[$priority][$moduleName] = $moduleInfo;
							
							if (\file_exists($moduleDir . "tables.yml")) {
								$tables = \system\yaml\Yaml::parse($moduleDir . "tables.yml");
								
								foreach ($tables as $tableName => $table) {
									if (\array_key_exists($tableName, $dynModel)) {
										$dynModel[$tableName]["fields"] = array_merge($dynModel[$tableName]["fields"], $table["fields"]);
										$dynModel[$tableName]["keys"] = array_merge($dynModel[$tableName]["fields"], $table["fields"]);
										$dynModel[$tableName]["relations"] = array_merge($dynModel[$tableName]["fields"], $table["fields"]);
									}
									else {
										$dynModel[$tableName] = $table;
									}
								}
							}
						}
						
					} catch (\Exception $ex) {
						print_r($ex);
						continue;
					}
				}
			}
		}
		
		$configuration = array(
			"modules" => array(),
			"components" => array(),
			"events" => array(),
			"model" => $dynModel
		);

		\krsort($dynController);
		foreach ($dynController as $modules) {
			\ksort($modules);
			// now modulesInfo array contains all the modules events and components
			// and it's sorted by module priority and module name
			foreach ($modules as $name => $module) {
				foreach ($module["events"] as $eventName => $isImplemented) {
					if (!\array_key_exists($eventName, $configuration["events"])) {
						$configuration["events"][$eventName] = array();
					}
					if ($isImplemented) {
						$configuration["events"][$eventName][] = $module["class"];
					}
				}
				foreach ($module["components"] as $componentClass => $pages) {
					$configuration["components"] = array_merge($configuration["components"], $pages);
				}
				$configuration["modules"][$name] = $module["class"];
			}
		}
			
		\ksort($configuration["events"]);
		\ksort($configuration["components"]);
		\ksort($configuration["model"]);
		
		return $configuration;
	}
	
	public static function getPath($module, $subpath=null) {
		return "module/" . $module . "/" . (\is_null($subpath) ? "" : $subpath . "/");
	}
	
	public static function getNamespace($module, $subnamespace=null) {
		return '\module\\' . $module . '\\' . (is_null($subnamespace) ? '' : $subnamespace . '\\');
	}
	
	public static function checkAccess($url, $urlArgs) {
		$component = $this->getComponent($url);
		if (!\is_null($component)) {
			\system\logic\Component::access($component["class"], $component["action"], $urlArgs);
		}
	}
	
	public static function getTable($name) {
		$configuration = $this->getConfiguration();
		if (\array_key_exists($name, $configuration["model"])) {
			return $configuration["model"][$name];
		} else {
			throw new \system\InternalErrorException(\system\Lang::translate('Table <em>@name</em> not found.', array('@name' => $name)));
		}
	}
	
	public static function getTemplateManager() {
		$tpl = new \system\TemplateManager();
		$tpl->addTemplateDir(\system\Theme::getThemePath("templates"));
		
		$conf = self::getConfiguration();
		foreach ($conf["modules"] as $name => $class) {
			$tpl->addTemplateDir(self::getNamespace($name, "templates"));
		}
		return $tpl;
	}
	
	public static function getComponent($url) {
		static $urls = null;
		
		if ($url == \config\settings()->BASE_DIR) {
			$url = '';
		} else if (\substr($url, 0, \strlen(\config\settings()->BASE_DIR)) == \config\settings()->BASE_DIR) {
			$url = \substr($url, \strlen(\config\settings()->BASE_DIR));
		}
		
		if (!empty($url)) {
			$x = \strstr($url, '?', true);
			if ($x) {
				$url = $x;
			}
		}
		
		if (\is_null($urls)) {
			// Url cache
			$urls = \system\Utils::get("system-urls", array());
		}
		if (!\array_key_exists($url, $urls)) {
			$urls[$url] = null;
			$configuration = self::getConfiguration();
			foreach ($configuration["components"] as $regexp => $component) {
				if (\preg_match('@^' . $regexp . '$@', $url, $m)) {
					\array_shift($m);
					$urls[$url] = array(
						"class" => $component["class"],
						"action" => $component["action"],
						"args" => $m
					);
					break;
//					\system\Utils::set("system-urls", $urls);
				}
			}
		}
		return $urls[$url];
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
	
	public static function run($url, $request=null) {
		$component = self::getComponent($url);
		print_r($component);
		if (!$component) {
			$component = array(
				 "class" => Module::getNamespace('core', 'components') . "Page",
				 "action" => "Read",
				 "args" => array("home")
			);
		}
		$obj = new $component["class"]($component["action"], $url, $component["args"], $request);
		$obj->process();
		
		
//		print_r($component);
//		echo "\n";

//		$componentClass = self::getComponent($componentName);
//		if (\is_null($componentClass)) {
//			return false;
//		}
//		$component = new $componentClass($request, $prefix);
//		$component->process();
//		return true;
	}
}
?>