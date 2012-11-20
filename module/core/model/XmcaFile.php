<?php
namespace module\core\model;

use system\model\MetaType;
use system\model\MetaInteger;
use system\model\MetaReal;
use system\model\MetaString;
use system\model\MetaOptions;
use system\model\MetaBoolean;
use system\model\MetaDate;
use system\model\MetaTime;
use system\model\MetaDateTime;
use system\model\RecordsetBuilder;
use system\model\RecordsetBuilderInterface;
use system\Validation;
use system\ValidationException;

/**
 * Class for managing table: xmca_file
 * XMCA PHP Generator 0.1 - Auto generated code
 * @author episoft
 */
class XmcaFile extends RecordsetBuilder implements RecordsetBuilderInterface {
	public function getTableName() {
		return "xmca_file";
	}
	
	public function isAutoIncrement() {
		return true;
	}
	
	protected function loadKeys() {
		return array (
			"primary_key" => array("id"),			
			"file_path" => array("dir_id", "name")			
		);
	}
	
	protected function loadMetaType($name) {
		$metaType = null;
	
		switch ($name) {
			case "id":
				$metaType = new MetaInteger($name, $this);
				$metaType->addValidate(function($x) {
					throw new ValidationException("Campo AUTO_INCREMENT.");
				});
				break;
				
			case "dir_id":
				$metaType = new MetaInteger($name, $this);
				$metaType->addValidate(function($x) {
					Validation::checkNotNullable($x);
				});
				break;
				
			case "name":
				$metaType = new MetaString($name, $this);
				$metaType->setDefaultValue("");
				$metaType->addValidate(function($x) {
					Validation::checkNotEmpty($x);
					Validation::checkSize($x, 1, 80);
					Validation::checkNotNullable($x);
				});
				break;
				
			case "size":
				$metaType = new MetaInteger($name, $this);
				$metaType->addValidate(function($x) {
					Validation::checkNotEmpty($x);
				});
				break;
			
			case "path":
				$metaType = new MetaString($name, $this);
				$metaType->setVirtual(parent::evalSelectExpression("CONCAT(@[dir.path], @[name])"));
				break;
			
			default:
				break;
		}

		return $metaType;
	}
	
	protected function loadHasOneRelationBuilder($name) {
		switch ($name) {
			case "dir":
				$builder = new XmcaDir();
				$builder->setParent($this, $name, array("dir_id" => "id"));
				$builder->setJoinType("LEFT");
				$builder->setOnDelete("NO_ACTION");
				$builder->setOnUpdate("NO_ACTION");
				return $builder;
				break;
				
			default:
				break;
		}
	}
	
	protected function loadHasManyRelationBuilder($name, RecordsetBuilderInterface $builder) {
		switch ($name) {
			default:
				break;
		}
	}
}
?>