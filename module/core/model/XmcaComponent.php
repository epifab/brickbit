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
 * Class for managing table: xmca_component
 * XMCA PHP Generator 0.1 - Auto generated code
 * @author episoft
 */
class XmcaComponent extends RecordsetBuilder implements RecordsetBuilderInterface {
	public function getTableName() {
		return "xmca_component";
	}
	
	public function isAutoIncrement() {
		return true;
	}
	
	protected function loadKeys() {
		return array (
			"primary_key" => array("id"),			
			"module_component" => array("name", "module_id")			
		);
	}
	
	protected function loadMetaType($name) {
		$metaType = null;
	
		switch ($name) {
			// Id
			case "id":
				$metaType = new MetaInteger($name, $this);
				$metaType->addValidate(function($x) {
					throw new ValidationException("Campo AUTO_INCREMENT.");
				});
				break;
			
			// Id
			case "module_id":
				$metaType = new MetaInteger($name, $this);
				break;
				
			// Name
			case "name":
				$metaType = new MetaString($name, $this);
				$metaType->addValidate(function($x) {
					Validation::checkSize($x, null, 32);
					Validation::checkPattern($x, "^[a-zA-Z_][a-zA-Z_0-9]*$");
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
			case "module":
				$builder = new XmcaModule();
				$builder->setParent($this, $name, array("module_id" => "id"));
				$builder->setJoinType("LEFT");
				$builder->setOnDelete("NO_ACTION");
				$builder->setOnUpdate("NO_ACTION");
				return $builder;
				
			default:
				break;
		}
	}
	
	protected function loadHasManyRelationBuilder($name, RecordsetBuilderInterface $builder) {
		switch ($name) {
			case "groups":
				if (!($builder instanceof XmcaGroupComponent)) {
					throw new InternalErrorException("Invalid object [expected type: XmcaGroupComponent]");
				}
				$builder->setParent($this, $name, array("id" => "component_id"));
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