<?php
namespace system\model;

class MetaVirtual extends MetaString {
	private $handle;
	public function setHandler($handle) {
		eval('$this->handle = ' . $handle . ';');
	}
	public function getHandler() {
		return $this->handle;
	}
}
?>