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
 * Class for managing table: xmca_content_style
 * XMCA PHP Generator 0.1 - Auto generated code
 * @author episoft
 */
class XmcaContentStyle extends RecordsetBuilder {
	public function getTableName() {
		return "xmca_content_style";
	}
	
	public function isAutoIncrement() {
		return false;
	}
	
	protected function loadKeys() {
		return array (
			"primary" => array("code")
		);
	}

	protected function loadMetaType($name) {
		$metaType = null;
	
		switch ($name) {
			case "code":
				$metaType = new MetaString($name, $this);
				$metaType->addValidate(function($x) {
					\system\Validation::checkSize($x, 3, 15);
					\system\Validation::checkAlNum($x);
				});
				break;
			
			case "description":
				$metaType = new MetaString($name, $this);
				$metaType->addValidate(function($x) {
					\system\Validation::checkSize($x, 0, 100);
				});
				break;
			
			case "content_template":
				$metaType = new MetaString($name, $this);
				$metaType->addValidate(function($x) {
					\system\Validation::checkSize($x, 0, 50);
					\system\Validation::checkAlNum($x);
				});
				break;
				
			case "preview_template":
				$metaType = new MetaString($name, $this);
				$metaType->addValidate(function($x) {
					\system\Validation::checkSize($x, 0, 50);
					\system\Validation::checkAlNum($x);
				});
				break;
		}

		return $metaType;
	}
	
	protected function loadHasOneRelationBuilder($name) {
		switch ($name) {
			
			default:
				break;
		}
	}
	
	protected function loadHasManyRelationBuilder($name, RecordsetBuilderInterface $builder) {
		switch ($name) {
			case "contents":
				if (!($builder instanceof XmcaContent)) {
					throw new InternalErrorException("Invalid object [expected type: XmcaContent]");
				}
				$builder->setParent($this, $name, array("code" => "style_code"));
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