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
 * Class for managing table: xmca_tag
 * XMCA PHP Generator 0.1 - Auto generated code
 * @author episoft
 */
class XmcaTag extends RecordsetBuilder {
	public function getTableName() {
		return "xmca_tag";
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
					throw new ValidationException("AUTO_INCREMENT field");
				});
				break;
				
			case "value":
				$metaType = new MetaString($name, $this);
				$metaType->addValidate(function($x) {
					\system\Validation::checkSize($x, 0, 100);
					\system\Validation::checkAlNum($x);
				});
				break;
			
			case "size":
				$metaType = new MetaInteger($name, $this);
				$metaType->setVirtual($this->evalSelectExpression(
					"@[stat.page_size] + @[stat.content_size]"
				));
				break;
			
			case "rate":
				$metaType = new MetaReal($name, $this);
				$metaType->setVirtual($this->evalSelectExpression(
					"(@[stat.page_size] + @[stat.content_size]) / (@[stat.total_page_size] + @[stat.total_content_size])"
				));
				break;
		}
		
		return $metaType;
	}
	
	protected function loadHasOneRelationBuilder($name) {
		switch ($name) {
			case "stat":
				$builder = new XmcaTagStat();
				$builder->setParent($this, $name, array("id" => "tag_id"));
				$builder->setJoinType("LEFT");
				$builder->setOnDelete("NO_ACTION");
				$builder->setOnUpdate("NO_ACTION");
				return $builder;
				break;
		}
	}
	
	protected function loadHasManyRelationBuilder($name, RecordsetBuilderInterface $builder) {
		switch ($name) {
			case "contents":
				if (!($builder instanceof XmcaContentTag)) {
					throw new InternalErrorException("Invalid object [expected type: XmcaContentTag]");
				}
				$builder->setParent($this, $name, array("id" => "tag_id"));
				$builder->setJoinType("LEFT");
				$builder->setOnDelete("CASCADE");
				$builder->setOnUpdate("CASCADE");
				return $builder;
				break;
				
			case "pages":
				if (!($builder instanceof XmcaPageTag)) {
					throw new InternalErrorException("Invalid object [expected type: XmcaPageTag]");
				}
				$builder->setParent($this, $name, array("id" => "tag_id"));
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