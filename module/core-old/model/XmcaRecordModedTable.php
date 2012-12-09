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
 * Class for managing table: xmca_record_moded_table
 * XMCA PHP Generator 0.1 - Auto generated code
 * @author episoft
 */
abstract class XmcaRecordModedTable extends RecordsetBuilder implements RecordsetBuilderInterface {

	public function __construct($importKeys=RecordsetBuilder::OPT_USE_KEYS_PRIMARY) {
		parent::__construct($importKeys);
		parent::using(
			"record_mode.read_mode",
			"record_mode.edit_mode",
			"record_mode.owner_id",
			"record_mode.group_id"
		);
	}
	
	protected function loadMetaType($name) {
		$metaType = null;
	
		switch ($name) {
			// Record Mode Id
			case $this->getRecordModeKeyName():
				$metaType = new MetaInteger($name, $this);
				$metaType->setDefaultValue(0);
				$metaType->addValidate(function($x) {
					throw new ValidationException("Campo AUTO_INCREMENT.");
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
				$builder->setParent($this, $name, array($this->getRecordModeKeyName() => "id"));
				$builder->setJoinType("LEFT");
				$builder->setOnDelete("CASCADE");
				$builder->setOnUpdate("CASCADE");
				return $builder;
				break;
				
			default:
				break;
		}
	}
	
	public function useRecordModeLogs() {
		$builder = new XmcaRecordModeLog();
		$builder->using(
			"id",
			"record_mode_id",
			"modifier_id",
			"upd_date"
		);
		parent::setHasManyRelationBuilder("record_mode.logs", $builder);
		return $builder;
	}
	
	public function isRecordModed() {
		return true;
	}
	public function getRecordModeKeyName() {
		return "record_mode_id";
	}
}
?>