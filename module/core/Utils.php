<?php
namespace module\core;

define('LOG_ERROR', 1);
define('LOG_WARNING', 2);
define('LOG_INFO', 3);
define('LOG_DEBUG', 4);

class Utils {
	
	public static function getNodeTypes() {
		static $nodeTypes = null;
		if (\is_null($nodeTypes)) {
			$nodeTypes = array();
			$x = \system\Main::raiseEvent('nodeTypes');
			foreach ($x as $y) {
				if (\is_array($y)) {
					$nodeTypes = \array_merge_recursive($nodeTypes, $y);
				}
			}
		}
		return $nodeTypes;
	}
	
	public static function nodeTypeExists($type) {
		return $type != '#' && \in_array($type, self::getNodeTypes());
	}
	
	public static function loadRSFormData(\system\model\Recordset $recordset, &$errors, $formInfo) {
		
		$numErrors = 0;
		$builder = $recordset->getBuilder();

		if (\array_key_exists('recordset', $_REQUEST) && \is_array($_REQUEST['recordset'])) {
			
			foreach ($formInfo as $fieldInfo) {
				if (\is_array($fieldInfo)) {
					$path = Utils::getParam('path', $fieldInfo, array('required' => true));
				} else {
					$path = $fieldInfo;
				}

				$metaType = $builder->searchMetaType($path, true);
				$metaType instanceof MetaType;

				$value = \system\Utils::getParam($path, $_REQUEST['recordset'], array('default' => null));

				if ($metaType instanceof MetaBoolean) {
					$value = \is_null($value) ? 0 : 1;
				}

				try {

					list($rs, $name) = $recordset->searchParent($path, true);
					$rs->setEdit($name, $value);
					
				} catch (ValidationException $ex) {
					$errors[$path] = $ex->getMessage();
					$numErrors++;
				} catch (ConversionException $ex) {
					$errors[$path] = $ex->getMessage();
					$numErrors++;
				}
			}
			if ($numErrors == 0) {
				return true;
			}
		}
		return false;
	}
}
?>