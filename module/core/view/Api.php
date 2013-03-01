<?php

namespace module\core\view;

class Api {
	public static function panel_form_id() {
		return \system\view\Panels::getInstance()->getFormId();
	}
	
	public static function panel_form_name() {
		return \system\view\Panels::getInstance()->getFormName();
	}
	
	public static function edit_form_id() {
		$vars = \system\view\Template::current()->getVars();
		return 'system-edit-form-' . $vars['system']['component']['requestId'];
	}

	public static function generate_input_id($path) {
		static $ids = array();
		if (\array_key_exists($path, $ids)) {
			$ids[$path]++;
			return 'de-input-' . \str_replace('.', '-', $path) . '-' . $ids[$path];
		} else {
			$ids[$path] = 0;
			return 'de-input-' . \str_replace('.', '-', $path);
		}
	}

	public static function input_name($path) {
		return 'recordset[' . $path . ']';
	}

	//////////////////////
	// BLOCKS
	//////////////////////
	public static function de_form($content) {
		$vars = \system\view\Template::current()->getVars();

		$formId = 'system-edit-form-' . $vars['system']['component']['requestId'];

		$form = "\n"
				  . '<form id="' . $formId . '" name="' . $formId . '" method="post" enctype="multipart/form-data" action="' . $vars['system']['component']['url'] . '">'
				  // forzo le risposte ad avere gli stessi ID per il form e per i contenuti
				  . '<input type="hidden" name="system[requestId]" value="' . $vars['system']['component']['requestId'] . '"/>';

		return $form . $content . '</form>';
	}
	
	public static function de_row(\system\model\Recordset $recordset, $path, $options) {
		
	}
	
	public static function de_error($path) {
		$vars = \system\view\Template::current()->getVars();
		if (\array_key_exists('errors', $vars) && \array_key_exists($path, $vars['errors'])) {
			return '<div class="de-error">' . $vars['errors'][$path] . '</div>';
		}
	}
	
	public static function hidden(\system\model\Recordset $recordset, $path) {
		return '<input'
			. ' type="hidden"'
			. ' id="' . self::generate_input_id($path) . '"'
			. ' name="' . self::input_name($path) . '"'
			. ' class="de-input' . \system\Utils::getParam('class', $params, array('defalt' => '', 'prefix' => ' ')) . '"'
			. ' value="' . \htmlentities($recordset->getProg($path)) . '"/>';
	}

	public static function textbox(\system\model\Recordset $recordset, $path, $params = array()) {
		$mt = $recordset->getBuilder()->searchMetaType($path, true);

		$params += $mt->getAttributes();

		return 
			'<input type="' . (\system\Utils::getParam('password', $params, array('default' => false)) ? 'password' : 'text') . '"'
			. ' name="' . self::input_name($path) . '"'
			. ' id="' . self::generate_input_id($path) . '"'
			. \system\Utils::getParam('size', $params, array('prefix' => ' size="', 'suffix' => '"', 'default' => ''))
			. \system\Utils::getParam('maxlength', $params, array('prefix' => ' maxlength="', 'suffix' => '"', 'default' => ''))
			. \system\Utils::getParam('placeholder', $params, array('prefix' => ' placeholder="', 'suffix' => '"', 'default' => ''))
			. ' class="de-input' . \system\Utils::getParam('class', $params, array('defalt' => '', 'prefix' => ' ')) . '"'
			. ' value="' . \htmlentities($recordset->getProg($path)) . '"/>';
	}
	
	public static function textarea(\system\model\Recordset $recordset, $path, $params = array()) {
		$mt = $recordset->getBuilder()->searchMetaType($path, true);
		
		$params += $mt->getAttributes();
		
		return
			'<textarea'
			. ' class="de-input' . \system\Utils::getParam('class', $params, array('defalt' => '', 'prefix' => ' ')) . '"'
			. ' id="' . self::generate_input_id($path) . '"'
			. ' name="' . self::input_name($path) . '"'
			. ' rows="' . \system\Utils::getParam('rows', $params, array('default' => 5)) . '"'
			. ' cols="' . \system\Utils::getParam('cols', $params, array('default' => 35)) . '"'
			. '>' . \htmlentities($recordset->getProg($path)) . '</textarea>';
	}

	public static function selectbox(\system\model\Recordset $recordset, $path, $params = array()) {
		$mt = $recordset->getBuilder()->searchMetaType($path, true);

		$params += $mt->getAttributes();

		$return = 
			'<select'
			. ' name="' . self::input_name($path) . '"'
			. ' id="' . self::generate_input_id($path) . '"'
			. (\system\Utils::getParam('multiple', $params, array('default' => false)) ? ' multiple="multiple"' : '')
			. ' size="' . (\system\Utils::getParam('multiple', $params, array('default' => false)) ? '5' : '1') . '"'
			. ' class="de-input' . \system\Utils::getParam('class', $params, array('defalt' => '', 'prefix' => ' ')) . '">';

		$values = $recordset->getProg($path);
		if (!\is_array($values)) {
			if (\is_null($values)) {
				$values = array();
			} else {
				$values = array($values);
			}
		}

		$options = \system\Utils::getParam('options', $params, array('default' => array()));
		
		if (\is_array($options)) {
			foreach ($options as $key => $val) {
				$return .= 
					'<option value="' . \htmlentities($key) . '"'
					. ($key == $recordset->getProg($path) ? ' selected="selected"' : '') . '>'
					. \system\Lang::translate($val) . '</option>';
			}
		}
		$return .= '</select>';
		return $return;
	}

	public static function radiobuttons(\system\model\Recordset $recordset, $path, $params = array()) {
		$mt = $recordset->getBuilder()->searchMetaType($path, true);

		$params += $mt->getAttributes();

		$options = \system\Utils::getParam('options', $params, array('default' => array()));
		
		$id = self::generate_input_id($path);

		$return = '';
		if (\is_array($options)) {
			foreach ($options as $key => $val) {
				$return .=
					'<div class="de-radio">'
					. '<input'
					. ' type="radio"'
					. ' name="' . self::input_name($path) . '"'
					. ' id="' . $id . '-' . \htmlentities($key) . '"'
					. ' class="de-input' . \system\Utils::getParam('class', $params, array('defalt' => '', 'prefix' => ' ')) . '"'
					. ($recordset->getProg($path) == $key ? ' checked="checked"' : '')
					. ' value="' . \htmlentities($key) . '"/>'
					. '<label for="' . $id . '-' . \htmlentities($key) . '">' . \system\Lang::translate($val) . '</label>'
					. '</div>';
			}
		}
		return $return;
	}
	
	public static function checkboxes(\system\model\Recordset $recordset, $path, $params = array()) {
		$mt = $recordset->getBuilder()->searchMetaType($path, true);

		$params += $mt->getAttributes();

		$options = \system\Utils::getParam('options', $params, array('default' => array()));
		
		$id = self::generate_input_id($path);
		
		$values = $recordset->getProg($path);
		if (!\is_array($values)) {
			if (\is_null($values)) {
				$values = array();
			} else {
				$values = array($values);
			}
		}

		$return = '';
		if (\is_array($options)) {
			foreach ($options as $key => $val) {
				$return .=
					'<div class="de-checkbox">'
					. '<input'
					. ' type="checkbox"'
					. ' name="' . self::input_name($path) . '[' . \htmleentities($key) . ']"'
					. ' id="' . $id . '-' . \htmlentities($key) . '"'
					. ' class="de-input' . \system\Utils::getParam('class', $params, array('defalt' => '', 'prefix' => ' ')) . '"'
					. (\in_array($key, $values) ? ' checked="checked"' : '')
					. ' value="1"/>'
					. '<label for="' . $id . '-' . \htmlentities($key) . '">' . \system\Lang::translate($val) . '</label>'
					. '</div>';
			}
		}
		return $return;
	}
	
	public static function checkbox(\system\model\Recordset $recordset, $path, $params = array()) {
		$mt = $recordset->getBuilder()->searchMetaType($path, true);

		$params += $mt->getAttributes();

		return
			'<input'
			. ' type="checkbox"'
			. ' name="' . self::input_name($path) . ']"'
			. ' id="' . self::generate_input_id($path) . '"'
			. ($recordset->getProg($path) ? ' checked="checked"' : '')
			. ' class="de-input' . \system\Utils::getParam('class', $params, array('defalt' => '', 'prefix' => ' ')) . '"'
			. ' value="1"/>';
	}
	
	public static function submit_control($label=null) {
		if (\is_null($label)) {
			$label = \t('Save');
		}
		$vars = \system\view\Template::current()->getVars();
		if ($vars['system']['component']['requestType'] != 'MAIN') {
			return '<div class="de-controls"><input class="de-control" type="submit" value="' . $label . '"/></div>';
		}
	}

	public static function link($content, $params) {
		$params['url'] = \system\Utils::getParam('url', $params, array('required' => true, 'prefix' => \config\settings()->BASE_DIR));

		$url = $params['url'];
		$ajax = \system\Utils::getParam('ajax', $params, array('default' => true, 'options' => array(false, true)));
		$class = \system\Utils::getParam('class', $params, array('default' => 'link'));
		$params['system'] = array(
			 'requestType' => 'MAIN',
//			'requestId' => null
		);
		$jsArgs = \system\Utils::php2Js($params); //array_merge(array('url' => $url), \system\Utils::getParam('args', $params, array('default' => array()))));

		if ($ajax) {
			$confirm = \system\Utils::getParam('confirm', $params, array('default' => false, 'options' => array(false, true)));
			if ($confirm) {
				$confirmTitle = str_replace("'", "\\'", \system\Utils::getParam('confirmTitle', $params, array('default' => '')));
				$confirmQuest = str_replace("'", "\\'", \system\Utils::getParam('confirmQuest', $params, array('default' => '')));
				$action = "ciderbit.confirm('" . $confirmTitle . "', '" . $confirmQuest . "', " . $jsArgs . "); return false;";
			} else {
				$action = "ciderbit.request(" . $jsArgs . "); return false;";
			}
		}
		return '<a href="' . $url . '"'
				  . (empty($class) ? '' : ' class="' . $class . '"')
				  . (empty($action) ? '' : ' onclick="' . $action . '"') . '>'
				  . $content
				  . '</a>';
	}

	public static function panelStart($params) {
		system\view\Panels::getInstance()->openPanel();
	}

	public static function panel($content, $params) {
		list(, $formName, $output) = \system\view\Panels::getForm();

		$panelName = \system\Utils::getParam('name', $params, array('required' => true));
		$panelClass =
				  'system-panel system-panel-' . $panelName . ' ' . $formName
				  . \system\Utils::getParam('class', $params, array('prefix' => ' ', 'default' => ''));
		$vars = \system\view\Template::current()->getVars();

		$panelId = 'system-panel-' . $vars['system']['component']['requestId'] . '-' . $panelName;

		$content = '<div id="' . $panelId . '" class="' . $panelClass . '">' . $content . '</div>';

		\system\view\Panels::getInstance()->closePanel($panelId, $panelName, $panelClass, $content);

		return $output . $content;
	}

	public static function panels($content, $params) {
		$content = '';

		$panels = \system\view\Panels::getInstance()->getPanels();
		foreach ($panels as $panel) {
			$content .= $panel;
		}
		return $content;
	}

	public static function access($url, $args = array()) {
		return \system\Main::checkAccess($url, $args);
	}

	//////////////////////
	// 
	//////////////////////
}

?>