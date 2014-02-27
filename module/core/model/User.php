<?php
namespace module\core\model;

use system\model\RecordsetBuilder;
use system\model\RecordsetInterface;
use system\model\FilterClauseGroup;
use system\model\FilterClause;
use system\model\LimitClause;
use system\model\SortClause;

class User {
  public static function getUrl(\system\model\RecordsetInterface $recordset) {
    return "user/{$recordset->id}";
  }
  
  public static function getEditUrl(\system\model\RecordsetInterface $recordset) {
    return "user/{$recordset->id}/edit";
  }
  
  public static function getDeleteUrl(\system\model\RecordsetInterface $recordset) {
    return "user/{$recordset->id}/delete";
  }
}
