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
 * Class for managing table: xmca_page
 * XMCA PHP Generator 0.1 - Auto generated code
 * @author episoft
 */
class XmcaPage extends XmcaRecordModedTable {
	public function getTableName() {
		return "xmca_page";
	}
	
	public function getRecordModeKeyName() {
		return "record_mode_id";
	}
	
	public function isAutoIncrement() {
		return true;
	}
	
	protected function loadKeys() {
		return array (
			"primary" => array("id"),
			"url_key" => array("url")
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
				
			case "url":
				$metaType = new MetaString($name, $this);
				$metaType->addValidate(function($x) {
					\system\Validation::checkSize($x, 0, 100);
					\system\Validation::checkAlNum($x);
				});
				break;
			
			case "style_code":
				$metaType = new MetaString($name, $this);
				$metaType->addValidate(function($x) {
					\system\Validation::checkSize($x, 1, 15);
					\system\Validation::checkAlNum($x);
				});
				break;
			
			case "sort_index":
				$metaType = new MetaInteger($name, $this);
				$metaType->addValidate(function($x) {
					\system\Validation::checkNotNullable($x);
					\system\Validation::checkRange($x, 1, null);
				});
				break;
			
			case "content_filters":
				$metaType = new MetaBoolean($name, $this);
				break;
			
			case "content_paging":
				$metaType = new MetaInteger($name, $this);
				$metaType->addValidate(function($x) {
					\system\Validation::checkRange($x, 0, null);
				});
				break;
			
			case "content_sorting":
				$metaType = new MetaOptions($name, $this);
				$metaType->setOptions(array(
					"sort_index_asc" => "Manual sorting",
					"date_asc" => "Date (ascending)",
					"date_desc" => "Date (descending)"
				));
				break;
			
			case "title":
			case "body":
				$metaType = new MetaString($name, $this);
				$metaType->setVirtual(parent::evalSelectExpression("CASE WHEN @[text_" . \system\Lang::getLangId() . "." . $name . "] IS NOT NULL THEN @[text_" . \system\Lang::getLangId() . "." . $name . "] ELSE @[text_" . \config\settings()->DEFAULT_LANG . "." . $name . "] END"));
				break;
			
			default:
				return parent::loadMetaType($name);
				break;
		}

		return $metaType;
	}
	
	protected function loadHasOneRelationBuilder($name) {

		if (substr($name,0,4) == "text") {
			// Relazioni fittizie text_it, text_en ...
			foreach (\config\settings()->LANGUAGES as $langId) {
				if ($name == "text_" . $langId) {
					$builder = new XmcaPageText();
					$builder->setParent($this, $name, array("id" => "page_id"));	
					$builder->setJoinType("LEFT");
					$builder->setOnDelete("NO_ACTION");
					$builder->setOnUpdate("NO_ACTION");
					$builder->addFilter(new \system\model\FilterClause($builder->lang_id, "=", $langId));
					return $builder;
				}
			}
		}
		
		switch ($name) {
			case "style":
				$builder = new XmcaPageStyle();
				$builder->setParent($this, $name, array("style_code" => "code"));
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
			case "contents":
				if (!($builder instanceof XmcaContent)) {
					throw new InternalErrorException("Invalid object [expected type: XmcaContent]");
				}
				$builder->using("supercontent_id");
				$builder->setParent($this, $name, array("id" => "page_id"));
				$builder->setJoinType("LEFT");
				$builder->setOnDelete("CASCADE");
				$builder->setOnUpdate("CASCADE");
				$builder->addFilter(new \system\model\FilterClause($builder->supercontent_id, "IS_NULL"));
				return $builder;
				break;
				
			case "tags":
				if (!($builder instanceof XmcaPageTag)) {
					throw new InternalErrorException("Invalid object [expected type: XmcaPageTag]");
				}
				$builder->setParent($this, $name, array("id" => "page_id"));
				$builder->setJoinType("LEFT");
				$builder->setOnDelete("CASCADE");
				$builder->setOnUpdate("CASCADE");
				return $builder;
				break;
				
			case "texts":
				if (!($builder instanceof XmcaPageText)) {
					throw new InternalErrorException("Invalid object [expected type: XmcaPageText]");
				}
				$builder->setParent($this, $name, array("id" => "page_id"));
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