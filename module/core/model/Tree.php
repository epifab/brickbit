<?php
namespace module\core\model;

use system\model\RecordsetBuilder;
use system\model\RecordsetInterface;
use system\model\FilterClauseGroup;
use system\model\FilterClause;
use system\model\SortClause;

class Tree {
	public static function ancestorsFilter(RecordsetInterface $recordset, RecordsetBuilder $ancestors) {
		$ancestors->setFilter(new FilterClauseGroup(
			new FilterClause($ancestors->rdel, '>', $recordset->rdel),
			'AND',
			new FilterClause($ancestors->ldel, '<', $recordset->ldel)
		));
		$ancestors->setSort(new SortClause(
			$ancestors->ldel, 'DESC'
		));
	}
	
	public static function descendantsFilter(RecordsetInterface $recordset, RecordsetBuilder $descendants) {
		$descendants->setFilter(new FilterClauseGroup(
			new FilterClause($descendants->rdel, '<', $recordset->rdel),
			'AND',
			new FilterClause($descendants->ldel, '>', $recordset->ldel)
		));
		$descendants->setSort(new SortClause(
			$descendants->rdel, 'DESC'
		));
	}

}
?>