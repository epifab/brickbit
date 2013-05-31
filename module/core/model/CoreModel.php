<?php
namespace module\core\model;

use system\model\Recordset;
use system\model\RecordsetBuilder;
use system\model\FilterClause;
use system\model\FilterClauseGroup;
use system\model\SortClause;

class CoreModel {
	public static function metaTypesMap() {
		return array(
			'html' => '\module\core\model\MetaHTML',
			'plaintext' => '\system\metatypes\MetaString'
		);
	}
	
	public static function onDelete(\system\model\RecordsetInterface $rs) {
		$table = $rs->getBuilder()->getTableInfo();
		foreach ($table['relations'] as $relationName => $relation) {
			if (\cb\array_item('onDelete', $relation) == 'CASCADE') {
				$rsb = new \system\model\RecordsetBuilder($relation['table']);
				$rsb->using('*');
				$filter = null;
				foreach ($relation['clauses'] as $clause) {
					$parentField = \key($clause);
					$childField = \current($clause);
					$f = new FilterClause($rsb->{$childField}, '=', $rs->getProg($parentField));
					\is_null($filter) ? $filter = new \system\model\FilterClauseGroup($f) : $filter->addClauses('AND', $f);
				}
				if (\is_null($filter)) {
					// This should never happen
					continue;
				}
				$rsb->addFilter($filter);
				$rs2 = $rsb->select();
				foreach ($rs2 as $r2) {
					\system\Main::raiseModelEvent('onDelete', $r2);
				}
				$q = "DELETE FROM " . $rsb->getTableName() . " WHERE ";
				$first = true;
				foreach ($relation['clauses'] as $clause) {
					$parentField = \key($clause);
					$childField = \current($clause);
					$first ? $first = false : $q .= " AND ";
					$q .= $childField . " = " . $rs->getDb($parentField);
				}
				$da = \system\model\DataLayerCore::getInstance();
				$da->executeQuery($q, __FILE__, __LINE__);
				$rs->unsetRelation($relationName);
			}
		}
	}
}
?>