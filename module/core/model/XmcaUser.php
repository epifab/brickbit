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
 * Class for managing table: xmca_user
 * XMCA PHP Generator 0.1 - Auto generated code
 * @author episoft
 */
class XmcaUser extends RecordsetBuilder implements RecordsetBuilderInterface {
	private static $superusers;
	
	public function getTableName() {
		return "xmca_user";
	}
	
	public function isAutoIncrement() {
		return true;
	}
	
	protected function loadKeys() {
		return array (
			"primary_key" => array("id"),			
			"email_key" => array("email")			
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
				
			// Email
			case "email":
				$metaType = new MetaString($name, $this);
				$metaType->setDefaultValue("");
				$metaType->addValidate(function($x) {
					Validation::checkEmail($x);
					Validation::checkSize($x, null, 80);
					Validation::checkNotNullable($x);
				});
				break;
				
			// Password
			case "password":
				$metaType = new MetaString($name, $this);
				$metaType->setDefaultValue("");
				$metaType->addValidate(function($x) {
					Validation::checkSize($x, null, 32);
					Validation::checkNotNullable($x);
				});
				break;
				
			// Full Name
			case "full_name":
				$metaType = new MetaString($name, $this);
				$metaType->setDefaultValue("");
				$metaType->addValidate(function($x) {
					Validation::checkSize($x, null, 100);
					Validation::checkNotEmpty($x);
				});
				break;
				
			// Last Login
			case "last_login":
				$metaType = new MetaDateTime($name, $this);
				$metaType->setDefaultValue(\time());
				$metaType->addValidate(function($x) {
				});
				break;
				
			// Ins Date Time
			case "ins_date_time":
				$metaType = new MetaDateTime($name, $this);
				$metaType->setDefaultValue(\time());
				$metaType->addValidate(function($x) {
					Validation::checkNotNullable($x);
				});
				break;
				
			// Last Upd Date Time
			case "last_upd_date_time":
				$metaType = new MetaDateTime($name, $this);
				$metaType->setDefaultValue(\time());
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
			default:
				break;
		}
	}
	
	protected function loadHasManyRelationBuilder($name, RecordsetBuilderInterface $builder) {
		switch ($name) {
			case "groups":
				if (!($builder instanceof XmcaUserGroup)) {
					throw new InternalErrorException("Invalid object [expected type: XmcaUserGroup]");
				}
				$builder->setParent($this, $name, array("id" => "user_id"));
				$builder->setJoinType("LEFT");
				$builder->setOnDelete("CASCADE");
				$builder->setOnUpdate("CASCADE");
				return $builder;
				break;
				
			default:
				break;
		}
	}
	
	public static function updateLastLogin($userId) {
		$query =
			"UPDATE xmca_user SET"
			. " last_login = " . MetaDateTime::stdProg2Db(\time())
			. " WHERE id = " . MetaInteger::stdProg2Db($userId);
		\system\model\DataLayerCore::getInstance()->executeUpdate($query, __FILE__, __LINE__);
	}
	
	public static function isSuperuser($userId) {
	
		if (empty ($userId)) {
			return false;
		}
		
		// Carico gli id di tutti i superutenti (se non l'ho fatto precedentemente)
		if (\is_null(self::$superusers)) {
			$query = 
				"SELECT DISTINCT user_id"
				. " FROM xmca_user_group xug"
				. " LEFT JOIN xmca_user xu ON xu.id = xug.user_id"
				. " WHERE xug.group_id = " . XmcaGroup::ADMINS_GROUP_ID;
			
			$result = \system\model\DataLayerCore::getInstance()->executeQuery($query, __FILE__, __LINE__);
			
			self::$superusers = array();
			
			while ($arr = \system\model\DataLayerCore::getInstance()->sqlFetchArray($result)) {
				self::$superusers[] = $arr["user_id"];
			}
			
			\system\model\DataLayerCore::getInstance()->sqlFreeResult($result);
		}
		
		return \in_array($userId, self::$superusers);
	}
	
	public static function getIdByLoginData($cryptedEmail, $cryptedPassword) {
		$query =
			"SELECT"
			. " id"
			. " FROM xmca_user"
			. " WHERE MD5(LOWER(email)) = " . MetaString::stdProg2Db($cryptedEmail) . " AND password = " . MetaString::stdProg2Db($cryptedPassword);
		
		return \system\model\DataLayerCore::getInstance()->executeScalar($query, __FILE__, __LINE__);
	}
}
?>