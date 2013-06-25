<?php
namespace system\metatypes;

class MetaBlob extends MetaType {
	public function prog2Db($x) {
		return "'" . \base64_encode($x) . "'";
	}
	
	public function db2Prog($x) {
		return \base64_decode($x);
	}

	protected function getEditWidgetDefault() {
		return null;
	}
	
	public function toProg($x) {
		return $x;
	}
}