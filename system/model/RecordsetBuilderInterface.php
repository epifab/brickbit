<?php
namespace system\model;

interface RecordsetBuilderInterface {
	public function getAbsolutePath();
	public function getTableName();
	public function setVirtual($selectExpression);
	public function isVirtual();
	public function getSelectExpression();
	public function getTableAlias();
	public function replaceAliasPrefix($prefix, $nchars=0);
	
	// chiavi
	public function getKeys();
	public function getKey($name);
	public function getPrimaryKey();
	public function isAutoIncrement();
	
	// relazioni con altri builder
	public function setParent(RecordsetBuilderInterface $builder, $relationName, $clauses);
	public function getParentBuilder();
	public function getClauses();
	public function getRelationName();
	public function setJoinType($joinType);
	public function getJoinType();
	public function setOnDelete($onDelete);
	public function getOnDelete();
	public function setOnUpdate($onUpdate);
	public function getOnUpdate();
	
	// generazione di oggetti recordset
	public function newRecordset($data=null);

	// aggiunta di elementi al builder
	public function using();
	
	public function useAllKeys();
	public function useAllKeysRecursive();
	public function usePrimaryKey();
	public function usePrimaryKeyRecursive();
	
	public function setHasManyRelationBuilder($relationPath, RecordsetBuilderInterface $builder);

	// ricerca di elementi
	public function getHasManyRelationBuilderList();
	public function getHasOneRelationBuilderList();
	public function getMetaTypeList();
	
	public function searchRelationBuilder($path);
	public function searchMetaType($path);

	public function issetProperty($path);
	
	// clausole di selezione
	public function setSort($sortClauses);
	public function setFilter($filterClauses);
	public function setLimit($limitClause);
	
	public function getSort();
	public function getFilter();
	public function getLimit();
	
	// funzioni per la selezione
	public function selectQuery(&$q1, &$q2);

	public function select();
	public function selectFirst();
	public function selectBy($metaType, $value);
	public function selectFirstBy($metaType, $value);
	
	/**
	 * Conta il numero totale dei records in base alle clausole impostate
	 */
	public function countRecords($ignoreRelations=false);
	/**
	 * Conta il numero totale delle pagine tenendo conto delle clausole filter
	 */
	public function countPages($pageSize);

	// gestione del record mode
	public function isRecordModed();
	public function isRecordModeLogged();
	public function getRecordModeKeyName();
}
?>