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
 * Class for managing table: xmca_group
 * XMCA PHP Generator 0.1 - Auto generated code
 * @author episoft
 */
class XmcaGroup extends RecordsetBuilder implements RecordsetBuilderInterface {
	const ADMINS_GROUP_ID = 1;
	const GUESTS_GROUP_ID = 2;
	
	public function getTableName() {
		return "xmca_group";
	}
	
	public function isAutoIncrement() {
		return true;
	}
	
	protected function loadKeys() {
		return array (
			"primary_key" => array("id")			
		);
	}
	
	protected function loadMetaType($name) {
		$metaType = null;
	
		switch ($name) {
			// Id
			case "id":
				$metaType = new MetaInteger($name, $this);
				$metaType->setDefaultValue(0);
				$metaType->addValidate(function($x) {
					throw new ValidationException("Campo AUTO_INCREMENT.");
				});
				break;
				
			// Name
			case "name":
				$metaType = new MetaString($name, $this);
				$metaType->setDefaultValue("");
				$metaType->addValidate(function($x) {
					Validation::checkSize($x, null, 30);
					Validation::checkPattern($x, "/[a-zA-Z_][a-zA-Z_0-9]*$");
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
			default:
				break;
		}
	}
	
	protected function loadHasManyRelationBuilder($name, RecordsetBuilderInterface $builder) {
		switch ($name) {
			case "components":
				if (!($builder instanceof XmcaGroupComponent)) {
					throw new InternalErrorException("Invalid object [expected type: XmcaGroupComponent]");
				}
				$builder->setParent($this, $name, array("id" => "group_id"));
				$builder->setJoinType("LEFT");
				$builder->setOnDelete("CASCADE");
				$builder->setOnUpdate("CASCADE");
				return $builder;
				break;
				
			case "users":
				if (!($builder instanceof XmcaUserGroup)) {
					throw new InternalErrorException("Invalid object [expected type: XmcaUserGroup]");
				}
				$builder->setParent($this, $name, array("id" => "group_id"));
				$builder->setJoinType("LEFT");
				$builder->setOnDelete("CASCADE");
				$builder->setOnUpdate("CASCADE");
				return $builder;
				break;
				
			default:
				break;
		}
	}
}
?>