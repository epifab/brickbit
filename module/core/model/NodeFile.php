<?php
namespace module\core\model;

use system\model\RecordsetBuilder;
use system\model\RecordsetInterface;
use system\model\FilterClauseGroup;
use system\model\FilterClause;
use system\model\SortClause;

class NodeFile {
	public static function urn(RecordsetInterface $rs) {
		if (!$rs->node_id) {
			return 'content/file/' . $rs->file_id . '.' . $rs->file->extension;
		} else {
			return 'content/' . $rs->node_id . '/file/' . $rs->node_index . '/' . $rs->virtual_name;
		}
	}
	
	public static function edit_urn(RecordsetInterface $recordset) {
		return 'content/file/' . $recordset->file_id . '/edit';
	}
	
	public static function delete_urn(RecordsetInterface $recordset) {
		return 'content/file/' . $recordset->file_id . '/delete';
	}
	
	public static function images(RecordsetInterface $rs) {
		$imgVersions = \array_keys(\system\Cache::imageVersionMakers());
		$versions = array();
		foreach ($imgVersions as $version) {
			$versions[$version] = 'content/' . $rs->node_id . '/img-' . $version . '/' . $rs->node_index . '/' . $rs->virtual_name;
		}
	}
}
?>