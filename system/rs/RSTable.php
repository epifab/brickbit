<?php
namespace system\rs;

class RSTable implements RSTableInterface {
	private $tableInfo;
	private $tableName;
	private $tableAlias;
	
	private $importedPaths = array();
	
	/**
	 * @var \system\rs\RSPropertyInterface[]
	 */
	private $properties = array();
	/**
	 * @var \system\rs\RSPropertyInterface[]
	 */
	private $fields = array();
	/**
	 * @var \system\rs\RSPropertyInterface[]
	 */
	private $keys = array();
	/**
	 * @var \system\rs\RSRelationInterface[]
	 */
	private $relations = array();
	/**
	 * @var \system\rs\RSRelationInterface[]
	 */
	private $hasOneRelations = array();
	/**
	 * @var \system\rs\RSRelationInterface[]
	 */
	private $hasManyRelations = array();
	/**
	 * @var \system\rs\RSPropertyInterface[]
	 */
	private $virtuals = array();
	
	
	public function __construct($tableName) {
		$this->tableName = $tableName;
		$this->tableInfo = \system\Main::getTable($tableName);
		$this->tableAlias = self::getUniqueAlias($tableName);
	}
	
	/**
	 * Import a set of properties
	 */
	public function import() {
		foreach (func_get_args() as $arg) {
			if ($arg == '*') {
				foreach ($this->tableInfo['fields'] as $name => $info) {
					$this->importProperty($name);
				}
				foreach ($this->tableInfo['keys'] as $name => $info) {
					$this->importProperty($name);
				}
				foreach ($this->tableInfo['relations'] as $name => $info) {
					$this->importProperty($name);
				}
				foreach ($this->tableInfo['virtuals'] as $name => $info) {
					$this->importProperty($name);
				}
			}
			else {
				$this->importProperty($arg);
			}
		}
	}
	
	/**
	 * Checks whether ther property exists in the table
	 * @param string $table Table name
	 * @param string $path Property path
	 * @return boolean True whether exists
	 */
	public static function propertyExists($table, $path) {
		try {
			$tableInfo = \system\Main::getTable($table);
			$dotPosition = \strpos($path, '.');

			if ($dotPosition === false) {
				return isset($tableInfo['fields'][$path])
					|| isset($tableInfo['keys'][$path])
					|| isset($tableInfo['relations'][$path])
					|| isset($tableInfo['virtuals'][$path]);
			}
			else {
				$relationName = \substr($path, 0, $dotPosition);
				return isset($tableInfo['relations'][$path])
					&& self::propertyExists($tableInfo['relations'][$relationName]['table'], \substr($path, $dotPosition + 1));
			}
		} catch (\system\exceptions\Error $ex) {
			return false;
		}
	}

	private function _importProperty($name) {
		$property = null;
		if (isset($this->tableInfo['fields'][$name])) {
			$property = new RSField($this, $this->tableInfo['fields'][$name]);
			$this->fields[$path] = $property;
		}
		else if (isset($this->tableInfo['keys'][$name])) {
			$property = new RSKey($this, $this->tableInfo['keys'][$name]);
			$this->keys[$path] = $property;
		}
		else if (isset($this->tableInfo['relations'][$name])) {
			$property = new RSRelation($this, $this->tableInfo['relations'][$name]);
			$this->relations[$name] = $property;
			$property->isHasMany()
				? $this->hasManyRelations = $property
				: $this->hasOneRelations = $property;
		}
		else if (isset($this->tableInfo['virtuals'][$name])) {
			$property = new RSVirtual($this, $this->tableInfo['virtuals'][$name]);
			$this->virtuals[$name] = $property;
		}
		else {
			throw new \system\exceptions\DataLayerError('Property <em>@name</em> not found in <em>@table</em>', array('@name' => $name, '@table' => $this->getTableName()));
		}
	}

	/**
	 * Import a property
	 * @param string $path Property path
	 * @return \system\rs\RSPropertyInterface Property
	 * @throws \system\exceptions\DataLayerError
	 */
	public function importProperty($path) {
		if (!isset($this->importedPaths[$path])) {
			$dotPosition = \strpos($path, '.');

			if ($dotPosition === false) {
				if (!isset($this->properties[$path])) {
					$this->_importProperty($path);
				}
				return $this->properties[$path];
			}
			else {
				$relationName = \substr($path, 0, $dotPosition);
				if (!isset($this->properties[$relationName])) {
					$this->_importProperty($relationName);
				}
				if (!isset($this->relations[$relationName])) {
					throw new \system\exceptions\DataLayerError('Relation <em>@name</em> not found in <em>@tanle</em>', array('@name' => $relationName, '@table' => $this->getTableName()));
				}

				$this->importedPaths[$path] = $this->relations[$relationName]->importProperty(\substr($path, $dotPosition + 1));
			}
		}
		return $this->importedPaths[$path];
	}

	/**
	 * Returns the pre-imported property identified by the path argument
	 * @param string $path Property path
	 * @return \system\rs\RSPropertyInterface Property or NULL if not imported
	 */
	public function getProperty($path) {
		$dotPosition = \strpos($path, '.');

		if ($dotPosition === false) {
			return isset($this->properties[$path])
				? $this->properties[$path]
				: null;
		}
		else {
			$relationName = \substr($path, 0, $dotPosition);
			return isset($this->relations[$relationName])
				? $this->relations[$relationName]->getProperty(substr($path, $dotPosition+1))
				: null;
		}
	}
	
	/**
	 * Returns the pre-imported field identified by the path argument
	 * @param string $path Field path
	 * @return \system\rs\RSFieldInterface Field or NULL if not imported
	 */
	public function getField($path) {
		$property = $this->getProperty($path);
		return ($property instanceof \system\rs\RSFieldInterface)
			? $property
			: null;
	}

	/**
	 * Returns the pre-imported key identified by the path argument
	 * @param string $path Key path
	 * @return \system\rs\RSKeyInterface Key or NULL if not imported
	 */
	public function getKey($path) {
		$property = $this->getProperty($path);
		return ($property instanceof \system\rs\RSKeyInterface)
			? $property
			: null;
	}

	/**
	 * Returns the pre-imported relation identified by the path argument
	 * @param string $path Relation path
	 * @return \system\rs\RSRelationInterface Relation or NULL if not imported
	 */
	public function getRelation($path) {
		$property = $this->getProperty($path);
		return ($property instanceof \system\rs\RSRelationInterface)
			? $property
			: null;
	}

	/**
	 * Returns the pre-imported has one relation identified by the path argument
	 * @param string $path Has one relation path
	 * @return \system\rs\RSRelationInterface Relation or NULL if not imported
	 */
	public function getHasOneRelation($path) {
		$property = $this->getRelation($path);
		return !empty($property) && $property->isHasOne()
			? $property
			: null;
	}

	/**
	 * Returns the pre-imported has many relation identified by the path argument
	 * @param string $path Has many relation path
	 * @return \system\rs\RSRelationInterface Relation or NULL if not imported
	 */
	public function getHasManyRelation($path) {
		$property = $this->getRelation($path);
		return !empty($property) && $property->isHasMany()
			? $property
			: null;
	}

	/**
	 * Returns the pre-imported virtual identified by the path argument
	 * @param string $path Vritual path
	 * @return \system\rs\RSVritualInterface Vritual or NULL if not imported
	 */
	public function getVirtual($path) {
		$property = $this->getProperty($path);
		return ($property instanceof \system\rs\RSVirtualInterface) && $property->isHasOne()
			? $property
			: null;
	}



	
	public function addFilter($filter) {
		
	}

	public function addSort($sort) {
		
	}

	public function countPages($pageSize) {
		
	}

	public function countResults() {
		
	}

	public function filter($path, $value, $eq = "=") {
		
	}

	public function filterCustom($query, $args = array()) {
		
	}

	public function getTableName() {
		return $this->tableName;
	}

	public function getTableInfo() {
		return $this->tableInfo;
	}

	public function getAlias() {
		return $this->tableAlias;
	}

	public function getName() {
		return $this->getTableName();
	}

	public function getPath() {
		return $this->getTableName();
	}

	public function getAutoIncrementField() {
		
	}

	public function isAutoIncrement() {
		
	}

	public function newRS() {
		
	}

	public function resetFilter() {
		
	}

	public function resetSort() {
		
	}

	public function select() {
		
	}

	public function selectFirst() {
		
	}

	public function selectQuery() {
		
	}

	public function serialize() {
		
	}

	public function setLimit($limit, $offset = 0) {
		
	}

	public function setPage($pageSize, $page = 0) {
		
	}

	public function sort($path, $eq = "ASC") {
		
	}

	public function unserialize($serialized) {
		
	}

	private static function getUniqueAlias($tableName) {
		static $tableIds = array();
		if (!\array_key_exists($tableName, $tableIds)) {
			$tableIds[$tableName] = 1;
		} else {
			$tableIds[$tableName]++;
		}
		return $tableName . $tableIds[$tableName];
	}
}