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
 * Class for managing table: xmca_content_text
 * XMCA PHP Generator 0.1 - Auto generated code
 * @author episoft
 */
class XmcaContentText extends RecordsetBuilder {
	public function getTableName() {
		return "xmca_content_text";
	}
	
	public function isAutoIncrement() {
		return false;
	}
	
	protected function loadKeys() {
		return array (
			"primary" => array("lang_id", "content_id")
		);
	}
	
	protected function loadMetaType($name) {
		$metaType = null;
	
		switch ($name) {
			case "lang_id":
				$metaType = new MetaString($name, $this);
				$metaType->addValidate(function($x) {
					\system\Validation::checkSize($x, 2, 2);
				});
				break;
				
			case "content_id":
				$metaType = new MetaInteger($name, $this);
				$metaType->addValidate(function($x) {
				});
				break;
			
			case "title":
				$metaType = new MetaString($name, $this);
				$metaType->addValidate(function($x) {
					Validation::checkSize($x, null, 100);
				});
				break;
			
			case "subtitle":
				$metaType = new MetaString($name, $this);
				$metaType->addValidate(function($x) {
					Validation::checkSize($x, null, 200);
				});
				break;
				
			case "body":
				$metaType = new MetaString($name, $this);
				$metaType->addValidate(function($x) {
				});
				break;
				
			case "preview":
				$metaType = new MetaString($name, $this);
				$metaType->addValidate(function($x) {
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

			default:
				return parent::loadHasOneRelationBuilder($name);
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