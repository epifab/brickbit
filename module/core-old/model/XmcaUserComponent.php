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
 * XMCA PHP Generator 0.1 - Auto generated code
 * @author episoft
 */
class XmcaUserComponent extends RecordsetBuilder implements RecordsetBuilderInterface {
	public function __construct($tableAlias=null, $relationName='', $parentBuilder=null, $clauses=null, $joinType=null) {
		parent::__construct($tableAlias, $relationName, $parentBuilder, $clauses, $joinType);
		$sqlExpression = 
<<<SQLEXPRESSION

					SELECT
						xug.user_id AS user_id,
						xc.id AS component_id,
						xc.name AS component_name
					FROM 
						xmca_user_group xug
						LEFT JOIN xmca_group_component xgc ON xgc.group_id = xug.group_id
						LEFT JOIN xmca_component xc ON xc.id = xgc.component_id
				
SQLEXPRESSION;
		parent::setVirtual($sqlExpression);
	}
	public function getTableName() {
		return "xmca_user_component";
	}
	
	public function isAutoIncrement() {
		return false;
	}
	
	protected function loadKeys() {
		return null;
	}
	
	protected function loadMetaType($name) {
		$metaType = null;
	
		switch ($name) {
			// User Id
			case "user_id":
				$metaType = new MetaInteger($name, $this);
				break;
				
			// Component Id
			case "component_id":
				$metaType = new MetaInteger($name, $this);
				break;
				
			// Component Name
			case "component_name":
				$metaType = new MetaString($name, $this);
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
				$builder->setParent($this, $name, array("event_id" => "id"));
				$builder->setJoinType("LEFT");
				$builder->setOnDelete("NO_ACTION");
				$builder->setOnUpdate("NO_ACTION");
				return $builder;
				break;
				
			case "component":
				$builder = new XmcaComponent();
				$builder->setParent($this, $name, array("component_id" => "id"));
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