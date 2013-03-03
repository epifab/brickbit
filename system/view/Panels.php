<?php
namespace system\view;

class Panels {
	private static $instance;
	
	private $panels = array();
	private $level = 0;
	
	private $filterName;
	private $filterClass;
	
	private function __construct($filterName=null, $filterClass=null) {
		$this->filterName = $filterName;
		$this->filterClass = $filterClass;
	}
	
	/**
	 * @param type $filterName
	 * @param type $filterClass
	 * @return Panels
	 */
	public static function getInstance($filterName=null, $filterClass=null) {
		if (!self::$instance) {
			self::$instance = new self($filterName, $filterClass);
		}
		return self::$instance;
	}
	
	public function isOpened() {
		return $this->level > 0;
	}
	
	public function openPanel() {
		$this->level++;
	}
	
	public function closePanel($id, $name, $class, $content) {
		$this->level--;
		if (!$this->isOpened()) {
			$print = true;
			if ($this->filterName && $name != $this->filterName) {
				$print = false;
			}
			if ($print) {
				if ($this->filterClass) {
					$class = is_array($class) ? $class : explode(" ", $class);
					$print = in_array($this->filterClass, $class);
				}
				if ($print) {
					$this->addPanel($id, $content);
				}
			}
		}
	}
	
	public function getPanel($id) {
		return \array_key_exists($id, $this->panels)
			? $this->panels[$id]
			: '';
	}
	
	public function addPanel($id, $content) {
		$this->panels[$id] = $content;
	}
	
	public function getPanels() {
		return $this->panels;
	}
	
	public static function getFormId() {
		$vars = \system\view\Template::current()->getVars();
		return 'system-panel-form-' . $vars['system']['component']['requestId'];
	}
	
	public static function getFormName() {
		$vars = \system\view\Template::current()->getVars();
		return 'system-panel-' . $vars['system']['component']['requestId'];
	}
	
	public static function getForm() {
		static $forms = array();

		$vars = \system\view\Template::current()->getVars();

		if (\array_key_exists($vars['system']['component']['requestId'], $forms)) {
			return $forms[$vars['system']['component']['requestId']];
		}
		else {
			$formId = self::getFormId();
			$formName = self::getFormName();

			$forms[$vars['system']['component']['requestId']] = array($formId, $formName, '');

			$form =
				'<form class="system-panel-form" id="' . $formId . '" name="' . $formName . '" method="post" action="' . $vars['system']['component']['url'] .'">'
				// forzo le risposte ad avere gli stessi ID per il form e per i contenuti
				. '<input type="hidden" name="system[requestId]" value="' . $vars['system']['component']['requestId'] . '"/>'
				. '<input type="hidden" name="system[requestType]" value="PAGE-PANELS"/>';

			foreach ($vars['system']['component']['requestData'] as $key => $value) {
				if ($key == 'system') {
					continue;
				}
				$args = array();
				\system\Utils::arg2Input($args, $key, $value);
				foreach ($args as $k => $v) {
					$form .= '<input type="hidden" name="' . \system\Utils::escape($k, '"') . '" value="' . \system\Utils::escape($k, '"') . '"/>';
				}
			}
			$form .= '</form>';

			// the form code is returned only once
			return array($formId, $formName, $form);
		}
	}
}
?>