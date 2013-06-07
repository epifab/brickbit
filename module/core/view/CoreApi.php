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
			Form::startForm($formId, $recordset);
		} 
		else {
			Form::closeForm();

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
		$formId = Form::getActiveForm();
		
		$options = \cb\array_item('options', $params, array('default' => array(), 'type' => 'array'));
		
		if ($formId) {
			$recordset = Form::getRecordset();
			
			if ($recordset && isset($params['path'])) {
				$path = $params['path'];
				
				$f = $recordset->getBuilder()->searchField($path, true);

				$widget = isset($params['widget']) ? $params['widget'] : $f->getEditWidget();

				$id = Form::getActiveForm() . '__recordset__' . \cb\plaintext(\str_replace('.', '__', $path));
				$name = Form::getActiveForm() . '[recordset][' . \cb\plaintext($path) . ']';
				$value = $recordset->getProg($path);
				
				$inputOptions = 
					$options
					+ array('id' => $id, 'name' => $name, 'value' => $value)
					+ $f->getAttributes();
				
				$inputOptions['value'] = Form::addInput($widget, $name, $value, $inputOptions);
				
				Form::addRsField($path, $name);
				
				return \system\view\Widget::getWidget($widget)->render($inputOptions);
			}
			
			else {
				$widget = \cb\array_item('widget', $params, array('required' => true));
				$name = \cb\array_item('name', $params, array('required' => true));
				$value = \cb\array_item('value', $params, array('required' => true));
				
				Form::addInput($widget, $name, $value, $params);
				
				return \system\view\Widget::getWidget($widget)->render($params);
			}
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
			$jsArgs = \system\utils\Utils::php2Js($params); //array_merge(array('url' => $url), \cb\array_item('args', $params, array('default' => array()))));

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