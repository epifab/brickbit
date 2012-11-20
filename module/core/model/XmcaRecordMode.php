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
 * Class for managing table: xmca_record_mode
 * XMCA PHP Generator 0.1 - Auto generated code
 * @author episoft
 */
class XmcaRecordMode extends RecordsetBuilder implements RecordsetBuilderInterface {
	const MODE_NOBODY = 0;
	const MODE_SU = 1;
	const MODE_SU_OWNER = 2;
	const MODE_SU_OWNER_GROUP = 3;
	const MODE_ANYONE = 4;
	
	public function getTableName() {
		return "xmca_record_mode";
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
				
			// Owner Id
			case "owner_id":
				$metaType = new MetaInteger($name, $this);
				$metaType->setDefaultValue(0);
				$metaType->addValidate(function($x) {
					Validation::checkNotNullable($x);
				});
				break;
				
			// Last Modifier Id
			case "last_modifier_id":
				$metaType = new MetaInteger($name, $this);
				$metaType->setDefaultValue(0);
				$metaType->addValidate(function($x) {
					Validation::checkNotNullable($x);
				});
				break;
				
			// Group Id
			case "group_id":
				$metaType = new MetaInteger($name, $this);
				$metaType->setDefaultValue(null);
				$metaType->addValidate(function($x) {
				});
				break;
				
			// Read Mode
			case "read_mode":
				$metaType = new MetaOptions($name, $this);
				$metaType->setDefaultValue(null);
				$metaType->setOptions(array(
					self::MODE_NOBODY => "Nobody",
					self::MODE_SU => "Superusers only",
					self::MODE_SU_OWNER => "Superusers and owner only",
					self::MODE_SU_OWNER_GROUP => "Superusers, owner and group only",
					self::MODE_ANYONE => "Anyone"
				));
				$metaType->addValidate(function($x) {
				});
				break;
				
			// Edit Mode
			case "edit_mode":
				$metaType = new MetaOptions($name, $this);
				$metaType->setDefaultValue(null);
				$metaType->setOptions(array(
					self::MODE_NOBODY => "Nobody",
					self::MODE_SU => "Superusers only",
					self::MODE_SU_OWNER => "Superusers and owner only",
					self::MODE_SU_OWNER_GROUP => "Superusers, owner and group only",
					self::MODE_ANYONE => "Anyone"
				));
				$metaType->addValidate(function($x) {
				});
				break;
				
			// Ins Date Time
			case "ins_date_time":
				$metaType = new MetaDateTime($name, $this);
				$metaType->setDefaultValue(\time());
				$metaType->addValidate(function($x) {
					Validation::checkNotNullable($x);
				});
				break;
				
			// Last Upd Date Time
			case "last_upd_date_time":
				$metaType = new MetaDateTime($name, $this);
				$metaType->setDefaultValue(\time());
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
			case "owner":
				$builder = new XmcaUser();
				$builder->setParent($this, $name, array("owner_id" => "id"));
				$builder->setJoinType("LEFT");
				$builder->setOnDelete("NO_ACTION");
				$builder->setOnUpdate("NO_ACTION");
				return $builder;
				break;
				
			case "last_modifier":
				$builder = new XmcaUser();
				$builder->setParent($this, $name, array("last_modifier_id" => "id"));
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
			case "logs":
				if (!($builder instanceof XmcaRecordModeLog)) {
					throw new InternalErrorException("Invalid object [expected type: XmcaRecordModeLog]");
				}
				$builder->setParent($this, $name, array("id" => "record_mode_id"));
				$builder->setJoinType("LEFT");
				$builder->setOnDelete("CASCADE");
				$builder->setOnUpdate("CASCADE");
				return $builder;
				break;
				
			default:
				break;
		}
	}
	
//	public static function checkEditPermission($userId, $id) {
//		list($read, $edit) = XmcaUser::checkRecordPermission($userId, $id);
//		return $edit;
//	}
	
//	public static function checkReadPermission($userId, $id) {
//		list($read, $edit) = XmcaUser::checkRecordPermission($userId, $id);
//		return $read;
//	}
	
//	public static function checkRecordPermissions($userId, $id) {
//		$recordModeBuilder = new XmcaRecordMode();
//		
//		$recordModeBuilder->using(
//			"edit_mode",
//			"read_mode",
//			"owner_id",
//			"group_id"
//		);
//		$recordModeBuilder->setFilter(
//			new FilterClause($recordModeBuilder->searchMetaType("id"), "EQ", $id)
//		);
//		$recordMode = $recordModeBuilder->selectFirst();
//		
//		if (\is_null($recordMode)) {
//			return null;
//		}
//		
//		return array(
//			self::getPermission($recordMode->read_mode, $recordMode, $userId),
//			self::getPermission($recordMode->edit_mode, $recordMode, $userId)
//		);
//	}
	
//	public static function getPermission($mode, $recordMode, $userId) {
//		if (\is_null($recordMode)) {
//			return true;
//		}
//		switch ($mode) {
//			case self::MODE_NOBODY:
//				return false;
//				break;
//
//			case self::MODE_SU:
//				return XmcaUser::isSuperuser($userId);
//				break;
//
//			case self::MODE_SU_OWNER:
//				return XmcaUser::isSuperuser($userId) || $userId == $recordMode->owner_id;
//				break;
//
//			case self::MODE_SU_OWNER_GROUP:
//				$query = 
//					"SELECT"
//					. " COUNT(*) AS allowed"
//					. " FROM"
//					. " xmca_user_group xug"
//					. " WHERE"
//					. " xug.user_id = " . MetaInteger::stdProg2Db($userId)
//					. " AND xug.group_id = " . MetaInteger::stdProg2Db($record_mode->group_id)
//					;
//				return XmcaUser::isSuperuser($userId) || $userId == $recordMode->owner_id || \system\model\DataLayerCore::getInstance()->executeScalar($query, __FILE__, __LINE__) == 1;
//				break;
//
//			default:
//				return true;
//				break;
//		}
//	}
}
?>