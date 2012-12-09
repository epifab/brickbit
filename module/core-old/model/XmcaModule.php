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
 * Class for managing table: xmca_module
 * XMCA PHP Generator 0.1 - Auto generated code
 * @author episoft
 */
class XmcaModule extends RecordsetBuilder implements RecordsetBuilderInterface {
	public function getTableName() {
		return "xmca_component";
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
			case "id":
				$metaType = new MetaInteger($name, $this);
				$metaType->setDefaultValue(0);
				$metaType->addValidate(function($x) {
					throw new ValidationException("Campo AUTO_INCREMENT.");
				});
				break;
				
			case "name":
				$metaType = new MetaString($name, $this);
				$metaType->addValidate(function($x) {
					Validation::checkSize($x, null, 32);
					Validation::checkPattern($x, "^[a-zA-Z_][a-zA-Z_0-9]*$");
					Validation::checkNotNullable($x);
				});
				break;

			case "active":
				$metaType = new MetaBoolean($name, $this);
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
				if (!($builder instanceof XmcaComponent)) {
					throw new InternalErrorException("Invalid object [expected type: XmcaComponent]");
				}
				$builder->setParent($this, $name, array("id" => "module_id"));
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