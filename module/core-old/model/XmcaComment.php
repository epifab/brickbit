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
 * Class for managing table: xmca_comment
 * XMCA PHP Generator 0.1 - Auto generated code
 * @author episoft
 */
class XmcaComment extends XmcaRecordModedTable {
	public function getTableName() {
		return "xmca_comment";
	}
	
	public function getRecordModeKeyName() {
		return "record_mode_id";
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
			case "id":
				$metaType = new MetaInteger($name, $this);
				$metaType->addValidate(function($x) {
					throw new ValidationException("Campo AUTO_INCREMENT.");
				});
				break;
				
			case "content_id":
				$metaType = new MetaInteger($name, $this);
				$metaType->addValidate(function($x) {
					Validation::checkNotEmpty($x);
				});
				break;
				
			case "comment_id":
				$metaType = new MetaInteger($name, $this);
				break;
			
			case "approved":
				$metaType = new MetaBoolean($name, $this);
				break;
				
			case "body":
				$metaType = new MetaString($name, $this);
				$metaType->addValidate(function($x) {
					\system\Validation::checkNotEmpty($x);
				});
				break;
			
			default:
				return parent::loadMetaType($name);
				break;
		}

		return $metaType;
	}
	
	protected function loadHasOneRelationBuilder($name) {
		switch ($name) {
			case "content":
				$builder = new XmcaContent();
				$builder->setParent($this, $name, array("content_id" => "id"));
				$builder->setJoinType("LEFT");
				$builder->setOnDelete("NO_ACTION");
				$builder->setOnUpdate("NO_ACTION");
				return $builder;
				break;
				
			case "supercomment":
				$builder = new XmcaComment();
				$builder->setParent($this, $name, array("supercomment_id" => "id"));
				$builder->setJoinType("LEFT");
				$builder->setOnDelete("NO_ACTION");
				$builder->setOnUpdate("NO_ACTION");
				return $builder;
				break;
				
			default:
				return parent::loadHasOneRelationBuilder($name);
				break;
		}
	}
	
	protected function loadHasManyRelationBuilder($name, RecordsetBuilderInterface $builder) {
		switch ($name) {
			case "comments":
				if (!($builder instanceof XmcaComment)) {
					throw new InternalErrorException("Invalid object [expected type: XmcaContent]");
				}
				$builder->setParent($this, $name, array("id" => "supercomment_id"));
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