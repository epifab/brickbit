<?php
namespace system\view;

class Form {
	private static $activeForm = null;
	/**
	 * @var \system\model\RecordsetInterface
	 */
	private static $recordset = null;
	
	public static function startForm($formId, $recordset = null) {
		if (self::$activeForm) {
			throw new system\InternalErrorException('Illegal nested form.');
		}
		
		self::$activeForm = $formId;
		self::$recordset = $recordset;
		
		$_SESSION['system']['forms'][$formId] = array(
			'id' => $formId,
			'input' => array(),
			'recordset' => empty($recordset) ? null : array(
				'masterTable' => $recordset->getBuilder()->getTableName(),
				'fields' => array(),
				'key' => ($recordset->isStored() ? $recordset->getPrimaryKey() : null)
			),
			'errors' => array(),
		);
	}
	
	/**
	 * @return \system\model\RecordsetInterface
	 */
	public static function getRecordset() {
		return self::$recordset;
	}
	
	public static function closeForm() {
		self::$activeForm = null;
	}
	
	public static function addInput($name, $value, $widget, \system\metatypes\MetaType $metaType) {
		if (self::$activeForm) {
			$_SESSION['system']['forms'][self::$activeForm]['input'][$name] = array(
				'name' => $name,
				'value' => $value,
				'widget' => $widget,
				'metaType' => $metaType,
			);
		}
	}
	
	public static function addRecordsetField($name, $path, $widget) {
		if (self::$activeForm && self::$recordset) {
			self::addInput(
				$name, 
				self::$recordset->getProg($path),
				$widget,
				self::$recordset->getBuilder()->searchField($path, true)->getMetaType()
			);
			$_SESSION['system']['forms'][self::$activeForm]['recordset']['fields'][$path] = $name;
		}
	}
	
	public static function getActiveForm() {
		return self::$activeForm;
	}
	
	private static function getPostedFormId() {
		if (isset($_REQUEST['system']) && isset($_REQUEST['system']['formId'])) {
			return $_REQUEST['system']['formId'];
		}
		return null;
	}
	
	public static function checkFormSubmission() {
		return
			isset($_REQUEST['system'])
			&& isset($_REQUEST['system']['formId'])
			&& isset($_SESSION['system']['forms'])
			&& isset($_SESSION['system']['forms'][$_REQUEST['system']['formId']]);
	}
	
	private static function getInputPostedValue($input) {
		$haystack = $_REQUEST;
		
		$needles = \preg_split('/(\[|\])+/', $input['name'], 0, PREG_SPLIT_NO_EMPTY);
		if (count($needles)) {
			foreach ($needles as $needle) {
				if (\array_key_exists($needle, $haystack)) {
					$haystack = $haystack[$needle];
				} else {
					return null;
				}
			}
			return \system\Main::invokeMethod($input['widget'], $haystack);
		} else {
			return null;
		}
	}
	
	private static function fetchInputValues(array &$form) {
		foreach ($form['input'] as $input) {
			$form['input'][$input['name']]['value'] = self::getInputPostedValue($input['name']);
		}
	}
	
	public static function formSubmission() {
		$formId = self::getPostedFormId();
		if (!\is_null($formId)) {
			$form = &$_SESSION['system']['forms'][$formId];
			self::fetchInputValues($form);
			return $form;
		} else {
			return null;
		}
	}
}
?>