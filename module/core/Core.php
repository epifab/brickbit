<?php
namespace module\core;

class Core extends \system\logic\Module {
	
	
	public static function cron() {

	}
	public static function onRun(\system\logic\Component $component) {

	}
	
	public static function loadRSFormData(\system\model\Recordset $recordset, &$errors, $formInfo) {
		
		$numErrors = 0;
		$builder = $recordset->getBuilder();

		if (\array_key_exists("node", $_REQUEST) && \is_array($_REQUEST["node"])) {
			
			foreach ($formInfo as $fieldInfo) {
				if (\is_array($fieldInfo)) {
					$path = Utils::getParam('path', $fieldInfo, array('required' => true));
				} else {
					$path = $fieldInfo;
				}

				$metaType = $builder->searchMetaType($path, true);
				$metaType instanceof MetaType;

				$value = \system\Utils::getParam($path, $_REQUEST["node"], array('default' => null));

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