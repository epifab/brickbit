<?php
namespace module\core;

class Api {
	  //////////////////////
	 // BLOCKS
	//////////////////////
	public function deForm($content) {
		$vars = \system\view\Template::current()->getVars();

		$formId = 'system-edit-form-' . $vars['system']['component']['requestId'];

		$form = "\n"
		. '<form id="' . $formId . '" name="' . $formId . '" method="post" enctype="multipart/form-data" action="' . $vars['system']['component']['url'] .'">'
		// forzo le risposte ad avere gli stessi ID per il form e per i contenuti
		. '<input type="hidden" name="system[requestId]" value="' . $vars['system']['component']['requestId'] . '"/>';

		return $form . $content . '</form>';
	}
	
public function deInput(\system\model\Recordset $recordset, $path, $params) {
		$inputName = 'node[' . $path . ']';
		$inputId = 'edit-node-' . \str_replace('.', '-', $path);
		$type = \system\Utils::getParam('type', $params, array('default' => 'text', 'options' => array('text', 'password')));
		
		return '<input type="' . $type . ' "'
			. ' name="' . $inputName . '"'
			. ' id="' . $inputId . '"'
			. \system\Utils::getParam('size', $params, array('prefix' => ' size="', 'suffix' => '"', 'default' => ''))
			. \system\Utils::getParam('maxlength', $params, array('prefix' => ' maxlength="', 'suffix' => '"', 'default' => ''))
			. \system\Utils::getParam('placeholder', $params, array('prefix' => ' placeholder="', 'suffix' => '"', 'default' => ''))
			. ' class="' . \implode(' ', \array_merge(array('de-input'), \system\Utils::getParam('class', $params, array('defalt' => array())))) . '"'
			. ' value="' . $recordset->getEdit($path) . "/>";
	}
	
	public function deSelect(\system\model\Recordset $recordset, $path, $params) {
		$inputName = 'node[' . $path . ']';
		$inputId = 'edit-node-' . \str_replace('.', '-', $path);
		$type = \system\Utils::getParam('type', $params, array('required' => true));
		
		$x = '<select'
			. ' name="' . $inputName . '"'
			. ' id="' . $inputId . '"'
			. ' class="' . \implode(' ', \array_merge(array('de-input'), \system\Utils::getParam('class', $params, array('defalt' => array())))) . '"/>';
		$mt = $recordset->searchMetaType($path);
		
		$options = \system\Utils::getParam('options', $params, array('default' => false));
		

		$options = $mt->getAttr('options', array('default' => \system\Utils::getParam('options', $params)));
		if (\is_array($options)) {
			foreach ($options as $key => $val) {
				$x .= '<option value="' . $key . '"' 
					. ($key == $recordset->getProg($path) ? ' selected="selected"' : '') . '>'
					. \system\Lang::translate($val) . '</option>';
			}
		}
		$x .= '</select>';
	}
	
	public function deCheckbox(\system\model\Recordset $recordset, $path, $params) {
		$inputName = 'node[' . $path . ']';
		$inputId = 'edit-node-' . \str_replace('.', '-', $path);
		$type = \system\Utils::getParam('type', $params, array('required' => true));
		
		return '<input type="' . $type . ' "'
			. ' name="' . $inputName . '"'
			. ' id="' . $inputId . '"'
			. \system\Utils::getParam('checked', $params, array('prefix' => ' checked="', 'suffix' => '"', 'default' => ''))
			. ' class="' . \implode(' ', \array_merge(array('de-input'), \system\Utils::getParam('class', $params, array('defalt' => array())))) . '"'
			. ' value="' . $recordset->getEdit($path) . "/>";
		
	}
	
	public function deRadios(\system\model\Recordset $recordset, $path, $params) {
		
	}
	
	
	public function link($content, $params) {
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
			$confirm = system\Utils::getParam('confirm', $params, array('default' => false, 'options' => array(false, true)));
			if ($confirm) {
				$confirmTitle = str_replace("'", "\\'", \system\Utils::getParam('confirmTitle', $params, array('default' => '')));
				$confirmQuest = str_replace("'", "\\'", \system\Utils::getParam('confirmQuest', $params, array('default' => '')));
				$action = "xmca.confirm('" . $confirmTitle . "', '" . $confirmQuest . "', " . $jsArgs . "); return false;";
			} else {
				$action = "xmca.request(" . $jsArgs . "); return false;";
			}
		}
		return '<a href="' . $url . '"' 
			. (empty($class) ? '' : ' class="' . $class . '"')
			. (empty($action) ? '' : ' onclick="' . $action . '"') . '>' 
			. $content 
			. '</a>';
	}
	
	public function panelStart($params) {
		system\view\Panels::getInstance()->openPanel();
	}
	
	public function panel($content, $params) {
		list(, $formName, $output) = \system\view\Panels::getForm();
		
		$panelName = \system\Utils::getParam('name', $params, array('required' => true));
		$panelClass = 
			'system-panel system-panel-' . $panelName . ' ' . $formName
			. \system\Utils::getParam('class', $params, array('prefix' => ' ', 'default' => ''));
		$vars = $smarty->getTemplateVars();
		
		$panelId = 'system-panel-' . $vars['system']['component']['requestId'] . '-' . $panelName;
		
		$content = '<div id="' . $panelId . '" class="' . $panelClass . '">' . $content . '</div>';
		
		\system\view\Panels::getInstance()->closePanel($panelId, $panelName, $panelClass, $content);
		
		return $output . $content;
	}
	
	public function panels($content, $params) {
		$content = '';
		
		$panels = \system\view\Panels::getInstance()->getPanels();
		foreach ($panels as $panel) {
			$content .= $panel;
		}
		return $content;
	}
	
	public function protect($content, $params) {
		$url = \system\Utils::getParam('url', $params, array('required' => true));
		$args = \system\Utils::getParam('args', $params, array('default' => array()));
		\system\logic\Module::checkAccess($url, $args) ? $content : '';
	}

	
	  //////////////////////
	 // 
	//////////////////////
}
?>