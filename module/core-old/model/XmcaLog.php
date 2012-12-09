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
 * Class for managing table: xmca_log
 * XMCA PHP Generator 0.1 - Auto generated code
 * @author episoft
 */
class XmcaLog extends RecordsetBuilder implements RecordsetBuilderInterface {
	private static $logs = "";
	
	public static function saveLog($componentName, $output) {
		$builder = new XmcaLog();
		$builder->using(
			"id",
			"user_id",
			"script_url",
			"date_time_request",
			"ip_address",
			"body",
			"output"
		);
		$rs = $builder->newRecordset();
		$rs->setProg("user_id", \system\Login::getLoggedUserId());
		$rs->setProg("script_url", $componentName);
		$rs->setProg("date_time_request", \time());
		$rs->setProg("body", self::$logs);
		$rs->setProg("output", $output);
		$rs->setProg("ip_address", \system\HTMLHelpers::getIpAddress());
		$rs->create();
		return $rs->getProg("id");
	}
	
	public static function add($log) {
		self::$logs .= $log . "\n";
	}
	
	public static function get() {
		return self::$logs;
	}
	
	public function getTableName() {
		return "xmca_log";
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

			// User Id
			case "user_id":
				$metaType = new MetaInteger($name, $this);
				$metaType->setDefaultValue(null);
				$metaType->addValidate(function($x) {
				});
				break;
			
			// Script url
			case "script_url":
				$metaType = new MetaString($name, $this);
				$metaType->setDefaultValue("");
				$metaType->addValidate(function($x) {
					Validation::checkNotNullable($x);
				});
				break;
			
			// Date Time Request
			case "date_time_request":
				$metaType = new MetaDateTime($name, $this);
				$metaType->setDefaultValue(\time());
				$metaType->addValidate(function($x) {
					Validation::checkNotNullable($x);
				});
				break;
			
			// Ip Address
			case "ip_address":
				$metaType = new MetaString($name, $this);
				$metaType->setDefaultValue("");
				$metaType->addValidate(function($x) {
					Validation::checkNotNullable($x);
				});
				break;
			
			// Body
			case "body":
				$metaType = new MetaString($name, $this);
				$metaType->setDefaultValue("");
				$metaType->addValidate(function($x) {
					Validation::checkNotNullable($x);
				});
				break;
			
			// Output
			case "output":
				$metaType = new MetaString($name, $this);
				$metaType->setDefaultValue("");
				$metaType->addValidate(function($x) {
					Validation::checkNotNullable($x);
				});
				break;
				
			default:
				break;
		}

		return $metaType;
	}
	
	protected function loadHasOneRelationBuilder($name) {
		switch ($name) {
			case "component":
				$builder = new XmcaComponent();
				$builder->setParent($this, $name, array("component_id" => "id"));
				$builder->setJoinType("LEFT");
				$builder->setOnDelete("NO_ACTION");
				$builder->setOnUpdate("NO_ACTION");
				return $builder;
				break;
			
			case "user":
				$builder = new XmcaUser();
				$builder->setParent($this, $name, array("user_id" => "id"));
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