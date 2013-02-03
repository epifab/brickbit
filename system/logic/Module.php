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
			$modulesConf = $rebuild || !\config\settings()->CORE_CACHE ? null : \system\Utils::get("system-modules", null);
			if (\is_null($modulesConf)) {
				$modulesConf = self::initConfiguration();
				if (\config\settings()->CORE_CACHE) {
					\system\Utils::set("system-modules", $modulesConf);
				}
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
						
						if (!\is_array($moduleInfo)) {
							throw new \system\InternalErrorException(\system\Lang::translate('Unable to parse <em>@name</em> module configuration.', array('@name' => $moduleName)));
						}
						
						$moduleInfo["events"] = array();
						
						$enabled = \system\Utils::getParam("enabled", $moduleInfo, array('default' => true));
						$priority = -\system\Utils::getParam("weight", $moduleInfo, array('default' => 0));
						$componentsNs = self::getNamespace($moduleName, \system\Utils::getParam("componentNs", $moduleInfo, array('default' => 'components')));
						$customEvents = \system\Utils::getParam("customEvents", $moduleInfo, array('default' => array()));
						$components = \system\Utils::getParam("components", $moduleInfo, array('default' => array()));

						if ($enabled) {
							$moduleInfo["namespace"] = $moduleNs;
							$moduleInfo["class"] = $moduleInfo["namespace"] . \system\Utils::getParam('class', $moduleInfo, array('required' => true));

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
							foreach ($components as $componentName => $component) {
								$componentClass = $componentsNs . \system\Utils::getParam('class', $component, array('required'));
								$componentsInfo[$componentClass] = array();
								foreach (@$component["pages"] as $page) {
									$action = (string)@$page["action"];
									$regexp = (string)@$page["url"];

									$rules = array(
										"@strid" => "[a-zA-Z0-9\-_]+",
										"@urlarg" => "[a-zA-Z0-9\-_.]+",
									);

									foreach ($rules as $search => $replace) {
										$regexp = str_replace($search, $replace, $regexp);
									}

									$componentsInfo[$componentClass][$regexp] = array(
										"module" => $moduleName,
										"name" => $componentName,
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
										$dynModel[$tableName]["fields"] = \array_merge($dynModel[$tableName]["fields"], (@\is_array($table["fields"])? $table["fields"] : array()));
										$dynModel[$tableName]["keys"] = \array_merge($dynModel[$tableName]["keys"], (@\is_array($table["keys"])? $table["keys"] : array()));
										$dynModel[$tableName]["relations"] = \array_merge($dynModel[$tableName]["relations"], (@\is_array($table["relations"])? $table["relations"] : array()));
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
	
	public static function getActiveModules() {
		$conf = $this->getConfiguration();
		return $conf['modules'];
	}
	
	public static function getPath($module, $subpath=null) {
		return "module/" . $module . "/" . (\is_null($subpath) ? "" : $subpath . "/");
	}
	
	public static function getAbsPath($module, $subpath=null) {
		return \config\settings()->BASE_DIR . self::getPath($module, $subpath);
	}
	
	public static function getNamespace($module, $subnamespace=null) {
		return '\module\\' . $module . '\\' . (is_null($subnamespace) ? '' : $subnamespace . '\\');
	}
	
	public static function checkAccess($url, $request) {
		$component = self::getComponent($url);
		if (!\is_null($component)) {
			\system\logic\Component::access($component["class"], $component["action"], $component["urlArgs"], $request);
		}
	}
	
	public static function getTable($name) {
		$configuration = self::getConfiguration();
		if (\array_key_exists($name, $configuration["model"])) {
			return $configuration["model"][$name];
		} else {
			throw new \system\InternalErrorException(\system\Lang::translate('Table <em>@name</em> not found.', array('@name' => $name)));
		}
	}
	
	public static function getTemplateManager() {
		$tpl = new \system\TemplateManager();
		$themeTplPath = \system\Theme::getThemePath('templates');
		
		if (!\is_null($themeTplPath)) {
			$tpl->addTemplateDir($themeTplPath);
		}
		
		$conf = self::getConfiguration();
		foreach ($conf["modules"] as $name => $class) {
			$moduleTplPath = self::getPath($name, 'templates');
			if ($moduleTplPath) {
				$tpl->addTemplateDir($moduleTplPath);
			}
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
			if (\config\settings()->CORE_CACHE) {
				// Url cache
				$urls = \system\Utils::get("system-urls", array());
			} else {
				$urls = array();
			}
		}
		if (!\array_key_exists($url, $urls)) {
			$urls[$url] = null;
			$configuration = self::getConfiguration();
			foreach ($configuration["components"] as $regexp => $component) {
				if (\preg_match('@^' . $regexp . '$@', $url, $m)) {
					\array_shift($m);
					$urls[$url] = array(
						"name" => $component["name"],
						"module" => $component["module"],
						"class" => $component["class"],
						"action" => $component["action"],
						"urlArgs" => $m
					);
					if (\config\settings()->CORE_CACHE) {
						\system\Utils::set("system-urls", $urls);
					}
					break;
				}
			}
		}
		return $urls[$url];
	}
	
	public static function exists($module) {
		$configuration = self::getConfiguration();
		return \array_key_exists($module, $configuration["modules"]);
	}
	
	/**
	 * Raise an event.
	 * It takes a variable number of parameters 
	 * to pass to the event implementation defined on each active module.
	 * @param string $eventName Event name
	 * @return array Array consisting of the event implementations results
	 */
	public static function raise($eventName) {
		$configuration = self::getConfiguration();
		$result = array();
		if (\array_key_exists($eventName, $configuration["events"])) {
			foreach ($configuration["events"][$eventName] as $class) {
				$result[$class] = \func_num_args() == 1
					? \call_user_func(array($class, $eventName))
					: \call_user_func_array(array($class, $eventName), \array_shift(\func_get_args()));
			}
		}
		return $result;
	}
	
	/**
	 * Run the component associated with the url.
	 * @param string $url
	 * @param array $request
	 */
	public static function run($url, $request=null) {
		$component = self::getComponent($url);
		if (!$component) {
			$component = array(
				"name" => "page",
				"module" => "core",
				"class" => Module::getNamespace('core', 'components') . \system\Utils::get('not-found-class', 'Node'),
				"action" => \system\Utils::get('not-found-action', 'NotFound'),
				"urlArgs" => array($url)
			);
		}
		$obj = new $component["class"]($component["name"], $component["module"], $component["action"], $url, $component["urlArgs"], $request);
		
		// Raise event onRun
//		self::raise("onRun", $obj);
		
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