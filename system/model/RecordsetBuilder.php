<?php
namespace system\model;

class RecordsetBuilder {
	private static function getUniqueAlias($tableName) {
		static $tableIds = array();
		if (!\array_key_exists($tableName, $tableIds)) {
			$tableIds[$tableName] = 1;
		} else {
			$tableIds[$tableName]++;
		}
		return $tableName . $tableIds[$tableName];
	}
	
	private $paths = array();
	
	/**
	 * Path assoluto della relazione
	 * @var string
	 */
	private $absolutePath = "";

	
	/**
	 * Nome della tabella
	 * @var string
	 */
	private $tableName;
	/**
	 * Info tabella (@see module.yml)
	 * @var array
	 */
	private $tableInfo;
	/**
	 * Nome classe del Recordset
	 * @var string
	 */
	private $recordsetClass = null;
	/**
	 * Builder della relazione madre 
	 * (null se si tratta del nodo radice)
	 * @var RecordsetBuilder
	 */
	private $parentBuilder = null;
	/**
	 * Nome della relazione madre
	 * (null se si tratta del nodo radice)
	 * @var string
	 */
	private $relationName = null;
	/**
	 * Lista di associazioni nome campo relazione madre => nome campo relazione figlia 
	 * (null se si tratta del nodo radice)
	 * @var string[]
	 */
	private $clauses = null;
	/**
	 * Handler to add filters to the recordset builder
	 * @var callable
	 */
	private $filterHandle = null;
	/**
	 * Tipo di relazione
	 * @var string
	 */
	private $relationType = "1-N";
	/**
	 * Azione da intraprendere nel caso di cancellazione di un record del recordset builder padre
	 * @var string
	 */
	private $onDelete = "NO_ACTION";
	/**
	 * Azione da intraprendere nel caso di cancellazione di un record del recordset builder padre
	 * @var string
	 */
	private $onUpdate = "NO_ACTION";
	/**
	 * Tipo di JOIN per la relazione (utilizzato soltanto in caso non si tratti del nodo radice)
	 * @var string
	 */
	private $joinType = "INNER";

	/**
	 * Alias della tabella associata al nodo
	 * @var string
	 */
	private $tableAlias = "ciderbit";
	
	/**
	 * Espressione per la selezione della tabella
	 * @var string
	 */
	private $selectExpression = null;
	
	/**
	 * Any imported property
	 * @var string[]
	 */
	private $properties = array();
	
	/**
	 * Lista di associazioni nome campo => Field campo appartenenti al nodo
	 * @var Field[]
	 */
	private $fieldList = array();
	
	/**
	 * Lista di associazioni (di tipo *-1) nome relazione => RecordsetBuilder relazione appartenenti al nodo
	 * @var RecordsetBuilder[]
	 */
	private $hasOneRelationBuilderList = array();
	/**
	 * Lista di associazioni (di tipo 1-N) nome relazione => RecordsetBuilder relazione appartenenti al nodo
	 * @var RecordsetBuilder[]
	 */
	private $hasManyRelationBuilderList = array();
	
	/**
	 * Lista di chiavi (espresse come liste di Field)
	 * @var Field[][]
	 */
	private $keyList = array();
	
	private $primaryKey = null;
	/**
	 * Lista di associazioni nome relazione => RecordsetBuilder relazione appartenenti al nodo
	 * @var RecordsetBuilder[]
	 */
	private $relationBuilderList = array();
	/**
	 * Oggetto FilterClause o FilterClauseGroup contenente le clausole per filtrare i risultati
	 * @var SelectClause
	 */
	private $filterClauses = null;
	/**
	 * Oggetto SortClause o SortClauseGroup contenente le clausole per ordinare i risultati
	 * @var SelectClause
	 */
	private $sortClauses = null;
	/**
	 * Oggetto LimitClause contenente la clausola per limitare (o paginare) i risultati
	 * @var SelectClause
	 */
	private $limitClause = null;
	
	private $selectKey = null;
	
	public function getTableInfo() {
		return $this->tableInfo;
	}
	
	public function relationExists($name) {
		return \array_key_exists($name, $this->tableInfo["relations"]);
	}
	
	public function hasOneRelationExists($name) {
		return \array_key_exists($name, $this->tableInfo["relations"]) && $this->tableInfo["relations"][$name]["type"] != "1-N";
	}
	
	public function hasManyRelationExists($name) {
		return \array_key_exists($name, $this->tableInfo["relations"]) && $this->tableInfo["relations"][$name]["type"] == "1-N";
	}
	
	public function keyExists($name) {
		return \array_key_exists($name, $this->tableInfo["keys"]);
	}
	
	public function fieldExists($name) {
		return \array_key_exists($name, $this->tableInfo["fields"]);
	}
	
	public function propertyExists($name) {
		return $this->relationExists($name) || $this->fieldExists($name) || $this->keyExists($name);
	}
	
	public function isAutoIncrement() {
		return $this->getPrimaryKey()
			? $this->getPrimaryKey()->isAutoIncrement()
			: false;
	}
	
	public function getAIField() {
		return $this->isAutoIncrement()
			? \current($this->primaryKey()->fields())
			: null;
	}
	
	private function loadVirtual($name) {
		if (\array_key_exists($name, $this->tableInfo['virtuals'])) {
			$field = new \system\model\FieldVirtual(
				$name, 
				'virtual',
				$this,
				$this->tableInfo['virtuals'][$name]
			);

			$this->fieldList[$name] = $field;
			$this->properties[$name] = $field;
			return $field;
		}
	}
	
	private function loadField($name) {
		if ($name == "*") {
			foreach ($this->tableInfo["fields"] as $name => $info) {
				$this->loadField($name);
			}
			foreach ($this->tableInfo["virtuals"] as $name => $info) {
				$this->loadVirtual($name);
			}
			return $this->getFieldList();
		}
		
		else if (\array_key_exists($name, $this->tableInfo['fields'])) {
			$field = new \system\model\Field(
				$name, 
				$this->tableInfo['fields'][$name]['type'],
				$this,
				$this->tableInfo['fields'][$name]
			);

			$this->fieldList[$name] = $field;
			$this->properties[$name] = $field;
			return $field;
		}
		else {
			return $this->loadVirtual($name);
		}
	}
	
	/**
	 * @param RecordsetBuilder $name
	 * @return \system\model\self 
	 */
	private function loadRelationBuilder($name) {
		
		if (\array_key_exists($name, $this->relationBuilderList)) {
			return $this->relationBuilderList[$name];
		}
		
		if (\array_key_exists($name, $this->tableInfo["relations"])) {
			$info = $this->tableInfo["relations"][$name];
			$builder = new self($info["table"]);
			$builder->setParent($this, $name, 
				\array_key_exists('clauses', $info) ? $info["clauses"] : array(),
				\array_key_exists('type', $info) ? $info["type"] : '1-N',
				\array_key_exists('filterHandle', $info) ? $info["filterHandle"] : null
			);
			$builder->setOnUpdate(\array_key_exists("onUpdate", $info) ? $info["onUpdate"] : "NO_ACTION");
			$builder->setOnDelete(\array_key_exists("onDelete", $info) ? $info["onDelete"] : "NO_ACTION");
			$builder->setJoinType(\array_key_exists("join", $info) ? $info["join"] : "LEFT");
			if (isset($info['selectKey'])) {
				$builder->setSelectKey($info['selectKey']);
			}
			$this->properties[$name] = $builder;
			$this->relationBuilderList[$name] = $builder;
			$builder->hasMany()
				? $this->hasManyRelationBuilderList[$name] = $builder
				: $this->hasOneRelationBuilderList[$name] = $builder;
			return $builder;
		}
	}
	
	private function loadKey($name=null) {
		if (\count($this->tableInfo["keys"]) == 0) {
			return null;
		}
		if (\is_null($name)) {
			\reset($this->tableInfo["keys"]);
			$keyInfo = \current($this->tableInfo["keys"]);
		}
		else {
			if (\array_key_exists($name, $this->tableInfo["keys"])) {
				$keyInfo = $this->tableInfo["keys"][$name];
			} else {
				return null;
			}
		}
		
		$key = new Key($name, $this);
		\reset($this->tableInfo["keys"]);
		$key->setPrimary($keyInfo == \current($this->tableInfo["keys"]));
		if ($key->isPrimary()) {
			$this->primaryKey = $key;
			$key->setAutoIncrement(\cb\array_item('autoIncrement', $keyInfo, array('default' => false)));
		}
		if (\array_key_exists('desc', $keyInfo)) {
			$key->setDesc($keyInfo['desc']);
		}
		foreach ($keyInfo['fields'] as $fieldName) {
			$this->using($fieldName);
			$key->addField($this->searchField($fieldName));
		}
		
		$this->keyList[$name] = $key;
		$this->properties[$name] = $key;
		
		return $key;
	}
	
	public function __construct($tableName) {
		$this->tableName = $tableName;
		$this->tableInfo = \system\Main::getTable($tableName);
		$this->tableAlias = self::getUniqueAlias($tableName);
		
		$this->useAllKeys();
		
		// Automatically importing record mode
		if ($this->isRecordModed()) {
			$this->using("record_mode.*");
			if (\config\settings()->RECORD_MODE_LOGS) {
				$this->using("record_mode.logs.*");
			}
		}
	}
	
	public function isRecordModed() {
		return $this->relationExists("record_mode");
	}
	
	public function getRecordModeField() {
		if ($this->isRecordModed()) {
			$clauses = $this->tableInfo["relations"]["record_mode"]["clauses"];
			\reset($clauses[0]);
			return \key($clauses[0]);
		}
		return null;
	}
	
	public function __get($v) {
		if (\array_key_exists($v, $this->relationBuilderList)) {
			return $this->relationBuilderList[$v];
		} else if (\array_key_exists($v, $this->fieldList)) {
			return $this->fieldList[$v];
		} else if (\array_key_exists($v, $this->keyList)) {
			return $this->keyList[$v];
		} else {
			return null;
		}
	}

	private function setParent(RecordsetBuilder $parentBuilder, $relationName, $clauses, $relationType, $filterHandle) {
		$this->absolutePath = $parentBuilder->getAbsolutePath() != "" ? $parentBuilder->getAbsolutePath() . "." . $relationName : $relationName;
		$this->parentBuilder = $parentBuilder;
		$this->clauses = $clauses;
		switch ($relationType) {
			case "1-1":
			case "1-N":
			case "N-1":
				$this->relationType = $relationType;
				break;
			default:
				throw new \system\exceptions\InternalError('Unknown type for relation <em>@name</em>.', array('@name' => $relationName));
				break;
		}
		
		// Importa automaticamente i campi che fanno parte della JOIN
		foreach ($this->clauses as $clause) {
			$this->parentBuilder->using(key($clause));
			$this->using(current($clause));
		}
		
		$this->useAllKeys();
		
		$this->filterHandle = $filterHandle;
	}
	
	private function useAllKeys() {
		if (!\is_null($this->tableInfo["keys"]) && \count($this->tableInfo["keys"]) > 0) {
			foreach ($this->tableInfo["keys"] as $name => $key) {
				$this->loadKey($name);
			}
		}
	}
	
	/**
	 * Restituisce un nuovo recordset
	 * @param array $data
	 * @return RecordsetInterface
	 */
	public function newRecordset($data=null) {
		$rs = null;
		if (empty($this->recordsetClass)) {
			$rs = new Recordset($this, $data);
		} else {
			$func = $this->recordsetClass;
			$rs =  new $func($this, $data);
		}
		if (!\is_null($data)) {
			\system\Main::raiseModelEvent("onRead", $rs);		
		} else {
			\system\Main::raiseModelEvent("onInitRs", $rs);
		}
		return $rs;
	}
	

	/**
	 * Restituisce la lista degli oggetti RecordsetBuilder associati alle relazioni 1-N
	 * direttamente figlie del nodo corrente
	 * @return RecordsetBuilder[]
	 */
	public function getHasManyRelationBuilderList() {
		return $this->hasManyRelationBuilderList;
	}
	
	
	/**
	 * Restituisce la lista degli oggetti RecordsetBuilder associati alle relazioni *-1
	 * direttamente figlie del nodo corrente
	 * @return RecordsetBuilder[]
	 */
	public function getHasOneRelationBuilderList() {
		return $this->hasOneRelationBuilderList;
	}
	
	/**
	 * Restituisce la lista degli oggetto $recordsetBuilder associati alle relazioni *-1 e 1-N
	 * direttamente figlie del nodo corrente
	 * @return RecordsetBuilder[]
	 */
	public function getRelationBuilderList() {
		return $this->relationBuilderList;
	}
	
	/**
	 * Restituisce la lista degli oggetti Field associati ai campi
	 * direttamente figli del nodo corrente
	 * @return Field[]
	 */
	public function getFieldList() {
		return $this->fieldList;
	}
	
	public function searchKey($path, $required=false) {
		$res = $this->searchProperty($path);
		if ($res == null) {
			if ($required) {
				throw new \system\exceptions\InternalError('Key @path not found.', array('@path' => $path));
			} else {
				return null;
			}
		} else if ($res instanceof Key) {
			return $res;
		} else {
			throw new \system\exceptions\InternalError('Key @path not found.', array('@path' => $path));
		}
	}
	
	/**
	 * Cerca un oggetto Field associato ad una relazione esplorando l'albero del recordset
	 * a partire dal nodo corrente e prendendo in considerazione soltanto relazioni del tipo <strong>HAS ONE</strong>
	 * @param string $path Path relativo
	 * @return Field Il metodo restituisce null se il path non corrisponde a nessuna proprieta'
	 * @throws InternalError Se il path non corrisponde ad un campo ma bensi' ad una relazione
	 */
	public function searchField($path, $required=false) {
		$res = $this->searchProperty($path);
		if ($res == null) {
			if ($required) {
				throw new \system\exceptions\InternalError('Field <em>@path</em> not found in <em>@table</em>.', array('@path' => $path, '@table' => $this->tableName));
			} else {
				return null;
			}
		} else if ($res instanceof Field) {
			return $res;
		} else {
			throw new \system\exceptions\InternalError('Field <em>@path</em> not found in <em>@table</em>.', array('@path' => $path, '@table' => $this->tableName));
		}
	}
	
	/**
	 * Cerca un oggetto RecordsetBuilder associato ad una relazione esplorando l'albero del recordset
	 * a partire dal nodo corrente e prendendo in considerazione soltanto relazioni del tipo <strong>HAS ONE</strong>
	 * @param string $path Path relativo della relazione
	 * @return RecordsetBuilder Il metodo restituisce null se il path non corrisponde a nessuna proprieta'
	 * @throws InternalError Se il path non corrisponde ad un campo ma bensi' ad una relazione
	 */
	public function searchRelationBuilder($path, $required=false) {
		$res = $this->searchProperty($path);
		if ($res == null) {
			if ($required) {
				throw new \system\exceptions\InternalError('Relation <em>@path</em> not found in <em>@table</em>.', array('@path' => $path, '@table' => $this->tableName));
			} else {
				return null;
			}
		} else if ($res instanceof self) {
			return $res;
		} else {
			throw new \system\exceptions\InternalError('Relation <em>@path</em> not found in <em>@table</em>.', array('@path' => $path, '@table' => $this->tableName));
		}
	}
	
	public function searchHasOneRelationBuilder($path, $required=false) {
		try {
			$rel = $this->searchRelationBuilder($path, true);
			if ($rel->hasMany()) {
				throw new \system\exceptions\InternalError('Has-one relation <em>@path</em> not found in <em>@table</em>.', array('@path' => $path, '@table' => $this->tableName));
			}
			return $rel;
		} catch (\Exception $ex) {
			if ($required) {
				throw $ex;
			}
			return null;
		}
	}
	
	public function searchHasManyRelationBuilder($path, $required=false) {
		try {
			$rel = $this->searchRelationBuilder($path, true);
			if (!$rel->hasMany()) {
				throw new \system\exceptions\InternalError('Has-many relation <em>@path</em> not found in <em>@table</em>.', array('@path' => $path, '@table' => $this->tableName));
			}
			return $rel;
		} catch (\Exception $ex) {
			if ($required) {
				throw $ex;
			}
			return null;
		}
	}
	
	/**
	 * Restituisce un oggetto Field o un RecordsetBuilder corrispondente ad un campo
	 * o ad una relazione esplorando l'albero costituito unicamente dalle
	 * relazioni <strong>HAS ONE</strong> a partire dal nodo corrente
	 * @param string $path Path dell'elemento da cercare, relativo al nodo corrente
	 * @return RecordsetBuilder|Field
	 */
	private function searchProperty($path) {
		if (!\array_key_exists($path, $this->paths)) {
			$dotPosition = strpos($path, ".");

			if ($dotPosition === false) {
				if (\array_key_exists($path, $this->fieldList)) {
					$this->paths[$path] = $this->fieldList[$path];
				} else if (\array_key_exists($path, $this->relationBuilderList)) {
					$this->paths[$path] = $this->relationBuilderList[$path];
				} else if (\array_key_exists($path, $this->keyList)) {
					$this->paths[$path] = $this->keyList[$path];
				}
			} else {
				$relation = substr($path, 0, $dotPosition);

				if (\array_key_exists($relation, $this->relationBuilderList)) {
					$this->paths[$path] = $this->relationBuilderList[$relation]->searchProperty(substr($path, $dotPosition+1));
				}
			}
		}
		return \array_key_exists($path, $this->paths) ? $this->paths[$path] : null;
	}

	/**
	 * Verifica se la proprieta' associata al path e' esistente
	 * @param string $path
	 * @return boolean
	 */
	public function issetProperty($path) {
		return $this->searchProperty($path) != null;
	}
	
	/**
	 * Restituisce l'oggetto RecordsetBuilder corrispondente al nodo padre del nodo corrente
	 * @return RecordsetBuilder
	 */
	public function getParentBuilder() {
		return $this->parentBuilder;
	}
	
	public function getFilterHandle() {
		return $this->filterHandle;
	}
	
	/**
	 * Liste di associazioni nome_campo_1 => nome_campo_2 dove 
	 * nome_campo_1 e' il nome di un campo appartenente al recordset builder padre del nodo corrente
	 * e nome_campo_2 e' il nome di un campo appartenente al nodo corrente
	 * @return string[][]
	 */
	public function getClauses() {
		return $this->clauses;
	}
	
	/**
	 * Nome relazione madre
	 * @return string
	 */
	public function getRelationName() {
		return $this->relationName;
	}
	
	public function hasMany() {
		return \is_null($this->parentBuilder) || $this->relationType != "1-1";
	}
	
	public function setJoinType($joinType) {
		switch (strtoupper($joinType)) {
			case "LEFT":
			case "RIGHT":
			case "INNER":
				$this->joinType = strtoupper($joinType);
				break;
			case "LEFT OUTER":
				$this->joinType = "LEFT";
				break;
			case "RIGHT OUTER":
				$this->joinType = "RIGHT";
				break;
			default:
				throw new \system\exceptions\InternalError("Parametri non validi per il RecordsetBuilder");
		}
	}
	
	public function setOnDelete($onDelete) {
		switch (strtoupper($onDelete)) {
			case "CASCADE":
			case "NO_ACTION":
				$this->onDelete = $onDelete;
				break;
			default:
				throw new \system\exceptions\InternalError("Parametri non validi per il RecordsetBuilder");
		}
	}
	
	public function getOnDelete() {
		return $this->onDelete;
	}
	
	public function setOnUpdate($onUpdate) {
		switch (strtoupper($onUpdate)) {
			case "CASCADE":
			case "NO_ACTION":
				$this->onUpdate = $onUpdate;
				break;
			default:
				throw new \system\exceptions\InternalError("Parametri non validi per il RecordsetBuilder");
		}
	}
	
	public function getOnUpdate() {
		return $this->onUpdate;
	}
	
	/**
	 * Restituisce il tipo di JOIN da effettuare con il recordset builder padre
	 * @return string
	 */
	public function getJoinType() {
		return $this->joinType;
	}
	
	/**
	 * Restituisce il path assoluto della relazione
	 * @return string
	 */
	public function getAbsolutePath() {
		return $this->absolutePath;
	}
	
	/**
	 * Table name
	 * @return string
	 */
	public function getTableName() {
		return $this->tableName;
	}

	/**
	 * Alias del nodo corrente
	 * @return string
	 */
	public function getTableAlias() {
		return $this->tableAlias;
	}
	
	/**
	 * Imposta il RecordsetBuilder come virtuale
	 * Permette di specificare un'espressione SQL per la selezione dei dati
	 * @param string $expression 
	 */
	public function setVirtual($selectExpression) {
		$this->selectExpression = "(" . $selectExpression . ")";
	}
	
	/**
	 * True se il RecordsetBuilder è virtuale
	 * @return boolean
	 */
	public function isVirtual() {
		return !empty($this->selectExpression);
	}
	
	/**
	 * Espressione per la selezione dei dati della tabella
	 * @return string
	 */
	public function getSelectExpression() {
		if (empty($this->selectExpression)) {
			return $this->getTableName();
		}
		return $this->selectExpression;
	}
	
	/**
	 * Lista di nomi di campi appartenenti al nodo corrente e costituenti la chiave primaria
	 * @return \system\model\Key
	 */
	public function getPrimaryKey() {
		return $this->primaryKey;
	}
	
	/**
	 * Lista di chiave appartenenti al nodo corrente restituite come array di array di nomi di campi
	 * @return \system\model\Key[]
	 */
	public function getKeys() {
		return $this->keyList;
	}
	
	/**
	 * @param string $name
	 * @return Key
	 */
	public function getKey($name) {
		if (!\array_key_exists($name, $this->keyList)) {
			return null;
		} else {
			return $this->keyList[$name];
		}
	}
	
	public function getSelectKey() {
		return $this->selectKey;
	}
	
	public function setSelectKey($path) {
		$this->using($path);
		$this->selectKey = $path;
	}
	
	private function evalSelectExpression($expression) {
		// Analizzo l'espressione selezionando tutti i percorsi dei campi 
		// specificati attraverso la sintassi @[PATH]

		$matches = array();
		\preg_match_all("/\@\[[a-zA-Z\.0-9_]+\]/", $expression, $matches, PREG_OFFSET_CAPTURE);

		$fullExpression = "(";
		$j = 0;
		foreach ($matches[0] as $match) {
			// Indice di partenza del matching
			$k = $match[1];
			// Copio la porzione di stringa precedente al segmento @[PATH]
			if ($k > $j) {
				$offset = $k - $j;
				$fullExpression .= \substr($expression,$j,$offset);
			}
			// PATH del campo
			$fieldExpr = \substr($match[0],2,-1);

			$dotPos = \strrpos($fieldExpr, ".");
			if ($dotPos === false) {
				// PATH semplice (campo)
				$builder = $this;
				$fieldName = $fieldExpr;
			} else {
				// PATH complesso (SUBPATH.campo)
				// Importo la catena di relazioni
				$relationPath = \substr($fieldExpr, 0, $dotPos);
				$this->using($relationPath);
				$builder = $this->searchRelationBuilder($relationPath);
				$fieldName = \substr($fieldExpr, $dotPos+1);
			}
			// Aggiungo l'espressione SQL per raggiungere il campo
			$fullExpression .= $builder->getTableAlias() . "." . $fieldName;
			// Incremento l'indice della fullExpression
			$j = $k + \strlen($match[0]);
		}
		// Aggiungo l'ultima parte dell'espressione
		$fullExpression .= \substr($expression,$j) . ")";

		return $fullExpression;
	}
	
	public function usingAll() {
		$ti = $this->getTableInfo();
		$this->using("*");
		foreach ($ti["relations"] as $relationName => $relationInfo) {
			if (!$this->parentBuilder || \count(\array_diff($relationInfo["clauses"], $this->getClauses())) > 0) {
				// it just prevents short endless loops:
				//  if the loop involves more than two relations it won't be catched by the if statement above
				//  (it catches A->B->A but not A->B->C->A)
//				if ($relationInfo["type"] != "1-N") {
					$this->loadRelationBuilder($relationName)->usingAll();
//				}
			}
		}
	}
	
	public function setRelation($path, RecordsetBuilder $relation) {
		if (!$relation->hasMany()) {
			throw new \system\exceptions\InternalError('Only has many relation can be added.', array());
		}
		
		$dotPosition = \strpos($path, ".");

		if ($dotPosition === false) {
			if (\array_key_exists($path, $this->tableInfo['relations'])) {
				$this->properties[$path] = $relation;
				$this->relationBuilderList[$path] = $relation;
			} else {
				throw new \system\exceptions\InternalError('Relation <em>@path</em> not found.', array('@path' => $path));
			}
		}
		else {
			$current = substr($path, 0, $dotPosition);
			$rest = substr($path, $dotPosition+1);
			$builder = $this->loadRelationBuilder($current);
			if (\is_null($builder)) {
				throw new \system\exceptions\InternalError('Relation <em>@path</em> not found.', array('@path' => $path));
			}
			$builder->setRelation($rest, $relation);
		}
	}

	/**
	 * Metodo per importare nuovi campi e relazioni HAS ONE all'interno dell'albero del recordset builder.<br/>
	 * Prende in input un numero di parametri opzionale corrispondente ai vari path che si desidera importare
	 */
	public function using() {
		
		foreach (\func_get_args() as $path) {
			$dotPosition = \strpos($path, ".");

			if ($dotPosition === false) {
				$current = $path;
				$rest = null;
			}
			else {
				$current = substr($path, 0, $dotPosition);
				$rest = substr($path, $dotPosition+1);
			}

			if (\is_null($rest)) {
				$found = !\is_null($this->loadField($current));
				if (!$found) {
					$found = !\is_null($this->loadRelationBuilder($current));
					if (!$found) {
						$found = !\is_null($this->loadKey($current));
					}
				}
				if (!$found) {
					// The property is neither a field nor a key nor a relation
					throw new \system\exceptions\InternalError('Field, key or relation <em>@path</em> not found in <em>@table</em>.', array('@path' => $path, '@table' => $this->tableName));
				}
			}
			else {
				// Deve trattarsi di una relazione
				$relation = $this->loadRelationBuilder($current);
				
				if (\is_null($relation)) {
					// The property is neither a field nor a key nor a relation
					throw new \system\exceptions\InternalError('Relation <em>@path</em> not found.', array('@path' => $path));
				}
				
				$relation->using($rest);
			}
		}
	}


	///<editor-fold defaultstate="collapsed" desc="get e set di clausole">
	public function setFilter($filterClauses) {
		if (is_null($filterClauses)) {
			$this->filterClauses = null;
		}
		else if ($filterClauses instanceof FilterClause || $filterClauses instanceof FilterClauseGroup) {
			$this->filterClauses = $filterClauses;
		}
		else {
			throw new \system\exceptions\InternalError("Parametro filterClauses non valido");
		}
	}
	
	public function addFilter($filterClauses) {
		if (!($filterClauses instanceof FilterClause) && !($filterClauses instanceof FilterClauseGroup)) {
			throw new \system\exceptions\InternalError("Parametro filterClauses non valido");
		}

		if (is_null($this->filterClauses)) {
			$this->filterClauses = $filterClauses;
		} else {
			$this->filterClauses = new FilterClauseGroup($this->filterClauses, "AND", $filterClauses);
		}
	}
	
	public function getFilter() {
		return $this->filterClauses;
	}
	
	public function setSort($sortClauses) {
		if (is_null($sortClauses)) {
			$this->sortClauses = null;
		}
		else if ($sortClauses instanceof SortClause || $sortClauses instanceof SortClauseGroup) {
			$this->sortClauses = $sortClauses;
		}
		else {
			throw new \system\exceptions\InternalError("Parametro sortClauses non valido");
		}
	}
	
	public function getSort() {
		return $this->sortClauses;
	}
	
	public function setLimit($limitClause) {
		if (is_null($limitClause)) {
			$this->limitClause = null;
		}
		else if ($limitClause instanceof LimitClause) {
			$this->limitClause = $limitClause;
		}
		else {
			throw new \system\exceptions\InternalError("Parametro limitClause non valido");
		}
	}
	
	public function getLimit() {
		return $this->limitClause;
	}
	///</editor-fold>

	public function selectQuery() {
		$q1 = "";
		$q2 = "";
		$this->initQuery($q1, $q2);
		
		$query = "SELECT $q1 FROM $q2";
		
		$query .= \is_null($this->filterClauses) ? "" : " WHERE " . $this->filterClauses->getQuery();
		$query .= \is_null($this->sortClauses) ? "" : " ORDER BY " . $this->sortClauses->getQuery();
		$query .= \is_null($this->limitClause) ? "" : " LIMIT " . $this->limitClause->getQuery();
		
		return $query;
	}
	
	/**
	 * Metodo per la costruzione di query select.<br/>
	 * Una volta conclusa la chiamata si avra'  che:
	 * <ul>
	 * <li>$q1 conterra' la lista dei campi da selezionare in sintassi SQL</li>
	 * <li>$q2 conterra' la lista delle tabelle dalle quali selezionarli in sintassi SQL</li>
	 * </ul>
	 */
	protected function initQuery(&$q1, &$q2) {
		if (empty($q2)) {
			$q2 .= $this->getSelectExpression() . " " . $this->getTableAlias();
		}
		
		// Aggiungo i campi
		foreach ($this->fieldList as $field) {
			if ($field->isVirtual()) {
				continue;
			}
			$q1 .= empty($q1) ? "" : ", ";
			$q1 .= $field->getSelectExpression() . " AS " . $field->getAlias();
		}

		// Aggiungo tutte le has one relations
		foreach ($this->hasOneRelationBuilderList as $builder) {
			$q2 .= " " . $builder->joinType . " JOIN " . $builder->getSelectExpression() . " " . $builder->tableAlias . " ON ";

			$first = true;
			foreach ($builder->clauses as $clause) {
				\reset($clause);
				$first ? $first = false : $q2 .= " AND ";
				$q2 .= $builder->tableAlias . "." . \current($clause) . " = " . $this->tableAlias . "." . \key($clause);
			}
			$builder instanceof self;
			
			$oldFilter = $builder->getFilter();
			
			// Initialize custom filters
			if (!\is_null($builder->filterHandle)) {
				eval('$func = ' . $builder->filterHandle . ';');
				\call_user_func($func, $this, $builder);
			}
			
			if (!\is_null($builder->filterClauses)) {
				$first ? $first = false : $q2 .= " AND ";
				$q2 .= " (" . $builder->filterClauses->getQuery() . ")";
			}

			$builder->setFilter($oldFilter);
			
			$builder->initQuery($q1, $q2);
		}
	}
	
	public function addReadModeFilters($user=null) {
		$this->addRecordModeFilters("read", $user);
	}
	
	public function addEditModeFilters($user=null) {
		$this->addRecordModeFilters("edit", $user);
	}
	
	public function addDeleteModeFilters($user=null) {
		$this->addRecordModeFilters("delete", $user);
	}
	
	private function addRecordModeFilters($modeType, $user=null) {
		if ($this->isRecordModed()) {
			$mode = $modeType . "_mode";
			
			if ($user->superuser) {
				// SUPERUSER -> just make sure the record mode is >= than MODE_SU
				$this->addFilter(new FilterClause($this->record_mode->$mode, '>=', \system\model\RecordMode::MODE_SU));
				return;
			}
			
			else if ($user->anonymous) {
				// NOT LOGGED -> not logged user can access the recordset only when record mode is = MODE_ANYONE
				$this->addFilter(new FilterClause($this->record_mode->$mode, '>=', \system\model\RecordMode::MODE_ANYONE));
				return;
			}
			
			else {
				// GENERIC LOGGED USER
				//  logged users can access the recordset when
				//   1) the user owns the recordset and the record mode is >= than MODE_SU_OWNER
				//   2) the user is amid the recordset admininstrators and the record mode is >= than MODE_SU_OWNER_ADMINS 
				//   3) the record mode is >= than MODE_REGISTERED 
				$registeredFilter = new FilterClause($this->record_mode->$mode, '>=', \system\model\RecordMode::MODE_REGISTERED);
				
				$adminsFilter = new FilterClauseGroup(
					new FilterClause($this->record_mode->$mode, ">=", \system\model\RecordMode::MODE_SU_OWNER_ADMINS),
					"AND",
					new FilterClauseGroup(
						new CustomClause($user->id . " IN (SELECT user_id FROM record_mode_user rmu WHERE rmu.record_mode_id = " . $this->record_mode->getTableAlias() . "." . $this->record_mode_id->getName()),
						"OR",
						new CustomClause($user->id . " IN (SELECT ur.user_id FROM record_mode_role rmr INNER JOIN user_role ur ON ur.role_id = rmr.role_id WHERE rmr.record_mode_id = " . $this->record_mode->getTableAlias() . "." . $this->record_mode_id->getName())
					)
				);
				
				$ownerFilter = new FilterClauseGroup(
					new FilterClause($this->record_mode->$mode, ">=", \system\model\RecordMode::MODE_SU_OWNER),
					"AND",
					new FilterClause($this->record_mode->owner_id, "=", $user->id)
				);
				
				$this->addFilter(new FilterClauseGroup(
					$registeredFilter,
					'OR',
					$adminsFilter,
					'OR',
					$ownerFilter
				));
				return;
			}
		}
	}
	
//	/**
//	 * Seleziona la prima istanza del recordset filtrandole per un campo
//	 * @param string $fieldPath Percorso del campo
//	 * @param mixed $value Valore di confronto
//	 * @return RecordsetInterface
//	 */
//	public function selectFirstBy($fieldPath, $value) {
//		$oldFilter = $this->filterClauses;
//		
//		for ($i = 0; $i < \func_num_args(); $i++) {
//			$fieldPath = \func_get_arg($i);
//			$value = \func_get_arg($i+1);
//			$this->addFilter(new FilterClause($this->searchField($fieldPath, true), (\is_null($value) ? "IS_NULL" : "="), $value));
//			$i += 2;
//		}
//		
//		$result = $this->selectFirst();
//		
//		$this->filterClauses = $oldFilter;
//		
//		return $result;
//	}
	
	/**
	 * Seleziona le istanze del recordset filtrandole per un campo
	 * @param string $fieldPath Percorso del campo
	 * @param mixed $value Valore di confronto
	 * @return RecordsetInterface[]
	 */
//	public function selectBy($fieldPath, $value) {
//		$newFilter = new FilterClause($this->searchField($fieldPath), (\is_null($value) ? "IS_NULL" : "="), $value);
//		if (\is_null($this->filterClauses)) {
//			$oldFilter = null;
//			$this->filterClauses = $newFilter;
//		} else {
//			$oldFilter = $this->filterClauses;
//			$this->filterClauses = new FilterClauseGroup($newFilter, "AND", $oldFilter);
//		}
//		$result = $this->select();
//		$this->filterClauses = $oldFilter;
//		return $result;
//	}

	/**
	 * @param array $fields Associative array "field name" => "value"
	 * @param boolean $firstResult If true, it returns only the first recordset selected
	 * @return \system\model\RecordsetInterface[]
	 */
	public function selectBy(array $fields, $firstResult=false) {
		$newFilter = new FilterClauseGroup();
		foreach ($fields as $name => $value) {
			$newFilter->addClauses(new FilterClause($this->searchField($name, true), '=', $value));
		}
		if (\is_null($this->filterClauses)) {
			$oldFilter = null;
			$this->filterClauses = $newFilter;
		} else {
			$oldFilter = $this->filterClauses;
			$this->filterClauses = new FilterClauseGroup($newFilter, "AND", $oldFilter);
		}
		$result = $firstResult ? $this->selectFirst() : $this->select();
		$this->filterClauses = $oldFilter;
		return $result;
	}
	
	/**
	 * @param array $fields Associative array "field name" => "value"
	 * @return \system\model\RecordsetInterface
	 */
	public function selectFirstBy(array $fields) {
		return $this->selectBy($fields, true);
	}

	/**
	 * Seleziona le istanze del recordset
	 * @return RecordsetInterface[]
	 */
	public function select() {

		$query = $this->selectQuery();

		$dataAccess = DataLayerCore::getInstance();
		$result = $dataAccess->executeQuery($query);
		
		$recordsets = array();
		
		while (($data = $dataAccess->sqlFetchArray($result))) {
			$rs = $this->newRecordset($data);
			$this->selectKey
				? $recordsets[$rs->getProg($this->selectKey)] = $rs
				: $recordsets[] = $rs;
		}
		
		$dataAccess->sqlFreeResult($result);
		
		return $recordsets;
	}

	/**
	 * Seleziona soltanto il primo risultato del recordset
	 * @return RecordsetInterface
	 */
	public function selectFirst() {
		$oldLimit = $this->limitClause;
		$this->limitClause = new LimitClause(1);
		$result = $this->select();
		$this->limitClause = $oldLimit;
		if (empty($result)) {
			return null;
		} else {
			return $result[0];
		}
	}
	
	/**
	 * Conta i risultati tenendo conto delle clausole FILTER e LIMIT
	 * @return int
	 */
	public function countRecords($ignoreRelations=false) {
		$q1 = "";
		$q2 = "";
		if ($ignoreRelations) {
			$q2 = $this->getTableName() . " AS " . $this->getTableAlias();
		} else {
			$this->initQuery($q1, $q2);
		}
		
		$query = "SELECT COUNT(*) FROM $q2";
		
		$query .= is_null($this->filterClauses) ? "" : " WHERE " . $this->filterClauses->getQuery();
		$query .= is_null($this->limitClause) ? "" : " LIMIT " . $this->limitClause->getQuery();
		
		$dataAccess = DataLayerCore::getInstance();
		
		return $dataAccess->executeScalar($query);
	}
	
	/**
	 * Conta il numero di pagine tenendo conto delle clausole FILTER e redella dimensione richiesta delle pagine
	 * @param int $pageSize
	 * @return int
	 */
	public function countPages($pageSize) {
		$q1 = "";
		$q2 = "";
		$this->initQuery($q1, $q2);

		$query = "SELECT COUNT(*) FROM $q2";

		$query .= is_null($this->filterClauses) ? "" : " WHERE " . $this->filterClauses->getQuery();

		$dataAccess = DataLayerCore::getInstance();

		$numRecords = $dataAccess->executeScalar($query);
		return \ceil($numRecords / $pageSize);
	}

//	public function serialize() {
//		return \serialize($this->tableName);
//	}
//	
//	public function unserialize($serialized) {
//		$tableName = \unserialize($serialized);
//		if (empty($tableName)) {
//			throw new \system\exceptions\InternalError('Unable to unserialize the recordset builder (unknown table)');
//		}
//		$rsb = new self($tableName);
//		$rsb->usingAll();
//		return $rsb;
//	}
	
	
//	private function fieldPathsAsArray(&$properties) {
//		foreach ($this->fieldList as $name => $field) {
//			$properties[] = $this->getAbsolutePath() . '.' . $name;
//		}
//	}
}
