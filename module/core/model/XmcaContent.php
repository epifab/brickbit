<?php
namespace module\core\model;

use system\InternalErrorException;
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
 * Class for managing table: xmca_content
 * XMCA PHP Generator 0.1 - Auto generated code
 * @author episoft
 */
class XmcaContent extends XmcaRecordModedTable {
	public function getTableName() {
		return "xmca_content";
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
					throw new ValidationException("Automatic field");
				});
				break;
				
			case "page_id":
				$metaType = new MetaInteger($name, $this);
				$metaType->addValidate(function($x) {
					Validation::checkNotEmpty($x);
				});
				break;
				
			case "supercontent_id":
				$metaType = new MetaInteger($name, $this);
				$metaType->addValidate(function($x) {
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
					\system\Validation::checkSize($x, 3, 15);
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
			
			case "public_date":
				$metaType = new MetaDateTime($name, $this);
				break;

			case "image_id":
				$metaType = new MetaInteger($name, $this);
				break;
				
			case "video_file_id":
				$metaType = new MetaInteger($name, $this);
				break;
				
			case "audio_file_id":
				$metaType = new MetaInteger($name, $this);
				break;
				
			case "download_file_id":
				$metaType = new MetaInteger($name, $this);
				break;
				
			case "expandable":
				$metaType = new MetaBoolean($name, $this);
				break;
			
			case "comments":
				$metaType = new MetaBoolean($name, $this);
				break;
			
			case "social_networks":
				$metaType = new MetaBoolean($name, $this);
				break;
			
			case "download_file_url":
				$metaType = new MetaString($name, $this);
				$metaType->setVirtual(parent::evalSelectExpression("CONCAT('content/download/', @[url], '.', SUBSTRING_INDEX(@[download_file.name], '.', -1))"));
				break;
			
			case "video_file_url":
				$metaType = new MetaString($name, $this);
				$metaType->setVirtual(parent::evalSelectExpression("CONCAT('content/video/', @[url], '.', SUBSTRING_INDEX(@[video_file.name], '.', -1))"));
				break;
			
			case "audio_file_url":
				$metaType = new MetaString($name, $this);
				$metaType->setVirtual(parent::evalSelectExpression("CONCAT('content/audio/', @[url], '.', SUBSTRING_INDEX(@[audio_file.name], '.', -1))"));
				break;
			
			case "image1_url":
				$metaType = new MetaString($name, $this);
				$metaType->setVirtual(parent::evalSelectExpression("CONCAT('content/image/1/', @[url], '.', SUBSTRING_INDEX(@[image.file1.name], '.', -1))"));
				break;
			
			case "image2_url":
				$metaType = new MetaString($name, $this);
				$metaType->setVirtual(parent::evalSelectExpression("CONCAT('content/image/2/', @[url], '.', SUBSTRING_INDEX(@[image.file2.name], '.', -1))"));
				break;
			
			case "image3_url":
				$metaType = new MetaString($name, $this);
				$metaType->setVirtual(parent::evalSelectExpression("CONCAT('content/image/3/', @[url], '.', SUBSTRING_INDEX(@[image.file3.name], '.', -1))"));
				break;
			
			case "image4_url":
				$metaType = new MetaString($name, $this);
				$metaType->setVirtual(parent::evalSelectExpression("CONCAT('content/image/4/', @[url], '.', SUBSTRING_INDEX(@[image.file4.name], '.', -1))"));
				break;
			
			case "title":
			case "subtitle":
			case "body":
			case "preview":
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
					$builder = new XmcaContentText();
					$builder->setParent($this, $name, array("id" => "content_id"));	
					$builder->setJoinType("LEFT");
					$builder->setOnDelete("NO_ACTION");
					$builder->setOnUpdate("NO_ACTION");
					$builder->addFilter(new \system\model\FilterClause($builder->lang_id, "=", $langId));
					return $builder;
				}
			}
		}
		
		switch ($name) {
			case "page":
				$builder = new XmcaPage();
				$builder->setParent($this, $name, array("page_id" => "id"));
				$builder->setJoinType("LEFT");
				$builder->setOnDelete("NO_ACTION");
				$builder->setOnUpdate("NO_ACTION");
				return $builder;
				break;
				
			case "supercontent":
				$builder = new XmcaContent();
				$builder->setParent($this, $name, array("supercontent_id" => "id"));
				$builder->setJoinType("LEFT");
				$builder->setOnDelete("NO_ACTION");
				$builder->setOnUpdate("NO_ACTION");
				return $builder;
				break;
				
			case "image":
				$builder = new XmcaImage();
				$builder->setParent($this, $name, array("image_id" => "id"));
				$builder->setJoinType("LEFT");
				$builder->setOnDelete("CASCADE");
				$builder->setOnUpdate("CASCADE");
				return $builder;
				break;
				
			case "download_file":
				$builder = new XmcaFile();
				$builder->setParent($this, $name, array("download_file_id" => "id"));
				$builder->setJoinType("LEFT");
				$builder->setOnDelete("CASCADE");
				$builder->setOnUpdate("CASCADE");
				return $builder;
				break;
				
			case "audio_file":
				$builder = new XmcaFile();
				$builder->setParent($this, $name, array("audio_file_id" => "id"));
				$builder->setJoinType("LEFT");
				$builder->setOnDelete("CASCADE");
				$builder->setOnUpdate("CASCADE");
				return $builder;
				break;
				
			case "video_file":
				$builder = new XmcaFile();
				$builder->setParent($this, $name, array("video_file_id" => "id"));
				$builder->setJoinType("LEFT");
				$builder->setOnDelete("CASCADE");
				$builder->setOnUpdate("CASCADE");
				return $builder;
				break;
				
			case "style":
				$builder = new XmcaContentStyle();
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
				$builder->setParent($this, $name, array("id" => "supercontent_id"));
				$builder->setJoinType("LEFT");
				$builder->setOnDelete("CASCADE");
				$builder->setOnUpdate("CASCADE");
				return $builder;
				break;
				
			case "tags":
				if (!($builder instanceof XmcaContentTag)) {
					throw new InternalErrorException("Invalid object [expected type: XmcaContentTag]");
				}
				$builder->setParent($this, $name, array("id" => "content_id"));
				$builder->setJoinType("LEFT");
				$builder->setOnDelete("CASCADE");
				$builder->setOnUpdate("CASCADE");
				return $builder;
				break;
				
			case "comments":
				if (!($builder instanceof XmcaComment)) {
					throw new InternalErrorException("Invalid object [expected type: XmcaComment]");
				}
				$builder->setParent($this, $name, array("id" => "content_id"));
				$builder->setJoinType("LEFT");
				$builder->setOnDelete("CASCADE");
				$builder->setOnUpdate("CASCADE");
				$builder->setFilter(new \system\model\FilterClause($builder->supercomment_id, "IS_NULL"));
				return $builder;
				break;
				
			case "texts":
				if (!($builder instanceof XmcaContentText)) {
					throw new InternalErrorException("Invalid object [expected type: XmcaContentText]");
				}
				$builder->setParent($this, $name, array("id" => "content_id"));
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