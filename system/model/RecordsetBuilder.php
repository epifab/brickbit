<?php
namespace system\model;

class RecordsetBuilder {
	const KEYS_NONE = 0;
	const KEYS_PRIMARY = 1;
	const KEYS_PRIMARY_RECURSIVE = 2;
	const KEYS_ALL = 3;
	const KEYS_ALL_RECURSIVE = 4;
	
//	private static function getUniqueAlias($tableName="xmca") {
//		static $tableIds = array();
//		if (!\array_key_exists($tableName, $tableIds)) {
//			$tableIds[$tableName] = 1;
//		} else {
//			$tableIds[$tableName]++;
//		}
//		return $tableName . $tableIds[$tableName];
//	}
	
//	public function printMetaTypes() {
//		$result = "";
//		foreach ($this->metaTypeList as $mt) {
//			$mt instanceof MetaType;
//			$result .= "<p>" . $mt->getAbsolutePath() . "</p>";
//		}
//		foreach ($this->hasOneRelationBuilderList as $hor) {
//			$result .= $hor->printMetaTypes();
//		}
//		return $result;
//	}
	
	private $paths = array();
	
	/**
	 * Path assoluto della relazione
	 * @var string
	 */
	private $absolutePath = "";

	
	private $importKeys = self::KEYS_ALL_RECURSIVE;
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
	private $tableAlias = "xmca";
	
	/**
	 * Espressione per la selezione della tabella
	 * @var string
	 */
	private $selectExpression = null;
	
	/**
	 * Lista di associazioni nome campo => MetaType campo appartenenti al nodo
	 * @var MetaType[]
	 */
	private $metaTypeList = array();
	
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
	 * Lista di chiavi (espresse come liste di MetaType)
	 * @var MetaType[][]
	 */
	private $keyList = array();
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
	
	private $autoIncrement = false;
	
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
	
	public function metaTypeExists($name) {
		return \array_key_exists($name, $this->tableInfo["fields"]);
	}
	
	public function propertyExists($name) {
		return $this->relationExists($name) || $this->metaTypeExists($name) || $this->keyExists($name);
	}
	
	public function isAutoIncrement() {
		return $this->autoIncrement;
	}
	
	private function loadMetaType($name) {
		if ($name == "*") {
			foreach ($this->tableInfo["fields"] as $name => $info) {
				$this->loadMetaType($name);
			}
		} else if (\array_key_exists($name, $this->tableInfo["fields"])) {
			$metaType = $this->tableInfo["fields"][$name]["type"]();
			return $metaType;
		}
	}
	
	private function loadRelationBuilder($name) {
		if (\array_key_exists($name, $this->tableInfo["relations"])) {
			$info = $this->tableInfo["relations"][$name];
			$builder = new self($name);
			$builder->setParent($this, $name, $info["clauses"], $info["type"]);
			$builder->setOnUpdate(\array_key_exists("onUpdate", $info) ? $info["onUpdate"] : "NO_ACTION");
			$builder->setOnDelete(\array_key_exists("onDelete", $info) ? $info["onDelete"] : "NO_ACTION");
			$builder->setJoinType(\array_key_exists("join", $info) ? $info["join"] : "LEFT");
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
				throw new \system\InternalErrorException(\system\Lang::translate('Key <em>@name</em> not found.', array('@name' => $name)));
			}
		}
		
		$key = array();
		foreach ($keyInfo as $fieldName) {
			$this->using($fieldName);
			$key[] = $this->searchMetaType($fieldName);
		}
		return $key;
	}
	
	public function __construct($tableName) {
		$this->tableName = $tableName;
		$this->tableInfo = \system\logic\Module::getTable($tableName);
		if (\count($this->tableInfo["keys"]) > 0) {
			\reset($this->tableInfo["keys"]);
			$k = \current($this->tableInfo["keys"]);
			$this->autoIncrement = \array_key_exists("autoIncrement", $k) ? (bool)$k["autoIncrement"] : false;
		}
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
	
	public function recordModeField() {
		if ($this->isRecordModed()) {
			$clauses = $this->tableInfo["relations"]["record_mode"]["clauses"];
			\reset($clauses);
			return \key($clauses);
		}
		return null;
	}
	
	public function __get($v) {
		if (\array_key_exists($v, $this->relationBuilderList)) {
			return $this->relationBuilderList[$v];
		} else if (\array_key_exists($v, $this->metaTypeList)) {
			return $this->metaTypeList[$v];
		} else if (\array_key_exists($v, $this->keyList)) {
			return $this->keyList[$v];
		} else {
			return null;
		}
	}

	private function setParent(RecordsetBuilder $parentBuilder, $relationName, $clauses, $relationType) {
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
				throw new \system\InternalErrorException(\system\Lang::translate('Unknown type for relation <em>@name</em>.', array('@name' => $relationName)));
				break;
		}
		
		// Importa automaticamente i campi che fanno parte della JOIN
		foreach ($this->clauses as $parentField => $childField) {
			$this->parentBuilder->using($parentField);
			$this->using($childField);
		}
		
		if ($parentBuilder->importKeys == self::KEYS_PRIMARY_RECURSIVE) {
			$this->usePrimaryKeyRecursive();
		} else if ($parentBuilder->importKeys == self::KEYS_ALL_RECURSIVE) {
			$this->useAllKeysRecursive();
		}
	}
	
	private function useAllKeys() {
		if (!\is_null($this->tableInfo["keys"]) && \count($this->tableInfo["keys"]) > 0) {
			foreach ($this->tableInfo["keys"] as $name => $key) {
				$this->keyList[$name] = $this->loadKey($name);
			}
		}
		$this->importKeys = self::KEYS_ALL;
	}
	
	private function useAllKeysRecursive() {
		$this->useAllKeys();
		
		foreach ($this->relationBuilderList as $builder) {
			$builder->useAllKeysRecursive();
		}
		$this->importKeys = self::KEYS_ALL_RECURSIVE;
	}

	private function usePrimaryKey() {
		if (!\is_null($this->tableInfo["keys"]) && \count($this->tableInfo["keys"]) > 0) {
			foreach ($this->tableInfo["keys"] as $name => $key) {
				$this->keyList[$name] = $this->loadKey($name);
				break; // only the first key
			}
		}
		$this->importKeys = self::KEYS_PRIMARY;
	}
	
	private function usePrimaryKeyRecursive() {
		$this->usePrimaryKey();
		
		foreach ($this->relationBuilderList as $builder) {
			$builder->usePrimaryKeyRecursive();
		}
		$this->importKeys = self::KEYS_PRIMARY_RECURSIVE;
	}
	
//	private function setRecordsetClass($className) {
//		if (!\is_callable($className)) {
//			throw new \system\InternalErrorException("Nome classe non valido");
//		}
//		$this->recordsetClass = $className;
//	}

	public function replaceAliasPrefix($prefix, $nchars=0) {
		if ($nchars > 0) {
			$suffix = substr($this->tableAlias, $nchars);
		} else {
			$suffix = $this->tableAlias;
		}
		$this->tableAlias = $prefix . $suffix;

		foreach ($this->relationBuilderList as $rel) {
			$rel->replaceAliasPrefix($prefix, $nchars);
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
			\system\logic\Module::raise("onRead", $rs);		
		} else {
			\system\logic\Module::raise("onInitRs", $rs);
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
	 * Restituisce la lista degli oggetti MetaType associati ai campi
	 * direttamente figli del nodo corrente
	 * @return MetaType[]
	 */
	public function getMetaTypeList() {
		return $this->metaTypeList;
	}
	
	/**
	 * Cerca un oggetto MetaType associato ad una relazione esplorando l'albero del recordset
	 * a partire dal nodo corrente e prendendo in considerazione soltanto relazioni del tipo <strong>HAS ONE</strong>
	 * @param string $path Path relativo
	 * @return MetaType Il metodo restituisce null se il path non corrisponde a nessuna proprieta'
	 * @throws \system\InternalErrorException Se il path non corrisponde ad un campo ma bensi' ad una relazione
	 */
	public function searchMetaType($path) {
		$res = $this->searchProperty($path);
		if ($res == null || $res instanceof MetaType) {
			return $res;
		} else {
			throw new \system\InternalErrorException("Il path $path non corrisponde ad un campo");
		}
	}

	/**
	 * Cerca un oggetto RecordsetBuilder associato ad una relazione esplorando l'albero del recordset
	 * a partire dal nodo corrente e prendendo in considerazione soltanto relazioni del tipo <strong>HAS ONE</strong>
	 * @param string $path Path relativo della relazione
	 * @return RecordsetBuilder Il metodo restituisce null se il path non corrisponde a nessuna proprieta'
	 * @throws \system\InternalErrorException Se il path non corrisponde ad un campo ma bensi' ad una relazione
	 */
	public function searchRelationBuilder($path) {
		$res = $this->searchProperty($path);
		if ($res == null || $res instanceof RecordsetBuilder) {
			return $res;
		} else {
			throw new \system\InternalErrorException("Il path $path non corrisponde a nessun campo importato");
		}
	}
	
	/**
	 * Restituisce un oggetto MetaType o un RecordsetBuilder corrispondente ad un campo
	 * o ad una relazione esplorando l'albero costituito unicamente dalle
	 * relazioni <strong>HAS ONE</strong> a partire dal nodo corrente
	 * @param string $path Path dell'elemento da cercare, relativo al nodo corrente
	 * @return RecordsetBuilder|MetaType
	 */
	private function searchProperty($path) {
		if (!\array_key_exists($path, $this->paths)) {
			$dotPosition = strpos($path, ".");

			if ($dotPosition === false) {
				if (\array_key_exists($path, $this->metaTypeList)) {
					$this->paths[$path] = $this->metaTypeList[$path];
				} else if (\array_key_exists($path, $this->relationBuilderList)) {
					$this->paths[$path] = $this->relationBuilderList[$path];
				} else {
					$this->paths[$path] = null;
				}
			} else {
				$relation = substr($path, 0, $dotPosition);

				if (\array_key_exists($relation, $this->relationBuilderList)) {
					$this->paths[$path] = $this->relationBuilderList[$relation]->searchProperty(substr($path, $dotPosition+1));
				} else {
					$this->paths[$path] = null;
				}
			}
		}
		return $this->paths[$path];
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
	
	public function hasMary() {
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
				throw new \system\InternalErrorException("Parametri non validi per il RecordsetBuilder");
		}
	}
	
	public function setOnDelete($onDelete) {
		switch (strtoupper($onDelete)) {
			case "CASCADE":
			case "NO_ACTION":
				$this->onDelete = $onDelete;
				break;
			default:
				throw new \system\InternalErrorException("Parametri non validi per il RecordsetBuilder");
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
				throw new \system\InternalErrorException("Parametri non validi per il RecordsetBuilder");
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
	 * @return string[]
	 */
	public function getPrimaryKey() {
		if (count($this->keyList > 0)) {
			return reset($this->keyList);
		} else {
			return null;
		}
	}
	
	/**
	 * Lista di chiave appartenenti al nodo corrente restituite come array di array di nomi di campi
	 * @return string[][]
	 */
	public function getKeys() {
		return $this->keyList;
	}
	
	public function getKey($name) {
		if (!\array_key_exists($name, $this->keyList)) {
			return null;
		} else {
			return $this->keyList[$name];
		}
	}
	
	
	private function evalSelectExpression($expression) {
		// Analizzo l'espressione selezionando tutti i percorsi dei campi 
		// specificati attraverso la sintassi @[PATH]
//		if ($this->getAbsolutePath()== "title_image")
//		throw new \Exception($this->getAbsolutePath() . " " . $this->getTableName() . " " . $this->getTableAlias());
		
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
	
	
	/**
	 * Metodo per importare nuovi campi e relazioni HAS ONE all'interno dell'albero del recordset builder.<br/>
	 * Prende in input un numero di parametri opzionale corrispondente ai vari path che si desidera importare
	 */
	public function using() {
		
		foreach (\func_get_args() as $path) {
			$dotPosition = strpos($path, ".");

			if ($dotPosition === false) {
				$current = $path;
				$rest = null;
			}
			else {
				$current = substr($path, 0, $dotPosition);
				$rest = substr($path, $dotPosition+1);
			}

			if (\is_null($rest)) {
				// Potrebbe essere una relazione o un campo

				// Controllo se la proprieta' non e' stata gia' importata
				if (!\array_key_exists($current, $this->metaTypeList) && !\array_key_exists($current, $this->relationBuilderList)) {

					$relation = null;
					// Cerco la proprieta' tra i campi da importare
					$field = $this->loadMetaType($current);

					if (!\is_null($field)) {
						// La proprieta' e' un campo: lo importo
						$this->metaTypeList[$current] = $field;
					} else {
						// La proprieta' non e' un campo...
						// La cerco tra le has one relations
						$relation = $this->loadRelationBuilder($current);
						if (!\is_null($relation)) {
							// La proprieta' e' una relazione: la importo
							$this->relationBuilderList[$current] = $relation;
							$relation->hasMany() 
								? $this->hasManyRelationBuilderList[$current] = $relation
								: $this->hasOneRelationBuilderList[$current] = $relation;
						} else {
							// La proprieta' non e' ne' un campo ne' una relazione.. ERRORE
							throw new \system\InternalErrorException("Path $path non valido");
						}
					}
				}
			}
			else {
				// Deve trattarsi di una relazione

				// Controllo se la relazione non e' stata gia' importata
				if (\array_key_exists($current, $this->relationBuilderList)) {
					$relation = $this->relationBuilderList[$current];
				}
				else {
					// Cerco la relazione tra le has one relation
					$relation = $this->loadRelationBuilder($current);
					if (!\is_null($relation)) {
						// La proprieta' e' una relazione: la importo
						$this->relationBuilderList[$current] = $relation;
						$relation->hasMany() 
							? $this->hasManyRelationBuilderList[$current] = $relation
							: $this->hasOneRelationBuilderList[$current] = $relation;
					} else {
						// La proprieta' non e' una relazione.. ERRORE
						throw new \system\InternalErrorException("Path $path non corrisponde a nessuna relazione valida");
					}
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
			throw new \system\InternalErrorException("Parametro filterClauses non valido");
		}
	}
	
	public function addFilter($filterClauses) {
		if (!($filterClauses instanceof FilterClause) && !($filterClauses instanceof FilterClauseGroup)) {
			throw new \system\InternalErrorException("Parametro filterClauses non valido");
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
			throw new \system\InternalErrorException("Parametro sortClauses non valido");
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
			throw new \system\InternalErrorException("Parametro limitClause non valido");
		}
	}
	
	public function getLimit() {
		return $this->limitClause;
	}
	///</editor-fold>

	/**
	 * Metodo per la costruzione di query select.<br/>
	 * Una volta conclusa la chiamata si avra'  che:
	 * <ul>
	 * <li>$q1 conterra' la lista dei campi da selezionare in sintassi SQL</li>
	 * <li>$q2 conterra' la lista delle tabelle dalle quali selezionarli in sintassi SQL</li>
	 * </ul>
	 */
	public function selectQuery(&$q1, &$q2) {
		if (empty($q2)) {
			$q2 .= $this->getSelectExpression() . " " . $this->getTableAlias();
		}
		
		// Aggiungo i campi
		foreach ($this->metaTypeList as $metaType) {
			if ($metaType instanceof MetaVirtual) {
				continue;
			}
			$q1 .= empty($q1) ? "" : ", ";
			$q1 .= $metaType->getSelectExpression() . " AS " . $metaType->getAlias();
		}

		// Aggiungo tutte le has one relations
		foreach ($this->hasOneRelationBuilderList as $builder) {
			$q2 .= " " . $builder->joinType . " JOIN " . $builder->getSelectExpression() . " " . $builder->tableAlias . " ON ";
			
			$first = true;
			foreach ($builder->clauses as $parentField => $childField) {
				$first ? $first = false : $q2 .= " AND ";
				$q2 .= $builder->tableAlias . "." . $childField . " = " . $this->tableAlias . "." . $parentField;
			}
			
			if (!\is_null($builder->filterClauses)) {
				$q2 .= " AND (" . $builder->filterClauses->getQuery() . ")";
			}

			$builder->selectQuery($q1, $q2);
		}
	}
	
	public function addReadModeFilters() {
		$this->addRecordModeFilters(true);
	}
	
	public function addEditModeFilters() {
		$this->addRecordModeFilters(false);
	}
	
	private function addRecordModeFilters($read=true) {
		if ($this->isRecordModed()) {
			if ($read) {
				$mode = "read_mode";
			} else {
				$mode = "edit_mode";
			}

			// CONDIZIONE 1: record mode > NOBODY
			$rmFilter = new FilterClauseGroup(
				new FilterClause($this->record_mode->$mode, ">", \system\model\RecordMode::MODE_NOBODY)
			);

			// Tolta la condizione 1, se l'utente è un superutente, non c'è nient'altro da verificare
			if (!\module\core\model\XmcaUser::isSuperuser(\system\Login::getLoggedUserId())) {
				// SOLTANTO SE l'utente NON e' un un SUPERUSER
				// l'accesso si restringe a queste condizioni:
				// modalità = ANYONE
				// OPPURE modalità >= GRUPPO e utente nel gruppo del record
				// OPPURE modalità >= OWNER e utente owner

				$anyoneFilter = new FilterClause($this->record_mode->$mode, "=", \system\model\RecordMode::MODE_ANYONE);

				if (!\system\Login::getInstance()->isAnonymous()) {
					$roleFilter = new FilterClauseGroup(
						new FilterClause($this->record_mode->$mode, ">=", \system\model\RecordMode::MODE_SU_OWNER_ROLE),
						"AND",
						new CustomClause($this->record_mode->getTableAlias() . "." . $this->record_mode->role_id->getName() . " IN (SELECT role_id FROM xmca_user_role WHERE user_id = " .	\system\Login::getLoggedUserId() . ")")
					);

					$ownerFilter = new FilterClauseGroup(
						new FilterClause($this->record_mode->$mode, ">=", \system\model\RecordMode::MODE_SU_OWNER),
						"AND",
						new FilterClause($this->record_mode->owner_id, "=", \system\Login::getLoggedUserId())
					);

					// unisco tutte le condizioni
					$rmFilter->addClauses("AND", new FilterClauseGroup(
						$anyoneFilter,
						"OR",
						$ownerFilter,
						"OR",
						$roleFilter
					));
				} else {
					$rmFilter->addClauses("AND", $anyoneFilter);
				}
			}

			$oldFilter = $this->getFilter();

			if (\is_null($oldFilter)) {
				$this->setFilter($rmFilter);
			}
			else {
				$newFilter = new FilterClauseGroup($oldFilter, "AND", $rmFilter);
				$this->setFilter($newFilter);
			}
		}
	}
	
	/**
	 * Seleziona la prima istanza del recordset filtrandole per un campo
	 * @param string $fieldPath Percorso del campo
	 * @param mixed $value Valore di confronto
	 * @return RecordsetInterface
	 */
	public function selectFirstBy($fieldPath, $value) {
		$newFilter = new FilterClause($this->searchMetaType($fieldPath), (\is_null($value) ? "IS_NULL" : "="), $value);
		
		if (\is_null($this->filterClauses)) {
			$oldFilter = null;
			$this->filterClauses = $newFilter;
		} else {
			$oldFilter = $this->filterClauses;
			$this->filterClauses = new FilterClauseGroup($newFilter, "AND", $oldFilter);
		}
		$result = $this->selectFirst();
		$this->filterClauses = $oldFilter;
		return $result;
	}
	
	/**
	 * Seleziona le istanze del recordset filtrandole per un campo
	 * @param string $fieldPath Percorso del campo
	 * @param mixed $value Valore di confronto
	 * @return RecordsetInterface[]
	 */
	public function selectBy($fieldPath, $value) {
		$newFilter = new FilterClause($this->searchMetaType($fieldPath), (\is_null($value) ? "IS_NULL" : "="), $value);
		if (\is_null($this->filterClauses)) {
			$oldFilter = null;
			$this->filterClauses = $newFilter;
		} else {
			$oldFilter = $this->filterClauses;
			$this->filterClauses = new FilterClauseGroup($newFilter, "AND", $oldFilter);
		}
		$result = $this->select();
		$this->filterClauses = $oldFilter;
		return $result;
	}

	/**
	 * Seleziona le istanze del recordset
	 * @return RecordsetInterface[]
	 */
	public function select() {
		
		$q1 = "";
		$q2 = "";
		$this->selectQuery($q1, $q2);
		
		$query = "SELECT $q1 FROM $q2";
		
		$query .= \is_null($this->filterClauses) ? "" : " WHERE " . $this->filterClauses->getQuery();
		$query .= \is_null($this->sortClauses) ? "" : " ORDER BY " . $this->sortClauses->getQuery();
		$query .= \is_null($this->limitClause) ? "" : " LIMIT " . $this->limitClause->getQuery();

		$dataAccess = DataLayerCore::getInstance();
		$result = $dataAccess->executeQuery($query, __FILE__, __LINE__);
		
		$recordsets = array();
		
		while (($data = $dataAccess->sqlFetchArray($result))) {
			$recordsets[] = $this->newRecordset($data);
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
			$this->selectQuery($q1, $q2);
		}
		
		$query = "SELECT COUNT(*) FROM $q2";
		
		$query .= is_null($this->filterClauses) ? "" : " WHERE " . $this->filterClauses->getQuery();
		$query .= is_null($this->limitClause) ? "" : " LIMIT " . $this->limitClause->getQuery();
		
		$dataAccess = DataLayerCore::getInstance();
		
		return $dataAccess->executeScalar($query, __FILE__, __LINE__);
	}
	
	/**
	 * Conta il numero di pagine tenendo conto delle clausole FILTER e redella dimensione richiesta delle pagine
	 * @param int $pageSize
	 * @return int
	 */
	public function countPages($pageSize) {
		$q1 = "";
		$q2 = "";
		$this->selectQuery($q1, $q2);

		$query = "SELECT COUNT(*) FROM $q2";

		$query .= is_null($this->filterClauses) ? "" : " WHERE " . $this->filterClauses->getQuery();

		$dataAccess = DataLayerCore::getInstance();

		$numRecords = $dataAccess->executeScalar($query, __FILE__, __LINE__);
		return \ceil($numRecords / $pageSize);
	}
}
?>