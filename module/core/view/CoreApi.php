<?php
namespace module\core\view;

use system\view\Api as Api;
use system\view\Form as Form;

class CoreApi {

	public function javascript($code) {
		$this->javascript .= "\n" . $code;
	}

	public function jss() {
		$jss = "";
		foreach ($this->javascript as $js) {
			$jss .= $js . "\n";
		}
		return $jss;
	}

	
	public static function edit_form_id() {
		$vars = \system\view\Template::current()->getVars();
		return 'system-edit-form-' . $vars['system']['component']['requestId'];
	}

	public static function generate_input_id($path) {
		static $ids = array();
		if (\array_key_exists($path, $ids)) {
			return 'de-input-' . \str_replace('.', '-', $path) . '-' . $ids[$path];
			$ids[$path]++;
		} else {
			return 'de-input-' . \str_replace('.', '-', $path);
			$ids[$path] = 1;
		}
	}

	public static function generate_input_name($path) {
		return 'recordset[' . $path . ']';
	}

	//////////////////////
	// BLOCKS
	//////////////////////
	public static function block_form($content, $params, $open) {
		$vars = \system\view\Template::current()->getVars();
		
		$recordset = \cb\array_item('recordset', $params);
		$formId = \cb\array_item('id', $params, array('required' => true));

		$id = $formId . '-' . $vars['system']['component']['requestId'];

		if ($open) {
			\system\view\Form::startForm($formId, $recordset);
		} 
		else {
			\system\view\Form::closeForm();

			$form = "\n"
				. '<form id="' . $id . '" name="' . $formId . '" method="post" enctype="multipart/form-data" action="' . $vars['system']['component']['url'] . '">'
				// forzo le risposte ad avere gli stessi ID per il form e per i contenuti
				. '<input type="hidden" name="system[formId]" value="' . $formId . '"/>'
				. '<input type="hidden" name="system[requestId]" value="' . $vars['system']['component']['requestId'] . '"/>';
			
			return $form . $content . '</form>';
		}
	}
	
	public static function de_error($path) {
		$vars = \system\view\Template::current()->getVars();
		if (\array_key_exists('errors', $vars) && \array_key_exists($path, $vars['errors'])) {
			return '<div class="de-error">' . $vars['errors'][$path] . '</div>';
		}
	}
	
	public static function input($params) {
		return '<input' . \cb\xml_arguments($params, array('type', 'name')) . ' />';
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	public static function hidden($params = array()) {
		Form::addControl($params, $widget);
	}
	
	
	public static function de_hidden($path, $params = array()) {
		$recordset = \system\view\Form::getRecordset();
		
		$input = array(
			'type' => 'hidden',
			'id' => Api::generate_input_id($path),
			'name' => Api::generate_input_name($path),
			'class' => 'de-input' . \cb\array_item('class', $params, array('default' => '', 'prefix' => ' ')),
			'value' => $recordset->getProg($path)
		);
		
		\system\view\Form::addRecordsetField($input['name'], $path, 'hidden', $params);
		
		return Api::input($input);
	}

	public static function de_textbox($path, $params = array()) {
		$formId = \system\view\Form::getActiveForm();
		if ($formId) {
			$recordset = \system\view\Form::getRecordset();
			
			$f = $recordset->getBuilder()->searchField($path, true);

			$params += $f->getAttributes();

			$input = array(
				'type' => (\cb\array_item('password', $params, array('default' => false)) ? 'password' : 'text'),
				'name' => $formId . '[' . $path . ']',
				'id' => \system\view\Api::generate_input_id($path),
				'class' => 'de-input' . \cb\array_item('class', $params, array('defalt' => '', 'prefix' => ' ')),
				'value' => $recordset->getProg($path)
			);
			
			$allowed = array('size', 'maxlength', 'placeholder');
			foreach ($allowed as $k) {
				if (\array_key_exists($k, $params)) {
					$input[$k] = $params[$k];
				}
			}
			
			\system\view\Form::addRecordsetField($input['name'], $path, 'textbox', $attributes);

			return \system\view\Api::input($input);
		}
	}
	
	public static function de_textarea($path, $params = array()) {
		$formId = \system\view\Form::getActiveForm();
		if ($formId) {
			$recordset = \system\view\Form::getRecordset();
			\system\view\Form::addInput($path, __FUNCTION__);

			$f = $recordset->getBuilder()->searchField($path, true);

			$params += $f->getAttributes();

			return
				'<textarea'
				. ' class="de-input' . \cb\array_item('class', $params, array('defalt' => '', 'prefix' => ' ')) . '"'
				. ' id="' . \system\view\Api::generate_input_id($path) . '"'
				. ' name="' . $formId . '[' . $path . ']"'
				. ' rows="' . \cb\array_item('rows', $params, array('default' => 5)) . '"'
				. ' cols="' . \cb\array_item('cols', $params, array('default' => 35)) . '"'
				. '>' . \htmlentities($recordset->getProg($path)) . '</textarea>';
		}
	}

	public static function de_selectbox($path, $params = array()) {
		$formId = \system\view\Form::getActiveForm();
		if ($formId) {
			$recordset = \system\view\Form::getRecordset();
			\system\view\Form::addInput($path, __FUNCTION__);

			$f = $recordset->getBuilder()->searchField($path, true);

			$params += $f->getAttributes();

			$return = 
				'<select'
				. ' name="' . $formId . '[' . $path . ']"'
				. ' id="' . \system\view\Api::generate_input_id($path) . '"'
				. (\cb\array_item('multiple', $params, array('default' => false)) ? ' multiple="multiple"' : '')
				. ' size="' . (\cb\array_item('multiple', $params, array('default' => false)) ? '5' : '1') . '"'
				. ' class="de-input' . \cb\array_item('class', $params, array('defalt' => '', 'prefix' => ' ')) . '">';

			$values = $recordset->getProg($path);
			if (!\is_array($values)) {
				if (\is_null($values)) {
					$values = array();
				} else {
					$values = array($values);
				}
			}

			$options = \cb\array_item('options', $params, array('default' => array()));

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
	}
	
	public static function radiobutton($args) {
		return \system\Api::input(array('type' => 'radio') + $args);
	}

	public static function de_radiobuttons($path, $params = array()) {
		$formId = \system\view\Form::getActiveForm();
		if ($formId) {
			$recordset = \system\view\Form::getRecordset();
			\system\view\Form::addInput($path, __FUNCTION__);

			$f = $recordset->getBuilder()->searchField($path, true);

			$params += $f->getAttributes();

			$options = \cb\array_item('options', $params, array('default' => array()));

			$id = \system\view\Api::generate_input_id($path);

			$return = '';
			if (\is_array($options)) {
				foreach ($options as $key => $val) {
					$return .=
						'<div class="de-radio">'
						. \system\Api::radiobutton(array(
							'name' => $formId . '[' . $path . '][' . \cb\text_plain($key) . ']',
							'id' => $id . '-' . \cb\text_plain($key),
							'class' => 'de-input' . \cb\array_item('class', $params, array('defalt' => '', 'prefix' => ' ')),
							'checked' => $recordset->getProg($path) == $key,
							'value' => \htmlentities($key)
						))
						. '<label for="' . $id . '-' . \cb\text_plain($key) . '">' . \cb\t($val) . '</label>'
						. '</div>';
				}
			}
			return $return;
		}
	}
	
	public static function checkboxes($name, $params) {
		$formId = \system\view\Form::getActiveForm();
		if ($formId) {

			$return = '';
		}
		
		return $return;
	}
	
	public static function de_checkboxes($path, $params = array()) {
		$formId = \system\view\Form::getActiveForm();
		if ($formId) {
			$recordset = \system\view\Form::getRecordset();
			\system\view\Form::addInput($path, 'checkboxes', $params);

			$f = $recordset->getBuilder()->searchField($path, true);

			// Extend params with the meta type attributes
			$params += $f->getAttributes();

			$options = \cb\array_item('options', $params, array('required' => true, 'type' => 'array'));

			$id = \system\view\Api::generate_input_id($path);

			$values = $recordset->getProg($path);
			if (!\is_array($values)) {
				if (\is_null($values)) {
					$values = array();
				} else {
					$values = array($values);
				}
			}

			$return = '';
			$return .= '<div class="de-checkboxes">';
			
			foreach ($options as $key => $val) {
				$input = array(
					'type' => 'checkbox',
					'name' => $name . '[' . \cb\text_plain($key) . ']',
					'id' => $id . '-' . \cb\text_plain($key),
					'value' => 1,
					'class' => 'de-input' . \cb\array_item('class', $params, array('defalt' => '', 'prefix' => ' ')),
				);
				if (\in_array($key, $values)) {
					$input['checked'] = 'checked';
				}
			
				$return .=
					'<div class="de-checkbox">'
					. \system\Api::input($input)
					. '<label for="' . $id . '-' . \htmlentities($key) . '">' . \system\Lang::translate($val) . '</label>'
					. '</div>';
			}
			$return .= '</div>';
			return $return;
		}
	}
	
	public static function de_checkbox($path, $params = array()) {
		$formId = \system\view\Form::getActiveForm();
		if ($formId) {
			$recordset = \system\view\Form::getRecordset();
			
			$input = array(
				'type' => 'checkbox',
				'name' => $formId . '[' . $path . ']',
				'id' => \system\view\Api::generate_input_id($path),
				'class' => 'de-input' . \cb\array_item('class', $params, array('defalt' => '', 'prefix' => ' ')),
				'value' => 1
			);
			if ($recordset->getProg($path)) {
				$input['checked'] = 'checked';
			}

			\system\view\Form::addRecordsetField($input['name'], $path, 'checkbox');

			$f = $recordset->getBuilder()->searchField($path, true);

			$params += $f->getAttributes();
			return \system\view\Api::input($input);
		}
	}
	
	public static function submit_control($label=null) {
		if (\is_null($label)) {
			$label = \cb\t('Save');
		}
		$vars = \system\view\Template::current()->getVars();
		if ($vars['system']['component']['requestType'] != 'MAIN') {
			return '<div class="de-controls"><input class="de-control" type="submit" value="' . $label . '"/></div>';
		}
	}

	public static function block_link($content, $params, $open) {
		if (!$open) {
			$params['url'] = \cb\array_item('url', $params, array('required' => true, 'prefix' => \config\settings()->BASE_DIR));

			$url = $params['url'];
			$ajax = \cb\array_item('ajax', $params, array('default' => true, 'options' => array(false, true)));
			$class = \cb\array_item('class', $params, array('default' => 'link'));
			$params['system'] = array(
				'requestType' => 'MAIN',
	//			'requestId' => null
			);
			$jsArgs = \system\Utils::php2Js($params); //array_merge(array('url' => $url), \cb\array_item('args', $params, array('default' => array()))));

			if ($ajax) {
				$confirm = \cb\array_item('confirm', $params, array('default' => false, 'options' => array(false, true)));
				if ($confirm) {
					$confirmTitle = str_replace("'", "\\'", \cb\array_item('confirmTitle', $params, array('default' => '')));
					$confirmQuest = str_replace("'", "\\'", \cb\array_item('confirmQuest', $params, array('default' => '')));
					$action = "ciderbit.confirm('" . $confirmTitle . "', '" . $confirmQuest . "', " . $jsArgs . "); return false;";
				} else {
					$action = "ciderbit.request(" . $jsArgs . "); return false;";
				}
			}
			return 
				'<a href="' . $url . '"'
				. (empty($class) ? '' : ' class="' . $class . '"')
				. (empty($action) ? '' : ' onclick="' . $action . '"') . '>'
				. $content
				. '</a>';
		}
	}
	
	public static function display_node($node, $display = 'default') {
		$api = \system\view\Api::getInstance();
		
		$templates = array(
			'node-'. $display . '--' . $node->id,
			'node-'. $display . '-' . $node->type,
			'node-'. $display
		);
		if ($display != 'default') {
			$templates += array(
				'node-default--' . $node->id,
				'node-default-' . $node->type,
				'node-default'
			);
		}
		
		foreach ($templates as $t) {
			if (\system\Main::templateExists($t)) {
				\system\view\Api::import($t, array('node' => $node));
				return;
			}
		}
	}
	
	public static function access($url, $args = array()) {
		return \system\Main::checkAccess($url, $args);
	}

	//////////////////////
	// 
	//////////////////////
}

?>