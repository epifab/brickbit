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
 * Class for managing table: xmca_dir
 * XMCA PHP Generator 0.1 - Auto generated code
 * @author episoft
 */
class XmcaDir extends RecordsetBuilder implements RecordsetBuilderInterface {
	const IMAGE_DIR_ID = 1;
	const IMAGE_DIR_PATH = "contents_media/image/";
	const DOWNLOAD_DIR_ID = 2;
	const DOWNLOAD_DIR_PATH = "contents_media/download/";
	const AUDIO_DIR_ID = 2;
	const AUDIO_DIR_PATH = "contents_media/audio/";
	const VIDEO_DIR_ID = 2;
	const VIDEO_DIR_PATH = "contents_media/video/";
	
	public function getTableName() {
		return "xmca_dir";
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
			case "id":
				$metaType = new MetaInteger($name, $this);
				$metaType->addValidate(function($x) {
					throw new ValidationException("Campo AUTO_INCREMENT.");
				});
				break;
				
			case "path":
				$metaType = new MetaString($name, $this);
				$metaType->addValidate(function($x) {
					Validation::checkNotEmpty($x);
					Validation::checkSize($x, 1, 80);
				});
				break;
			
			default:
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
			case "files":
				if (!($builder instanceof XmcaFile)) {
					throw new InternalErrorException("Invalid object [expected type: XmcaFile]");
				}
				$builder->setParent($this, $name, array("id" => "dir_id"));
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