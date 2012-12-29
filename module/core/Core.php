<?php
namespace module\core;

class Core extends \system\logic\Module {
	
	public static function onRun(\system\logic\Component $component) {
		die("ciao");
	}
	
	public static function loadRSFormData(Recordset $recordset, &$errors, $formInfo) {
		
		$numErrors = 0;
		$builder = $recordset->getBuilder();

		if (\array_key_exists("recordset", $_REQUEST) && \is_array($_REQUEST["recordset"])) {
			
			foreach ($formInfo as $fieldInfo) {
				if (\is_array($fieldInfo)) {
					$path = Utils::getParam('path', $fieldInfo, array('required' => true));
				} else {
					$path = $fieldInfo;
				}
				
				$metaType = $builder->searchMetaType($path, true);
				$metaType instanceof MetaType;

				$value = Utils::getParam($path, $_REQUEST["recordset"], array('default' => null));
				
				try {
					if ($metaType instanceof MetaBoolean) {
						$value = \is_null($value) ? 0 : 1;
					}
					
					$recordset->setEdit($curPath, $value);
					
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