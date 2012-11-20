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
 * Class for managing table: xmca_record_mode_log
 * XMCA PHP Generator 0.1 - Auto generated code
 * @author episoft
 */
class XmcaRecordModeLog extends RecordsetBuilder implements RecordsetBuilderInterface {
	public function getTableName() {
		return "xmca_record_mode_log";
	}
	
	public function isAutoIncrement() {
		return true;
	}
	
	protected function loadKeys() {
		return array (
			"primary" => array("id")			
		);
	}
	
	protected function loadMetaType($name) {
		$metaType = null;
	
		switch ($name) {
			// Id
			case "id":
				$metaType = new MetaInteger($name, $this);
				$metaType->setDefaultValue(null);
				$metaType->addValidate(function($x) {
					throw new ValidationException("Campo AUTO_INCREMENT.");
				});
				break;
				
			// Record Mode Id
			case "record_mode_id":
				$metaType = new MetaInteger($name, $this);
				$metaType->setDefaultValue(null);
				$metaType->addValidate(function($x) {
				});
				break;
				
			// Modifier Id
			case "modifier_id":
				$metaType = new MetaInteger($name, $this);
				$metaType->setDefaultValue(null);
				$metaType->addValidate(function($x) {
				});
				break;
				
			// Upd Date Time
			case "upd_date_time":
				$metaType = new MetaDateTime($name, $this);
				$metaType->setDefaultValue(\time());
				$metaType->addValidate(function($x) {
				});
				break;
				
			default:
				break;
		}

		return $metaType;
	}
	
	protected function loadHasOneRelationBuilder($name) {
		switch ($name) {
			case "record_mode":
				$builder = new XmcaRecordMode();
				$builder->setParent($this, $name, array("record_mode_id" => "id"));
				$builder->setJoinType("LEFT");
				$builder->setOnDelete("NO_ACTION");
				$builder->setOnUpdate("NO_ACTION");
				return $builder;
				break;
				
			case "modifier":
				$builder = new XmcaUser();
				$builder->setParent($this, $name, array("last_modifier_id" => "id"));
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