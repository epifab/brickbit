<?php
namespace system\model;

class MetaVirtual extends MetaString {
	private $handle;
	public function setHandler($handle) {
		$this->handle = $handle;
	}
	public function getHandler() {
		return $this->handle;
	}
}
?>