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
 * @author episoft
 */
class XmcaTagStat extends RecordsetBuilder implements RecordsetBuilderInterface {
	public function __construct($tableAlias=null, $relationName='', $parentBuilder=null, $clauses=null, $joinType=null) {
		parent::__construct($tableAlias, $relationName, $parentBuilder, $clauses, $joinType);
		$sqlExpression = 
<<<SQLEXPRESSION

					SELECT
						xt.id AS tag_id,
						(SELECT COUNT(*) FROM xmca_page_tag) AS total_page_size,
						(CASE WHEN xct.size IS NULL THEN 0 ELSE xct.size END) AS page_size,
						(SELECT COUNT(*) FROM xmca_content_tag) AS total_content_size,
						(CASE WHEN xpt.size IS NULL THEN 0 ELSE xpt.size END) AS content_size
					FROM 
						xmca_tag xt
						LEFT JOIN (SELECT tag_id, COUNT(*) AS size FROM xmca_page_tag GROUP BY tag_id) xpt ON xpt.tag_id = xt.id
						LEFT JOIN (SELECT tag_id, COUNT(*) AS size FROM xmca_content_tag GROUP BY tag_id) xct ON xct.tag_id = xt.id
				
SQLEXPRESSION;
		parent::setVirtual($sqlExpression);
	}
	public function getTableName() {
		return "xmca_tag_stat";
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
			case "tag_id":
				$metaType = new MetaInteger($name, $this);
				break;
				
			case "page_size":
				$metaType = new MetaInteger($name, $this);
				break;
				
			case "content_size":
				$metaType = new MetaInteger($name, $this);
				break;
				
			case "total_content_size":
				$metaType = new MetaInteger($name, $this);
				break;
				
			case "total_content_size":
				$metaType = new MetaInteger($name, $this);
				break;

			default:
				break;
		}

		return $metaType;
	}
	
	protected function loadHasOneRelationBuilder($name) {
		switch ($name) {
			case "tag":
				$builder = new XmcaTag();
				$builder->setParent($this, $name, array("tag_id" => "id"));
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