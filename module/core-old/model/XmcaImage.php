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
 * Class for managing table: xmca_image
 * XMCA PHP Generator 0.1 - Auto generated code
 * @author episoft
 */
class XmcaImage extends RecordsetBuilder implements RecordsetBuilderInterface {
	public function getTableName() {
		return "xmca_image";
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
				
			// Width1
			case "width1":
				$metaType = new MetaInteger($name, $this);
				$metaType->setDefaultValue(0);
				$metaType->addValidate(function($x) {
					Validation::checkNotNullable($x);
				});
				break;
				
			// Height1
			case "height1":
				$metaType = new MetaInteger($name, $this);
				$metaType->setDefaultValue(0);
				$metaType->addValidate(function($x) {
					Validation::checkNotNullable($x);
				});
				break;
				
			// File1 Id
			case "file1_id":
				$metaType = new MetaInteger($name, $this);
				$metaType->setDefaultValue(0);
				$metaType->addValidate(function($x) {
					Validation::checkNotNullable($x);
				});
				break;
				
			// Width2
			case "width2":
				$metaType = new MetaInteger($name, $this);
				$metaType->setDefaultValue(0);
				$metaType->addValidate(function($x) {
					Validation::checkNotNullable($x);
				});
				break;
				
			// Height2
			case "height2":
				$metaType = new MetaInteger($name, $this);
				$metaType->setDefaultValue(0);
				$metaType->addValidate(function($x) {
					Validation::checkNotNullable($x);
				});
				break;
				
			// File2 Id
			case "file2_id":
				$metaType = new MetaInteger($name, $this);
				$metaType->setDefaultValue(0);
				$metaType->addValidate(function($x) {
					Validation::checkNotNullable($x);
				});
				break;
				
			// Width3
			case "width3":
				$metaType = new MetaInteger($name, $this);
				$metaType->setDefaultValue(null);
				$metaType->addValidate(function($x) {
				});
				break;
				
			// Height3
			case "height3":
				$metaType = new MetaInteger($name, $this);
				$metaType->setDefaultValue(null);
				$metaType->addValidate(function($x) {
				});
				break;
				
			// File3 Id
			case "file3_id":
				$metaType = new MetaInteger($name, $this);
				$metaType->setDefaultValue(null);
				$metaType->addValidate(function($x) {
				});
				break;
				
			// Width4
			case "width4":
				$metaType = new MetaInteger($name, $this);
				$metaType->setDefaultValue(null);
				$metaType->addValidate(function($x) {
				});
				break;
				
			// Height4
			case "height4":
				$metaType = new MetaInteger($name, $this);
				$metaType->setDefaultValue(null);
				$metaType->addValidate(function($x) {
				});
				break;
				
			// File4 Id
			case "file4_id":
				$metaType = new MetaInteger($name, $this);
				$metaType->setDefaultValue(null);
				$metaType->addValidate(function($x) {
				});
				break;
				
			// Path File1
			case "path_file1":
				$metaType = new MetaString($name, $this);
				$metaType->setVirtual(parent::evalSelectExpression("CONCAT(@[file1.dir.path],@[file1.name])"));
				break;
				
			// Path File2
			case "path_file2":
				$metaType = new MetaString($name, $this);
				$metaType->setVirtual(parent::evalSelectExpression("CONCAT(@[file2.dir.path],@[file2.name])"));
				break;
				
			// Path File3
			case "path_file3":
				$metaType = new MetaString($name, $this);
				$metaType->setVirtual(parent::evalSelectExpression("CONCAT(@[file3.dir.path],@[file3.name])"));
				break;
				
			// Path File4
			case "path_file4":
				$metaType = new MetaString($name, $this);
				$metaType->setVirtual(parent::evalSelectExpression("CONCAT(@[file4.dir.path],@[file4.name])"));
				break;
		}

		return $metaType;
	}
	
	protected function loadHasOneRelationBuilder($name) {
		switch ($name) {
			case "file1":
				$builder = new XmcaFile();
				$builder->setParent($this, $name, array("file1_id" => "id"));
				$builder->setJoinType("LEFT");
				$builder->setOnDelete("CASCADE");
				$builder->setOnUpdate("CASCADE");
				return $builder;
				break;
				
			case "file2":
				$builder = new XmcaFile();
				$builder->setParent($this, $name, array("file2_id" => "id"));
				$builder->setJoinType("LEFT");
				$builder->setOnDelete("CASCADE");
				$builder->setOnUpdate("CASCADE");
				return $builder;
				break;
				
			case "file3":
				$builder = new XmcaFile();
				$builder->setParent($this, $name, array("file3_id" => "id"));
				$builder->setJoinType("LEFT");
				$builder->setOnDelete("CASCADE");
				$builder->setOnUpdate("CASCADE");
				return $builder;
				break;
				
			case "file4":
				$builder = new XmcaFile();
				$builder->setParent($this, $name, array("file4_id" => "id"));
				$builder->setJoinType("LEFT");
				$builder->setOnDelete("CASCADE");
				$builder->setOnUpdate("CASCADE");
				return $builder;
				break;
		}
	}
	
	protected function loadHasManyRelationBuilder($name, RecordsetBuilderInterface $builder) {
		switch ($name) {
			default:
				break;
		}
	}
	
	public static function initializeRs($inputName) {
		$imgBuilder = new self();
		$imgBuilder->using(
			"id",
			// img 1
			"width1", "height1", "path_file1",
			"file1.name", "file1.dir_id",
			// img 2
			"width2", "height2", "path_file2",
			"file2.name", "file2.dir_id",
			// img 3
			"width3", "height3", "path_file3",
			"file3.name", "file3.dir_id",
			// img 4
			"width4", "height4", "path_file4",
			"file4.name", "file4.dir_id"
		);
		
		$img = $imgBuilder->newRecordset();

		$img->file1_id = 0;
		$img->file2_id = 0;
		$img->file3_id = 0;
		$img->file4_id = 0;
		$img->create();
		
		$ext = \system\File::getExtension($_FILES[$inputName]["name"]);

		$img->file1->dir_id = \module\core\model\XmcaDir::IMAGE_DIR_ID;
		$img->file1->name = $img->id . "-01." . $ext;
		$img->path_file1 = \module\core\model\XmcaDir::IMAGE_DIR_PATH . $img->file1->name;
		
		$img->file2->dir_id = \module\core\model\XmcaDir::IMAGE_DIR_ID;
		$img->file2->name = $img->id . "-02." . $ext;
		$img->path_file2 = \module\core\model\XmcaDir::IMAGE_DIR_PATH . $img->file2->name;
		
		$img->file3->dir_id = \module\core\model\XmcaDir::IMAGE_DIR_ID;
		$img->file3->name = $img->id . "-03." . $ext;
		$img->path_file3 = \module\core\model\XmcaDir::IMAGE_DIR_PATH . $img->file3->name;
		
		$img->file4->dir_id = \module\core\model\XmcaDir::IMAGE_DIR_ID;
		$img->file4->name = $img->id . "-04." . $ext;
		$img->path_file4 = \module\core\model\XmcaDir::IMAGE_DIR_PATH . $img->file4->name;

		return $img;
	}
	
	public static function finalizeRs(\system\model\Recordset $img) {
		if (\file_exists($img->path_file1)) {
			$img->file1->save();
			$img->file1_id = $img->file1->id;
			list($w, $h) = \getimagesize($img->path_file1);
			$img->width1 = $w;
			$img->height1 = $h;
		}
		if (\file_exists($img->path_file2)) {
			$img->file2->save();
			$img->file2_id = $img->file2->id;
			list($w, $h) = \getimagesize($img->path_file2);
			$img->width2 = $w;
			$img->height2 = $h;
		}
		if (\file_exists($img->path_file3)) {
			$img->file3->save();
			$img->file3_id = $img->file3->id;
			list($w, $h) = \getimagesize($img->path_file3);
			$img->width3 = $w;
			$img->height3 = $h;
		}
		if (\file_exists($img->path_file4)) {
			$img->file4->save();
			$img->file4_id = $img->file4->id;
			list($w, $h) = \getimagesize($img->path_file4);
			$img->width4 = $w;
			$img->height4 = $h;
		}
		$img->update();
	}
}
?>