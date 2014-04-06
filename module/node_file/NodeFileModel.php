<?php
namespace module\node_file;

use system\model2\RecordsetInterface;

class NodeFileModel {
  public static function onDelete(RecordsetInterface $recordset) {
    switch ($recordset->getTable()->getName()) {
      case 'file':
      case 'file_version':
        unlink($recordset->path);
        break;
    }
  }
}