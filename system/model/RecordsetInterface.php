<?php
namespace system\model;

interface RecordsetInterface {
	public function getBuilder();
	
	public function create();
	public function update();
	public function save();
	public function delete();
	
	public function search($path);
	
	public function isStored();
	
	public function getProg($name);
	public function setProg($name, $value);
	public function getDb($name);
	public function setDb($name, $value);
//	public function getEdit($name);
	public function setEdit($name, $value);
//	public function getRead($name);
	
	public function searchMetaType($path);
	public function getMetaTypeList();
}
?>
