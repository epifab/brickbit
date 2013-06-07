<?php
namespace system\view;

class Form {
	private static $activeForm = null;
	
	public static function startForm($formId) {
		if (self::$activeForm) {
			throw new \system\error\InternalError('Illegal nested form.');
		}
		
		self::$activeForm = $formId;
		
		$_SESSION['system']['forms'][$formId] = array(
			'id' => $formId,
			'input' => array(),
			'errors' => array(),
		);
	}
	
	public static function closeForm() {
		self::$activeForm = null;
	}
	
	public static function addInput($widget, $name, $value, array $input = array(), $metaType = null) {
		if (self::$activeForm) {
			$form = &$_SESSION['system']['forms'][self::$activeForm];
			
			$input['name'] = $name;
			$input['value'] = $value;
			
			$return = (isset($form['input'][$input['name']]))
				? $form['input'][$input['name']]['value']
				: $value;
			
			$form['input'][$input['name']] = array(
				'name' => $name,
				'value' => $value,
				'widget' => $widget,
				'input' => $input,
				'metaType' => $metaType
			);
			
			return $return;
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
	
	private static function getInputPostedValue(array $input) {
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
			return \system\view\Widget::getWidget($input['widget'])->fetch($haystack, $input);
		} else {
			return null;
		}
	}
	
	private static function fetchInputValues(array &$form) {
		foreach ($form['input'] as $input) {
			$input = &$form['input'][$input['name']];
			
			$input['value'] = self::getInputPostedValue($input['name']);
			$input['error'] = null;
			
			$form['errors'][$input['name']] = null;

			$mt = $input['metaType'];
			if ($mt) {
				try {
					$mt->validate($input['value']);
				} catch (\system\error\InternalError $ex) {
					$form['errors'][$input['name']] = $ex->getMessage();
				}
			}
		}
	}
	
	public static function formSubmission() {
		$formId = self::getPostedFormId();
		if (!\is_null($formId)) {
			$form = &$_SESSION['system']['forms'][$formId];
			self::fetchInputValues($form);
			if (!empty($form['recordset'])) {
				$rsb = new \system\model\RecordsetBuilder($form['recordset']['masterTable']);
				$rsb->usingAll();
				if (!empty($form['recordset']['key'])) {
					$rs = $rsb->selectFirstBy($form['recordset']['key']);
					if (!$rs) {
						throw new \system\error\InternalError('The resource you tried to edit does no longer exists.');
					}
				} else {
					$rs = $rsb->newRecordset();
				}
				foreach ($form['recordset']['fields'] as $path => $name) {
					$f = $rsb->searchField($path, true);
					$f->validate($form['input'][$name]['value']);
					$rs->setProg($form['input'][$name]['value']);
				}
				return array('recordset' => $rs) + $form;
			}
			else {
				return $form;
			}
		} else {
			return null;
		}
	}
}