<?php
namespace module\node;

use system\model2\RecordsetInterface;

class NodeModel {
  public static function onDelete(RecordsetInterface $recordset) {
    if ($recordset->getTable()->getName() == 'file') {
    }
  }
}