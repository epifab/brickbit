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
 * Class for managing table: xmca_user_group
 * XMCA PHP Generator 0.1 - Auto generated code
 * @author episoft
 */
class XmcaUserGroup extends RecordsetBuilder implements RecordsetBuilderInterface {
	public function getTableName() {
		return "xmca_user_group";
	}
	
	public function isAutoIncrement() {
		return false;
	}
	
	protected function loadKeys() {
		return array (
			"primary_key" => array("user_id", "group_id")			
		);
	}
	
	protected function loadMetaType($name) {
		$metaType = null;
	
		switch ($name) {
			// User Id
			case "user_id":
				$metaType = new MetaInteger($name, $this);
				$metaType->setDefaultValue(0);
				$metaType->addValidate(function($x) {
					Validation::checkNotNullable($x);
				});
				break;
				
			// Group Id
			case "group_id":
				$metaType = new MetaInteger($name, $this);
				$metaType->setDefaultValue(0);
				$metaType->addValidate(function($x) {
					Validation::checkNotNullable($x);
				});
				break;
				
			default:
				break;
		}

		return $metaType;
	}
	
	protected function loadHasOneRelationBuilder($name) {
		switch ($name) {
			case "user":
				$builder = new XmcaUser();
				$builder->setParent($this, $name, array("user_id" => "id"));
				$builder->setJoinType("LEFT");
				$builder->setOnDelete("NO_ACTION");
				$builder->setOnUpdate("NO_ACTION");
				return $builder;
				break;
				
			case "group":
				$builder = new XmcaGroup();
				$builder->setParent($this, $name, array("group_id" => "id"));
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