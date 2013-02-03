<?php
namespace module\core\model;

class MetaHTML extends \system\model\MetaString {
	public function getEditWidgetDefault() {
		return 'wysiwyg';
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