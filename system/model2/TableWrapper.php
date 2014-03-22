<?php
namespace system\model2;

// Useful regexp ;)
//public function ([^)]+)\) ?\{\r\n
//public function $1) {\r\n    return \return $this->table->$1);
//
//public function ([^\(]+)(\([^ ][^{]+\{)\r?\n    
//public function $1$2\n    return \\call_user_func_array(array(\$this->table, '$1'), \\func_get_args());\n    //

abstract class TableWrapper implements \system\model2\TableInterface {
  private $table;
  
  protected function __construct($tableName) {
    $this->table = Table::loadTable($tableName);
  }

  public function addFilters() {
    return \call_user_func_array(array($this->table, 'addFilters'), \func_get_args());
    //return $this->table->addFilters();
  }

  public function addSorts() {
    return \call_user_func_array(array($this->table, 'addSorts'), \func_get_args());
    //return $this->table->addSorts();    
  }

  public function countPages($pageSize) {
    return \call_user_func_array(array($this->table, 'countPages'), \func_get_args());
    //return $this->table->countPages($pageSize);    
  }

  public function countResults() {
    return \call_user_func_array(array($this->table, 'countResults'), \func_get_args());
    //return $this->table->countResults();    
  }

  public function filter($path, $value, $eq = '=') {
    return \call_user_func_array(array($this->table, 'filter'), \func_get_args());
    //return $this->table->filter($path, $value, $eq = '=');    
  }

  public function filterCustom($query, array $args = array()) {
    return \call_user_func_array(array($this->table, 'filterCustom'), \func_get_args());
    //return $this->table->filterCustom($query, $args);
  }

  public function filterGroup($type = 'AND') {
    return \call_user_func_array(array($this->table, 'filterGroup'), \func_get_args());
    //return $this->table->filterGroup($type = 'AND');    
  }

  public function getAlias() {
    return \call_user_func_array(array($this->table, 'getAlias'), \func_get_args());
    //return $this->table->getAlias();    
  }

  public function getAutoIncrementField() {
    return \call_user_func_array(array($this->table, 'getAutoIncrementField'), \func_get_args());
    //return $this->table->getAutoIncrementField();    
  }

  public function getField ($path) {
    return \call_user_func_array(array($this->table, 'getField '), \func_get_args());
    //return $this->table->getField($path);    
  }

  public function getFields () {
    return \call_user_func_array(array($this->table, 'getFields '), \func_get_args());
    //return $this->table->getFields();    
  }

  public function getFilter() {
    return \call_user_func_array(array($this->table, 'getFilter'), \func_get_args());
    //return $this->table->getFilter();    
  }

  public function getHasManyRelation($path) {
    return \call_user_func_array(array($this->table, 'getHasManyRelation'), \func_get_args());
    //return $this->table->getHasManyRelation($path);    
  }

  public function getHasManyRelations() {
    return \call_user_func_array(array($this->table, 'getHasManyRelations'), \func_get_args());
    //return $this->table->getHasManyRelations();    
  }

  public function getHasOneRelation($path) {
    return \call_user_func_array(array($this->table, 'getHasOneRelation'), \func_get_args());
    //return $this->table->getHasOneRelation($path);    
  }

  public function getHasOneRelations() {
    return \call_user_func_array(array($this->table, 'getHasOneRelations'), \func_get_args());
    //return $this->table->getHasOneRelations();    
  }

  public function getInfo() {
    return \call_user_func_array(array($this->table, 'getInfo'), \func_get_args());
    //return $this->table->getInfo();    
  }

  public function getKey($path) {
    return \call_user_func_array(array($this->table, 'getKey'), \func_get_args());
    //return $this->table->getKey($path);    
  }

  public function getKeys() {
    return \call_user_func_array(array($this->table, 'getKeys'), \func_get_args());
    //return $this->table->getKeys();    
  }

  public function getLimit() {
    return \call_user_func_array(array($this->table, 'getLimit'), \func_get_args());
    //return $this->table->getLimit();    
  }

  public function getName() {
    return \call_user_func_array(array($this->table, 'getName'), \func_get_args());
    //return $this->table->getName();    
  }

  public function getPath() {
    return \call_user_func_array(array($this->table, 'getPath'), \func_get_args());
    //return $this->table->getPath();    
  }

  public function getPrimaryKey() {
    return \call_user_func_array(array($this->table, 'getPrimaryKey'), \func_get_args());
    //return $this->table->getPrimaryKey();    
  }

  public function getProperty($path) {
    return \call_user_func_array(array($this->table, 'getProperty'), \func_get_args());
    //return $this->table->getProperty($path);    
  }

  public function getRelation($path) {
    return \call_user_func_array(array($this->table, 'getRelation'), \func_get_args());
    //return $this->table->getRelation($path);    
  }

  public function getRelations() {
    return \call_user_func_array(array($this->table, 'getRelations'), \func_get_args());
    //return $this->table->getRelations();    
  }

  public function getSort() {
    return \call_user_func_array(array($this->table, 'getSort'), \func_get_args());
    //return $this->table->getSort();    
  }

  public function getTableName() {
    return \call_user_func_array(array($this->table, 'getTableName'), \func_get_args());
    //return $this->table->getTableName();    
  }

  public function getVirtual($path) {
    return \call_user_func_array(array($this->table, 'getVirtual'), \func_get_args());
    //return $this->table->getVirtual($path);    
  }

  public function getVirtuals() {
    return \call_user_func_array(array($this->table, 'getVirtuals'), \func_get_args());
    //return $this->table->getVirtuals();    
  }

  public function import() {
    return \call_user_func_array(array($this->table, 'import'), \func_get_args());
    //return $this->table->import();    
  }

  public function importField($path) {
    return \call_user_func_array(array($this->table, 'importField'), \func_get_args());
    //return $this->table->importField($path);    
  }

  public function importKey($path) {
    return \call_user_func_array(array($this->table, 'importKey'), \func_get_args());
    //return $this->table->importKey($path);    
  }

  public function importProperty($path) {
    return \call_user_func_array(array($this->table, 'importProperty'), \func_get_args());
    //return $this->table->importProperty($path);    
  }

  public function importRelation($path) {
    return \call_user_func_array(array($this->table, 'importRelation'), \func_get_args());
    //return $this->table->importRelation($path);    
  }

  public function importVirtual($path) {
    return \call_user_func_array(array($this->table, 'importVirtual'), \func_get_args());
    //return $this->table->importVirtual($path);    
  }
  
  public function initQuery(&$q1, &$q2) {
    return $this->table->initQuery($q1, $q2);
  }

  public function isAutoImport() {
    return \call_user_func_array(array($this->table, 'isAutoImport'), \func_get_args());
    //return $this->table->isAutoImport();    
  }

  public function isAutoIncrement() {
    return \call_user_func_array(array($this->table, 'isAutoIncrement'), \func_get_args());
    //return $this->table->isAutoIncrement();    
  }

  public function newRecordset($data = null) {
    return \call_user_func_array(array($this->table, 'newRecordset'), \func_get_args());
    //return $this->table->newRecordset($data = null);    
  }

  public function resetFilter() {
    return \call_user_func_array(array($this->table, 'resetFilter'), \func_get_args());
    //return $this->table->resetFilter();    
  }

  public function resetLimit() {
    return \call_user_func_array(array($this->table, 'resetLimit'), \func_get_args());
    //return $this->table->resetLimit();    
  }

  public function resetSort() {
    return \call_user_func_array(array($this->table, 'resetSort'), \func_get_args());
    //return $this->table->resetSort();    
  }

  public function select() {
    return \call_user_func_array(array($this->table, 'select'), \func_get_args());
    //return $this->table->select();    
  }

  public function selectFirst() {
    return \call_user_func_array(array($this->table, 'selectFirst'), \func_get_args());
    //return $this->table->selectFirst();    
  }

  public function selectQuery() {
    return \call_user_func_array(array($this->table, 'selectQuery'), \func_get_args());
    //return $this->table->selectQuery();    
  }

  public function setAutoImport($autoImport) {
    return \call_user_func_array(array($this->table, 'setAutoImport'), \func_get_args());
    //return $this->table->setAutoImport($autoImport);    
  }

  public function setLimit(\system\model2\clauses\LimitClause $limit) {
    return \call_user_func_array(array($this->table, 'setLimit'), \func_get_args());
    //return $this->table->setLimit($limit);    
  }

  public function setPage($pageSize, $page = 0) {
    return \call_user_func_array(array($this->table, 'setPage'), \func_get_args());
    //return $this->table->setPage($pageSize, $page = 0);    
  }

  public function sort($path, $eq = 'ASC') {
    return \call_user_func_array(array($this->table, 'sort'), \func_get_args());
    //return $this->table->sort($path, $eq = 'ASC');    
  }

  public function limit($limit, $offset = 0) {
    return \call_user_func_array(array($this->table, 'limit'), \func_get_args());
    //return $this->table->limit($limit, $offset = 0);    
  }

  public function pageLimits($pageSize, $page = 0) {
    return \call_user_func_array(array($this->table, 'pageLimits'), \func_get_args());
    //return $this->table->pageLimits($pageSize, $page = 0);    
  }
  
  public function getSelectKey() {
    return \call_user_func_array(array($this->table, 'getSelectKey'), \func_get_args());
  }
  
  public function setSelectKey(FieldInterface $field) {
    return \call_user_func_array(array($this->table, 'setSelectKey'), \func_get_args());
  }
}
