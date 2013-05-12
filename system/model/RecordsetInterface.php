<?php
namespace system\model;

interface RecordsetInterface {
	/**
	 * @return \system\model\RecordsetBuilder 
	 */
	public function getBuilder();
	
	public function create();
	public function update();
	public function save();
	public function delete();
	
	public function search($path);
	
	public function isStored();
	
	public function getPrimaryKey();
	public function getKey($keyName);
	
	public function getProg($path);
	public function setProg($path, $value);
	public function getDb($path);
	public function setDb($path, $value);
	
	public function setRelation($name, $value);
	public function unsetRelation($name);
	
	public function searchField($path);
	public function getFieldList();
	
	public function toArray();
}
?>
