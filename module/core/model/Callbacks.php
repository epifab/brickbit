<?php
namespace module\core\model;

use system\model\Recordset;
use system\model\RecordsetBuilder;
use system\model\FilterClause;
use system\model\FilterClauseGroup;
use system\model\SortClause;

class Callbacks {
	public static function ancestors(Recordset $child, RecordsetBuilder $ancestors) {
		$ancestors->setFilter(new \system\model\FilterClauseGroup(
			new FilterClause($ancestors->rdel, '>', $child->rdel),
			'AND',
			new FilterClause($ancestors->ldel, '<', $child->ldel)
		));
		$ancestors->setSort(new SortClause(
			$ancestors->ldel, 'DESC'
		));
	}
	
	public static function descendants(Recordset $parent, RecordsetBuilder $descendants) {
		$descendants->setFilter(new \system\model\FilterClauseGroup(
			new FilterClause($descendants->rdel, '<', $parent->rdel),
			'AND',
			new FilterClause($descendants->ldel, '>', $parent->ldel)
		));
		$descendants->setSort(new SortClause(
			$descendants->rdel, 'DESC'
		));
	}
}
?>