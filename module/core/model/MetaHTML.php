<?php
namespace module\core\model;

class MetaHTML extends \system\metatypes\MetaString {
	public function getEditWidgetDefault() {
		return 'textarea';
	}
	
	protected function validateSingle($x) {
		parent::validateSingle($x);
		$tags = $this->getAttr('tags');
		if ($tags) {
			if (\array_key_exists('allowed', $tags)) {

			}
		}
	}
}
?>