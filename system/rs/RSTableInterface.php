<?php
namespace system\rs;

interface RSTableInterface extends RSPropertyInterface, \Serializable {
  public function import();
  
  public function importProperty($path);
  public function getProperty($path);

  public function getRelation($path);
  public function getHasOneRelation($path);
  public function getHasManyRelation($path);
  public function getField($path);
  public function getKey($path);
  public function getVirtual($path);
  
  public function getTableName();
  public function getTableInfo();
  
  public function isAutoIncrement();
  public function getAutoIncrementField();
  
  public function newRS();
  
  public function selectQuery();
  
  public function select();
  public function selectFirst();
  
  public function countResults();
  public function countPages($pageSize);

  public function filterCustom($query, $args = array());
  public function filter($path, $value, $eq = '=');
  public function sort($path, $eq = 'ASC');
  
  public function resetFilter();
  public function resetSort();
  
  public function addFilter($filter);
  public function addSort($sort);
  
  public function setLimit($limit, $offset = 0);
  public function setPage($pageSize, $page = 0);
}
