<?php
namespace system\model;
use config\Config;

abstract class RecordsetBuilder implements RecordsetBuilderInterface {
	const OPT_USE_KEYS_NONE = 0;
	const OPT_USE_KEYS_PRIMARY = 1;
	const OPT_USE_KEYS_ALL = 2;
	
	private static function getUniqueAlias($tableName="xmca") {
		static $tableIds = array();
		if (!\array_key_exists($tableName, $tableIds)) {
			$tableIds[$tableName] = 1;
		} else {
			$tableIds[$tableName]++;
		}
		return $tableName . $tableIds[$tableName];
	}
	
	public function printMetaTypes() {
		$result = "";
		foreach ($this->metaTypeList as $mt) {
			$mt instanceof MetaType;
			$result .= "<p>" . $mt->getAbsolutePath() . "</p>";
		}
		foreach ($this->hasOneRelationBuilderList as $hor) {
			$result .= $hor->printMetaTypes();
		}
		return $result;
	}
	
	private $paths = array();
	
	/**
	 * Path assoluto della relazione
	 * @var string
	 */
	protected $absolutePath = "";

	
	protected $importKeys = RecordsetBuilder::OPT_USE_KEYS_NONE;
	
	/**
	 * Nome classe del Recordset
	 * @var string
	 */
	protected $recordsetClass = null;
	/**
	 * Builder della relazione madre 
	 * (null se si tratta del nodo radice)
	 * @var RecordsetBuilder
	 */
	protected $parentBuilder = null;
	/**
	 * Nome della relazione madre
	 * (null se si tratta del nodo radice)
	 * @var string
	 */
	protected $relationName = null;
	/**
	 * Lista di associazioni nome campo relazione madre => nome campo relazione figlia 
	 * (null se si tratta del nodo radice)
	 * @var string[]
	 */
	protected $clauses = null;
	/**
	 * Azione da intraprendere nel caso di cancellazione di un record del recordset builder padre
	 * @var string
	 */
	protected $onDelete = "NO_ACTION";
	/**
	 * Azione da intraprendere nel caso di cancellazione di un record del recordset builder padre
	 * @var string
	 */
	protected $onUpdate = "NO_ACTION";
	/**
	 * Tipo di JOIN per la relazione (utilizzato soltanto in caso non si tratti del nodo radice)
	 * @var string
	 */
	protected $joinType = "INNER";

	/**
	 * Alias della tabella associata al nodo
	 * @var string
	 */
	protected $tableAlias = "xmca";
	
	/**
	 * Espressione per la selezione della tabella
	 * @var string
	 */
	protected $selectExpression = null;
	
	/**
	 * Lista di associazioni nome campo => MetaType campo appartenenti al nodo
	 * @var MetaType[]
	 */
	protected $metaTypeList = array();
	
	/**
	 * Lista di associazioni (di tipo *-1) nome relazione => RecordsetBuilder relazione appartenenti al nodo
	 * @var RecordsetBuilder[]
	 */
	protected $hasOneRelationBuilderList = array();
	/**
	 * Lista di associazioni (di tipo 1-N) nome relazione => RecordsetBuilder relazione appartenenti al nodo
	 * @var RecordsetBuilder[]
	 */
	protected $hasManyRelationBuilderList = array();
	
	/**
	 * Lista di chiavi (espresse come liste di MetaType)
	 * @var MetaType[][]
	 */
	protected $keyList = array();
	/**
	 * Lista di associazioni nome relazione => RecordsetBuilder relazione appartenenti al nodo
	 * @var RecordsetBuilder[]
	 */
	protected $relationBuilderList = array();
	/**
	 * Oggetto FilterClause o FilterClauseGroup contenente le clausole per filtrare i risultati
	 * @var SelectClause
	 */
	protected $filterClauses = null;
	/**
	 * Oggetto SortClause o SortClauseGroup contenente le clausole per ordinare i risultati
	 * @var SelectClause
	 */
	protected $sortClauses = null;
	/**
	 * Oggetto LimitClause contenente la clausola per limitare (o paginare) i risultati
	 * @var SelectClause
	 */
	protected $limitClause = null;
	
	/**
	 * Metodo della classe estesa per l'inizializzazione dei campi
	 */
	abstract protected function loadMetaType($name);
	/**
	 * Metodo della classe estesa per l'inizializzazione delle relazioni *-1
	 */
	abstract protected function loadHasOneRelationBuilder($name);
	/**
	 * Metodo della classe estesa per l'inizializzazione delle relazioni 1-N
	 */
	abstract protected function loadHasManyRelationBuilder($name, RecordsetBuilderInterface $builder);
	
	/**
	 * Metodo della classe estesa per l'inizializzazione delle chiavi
	 */
	abstract protected function loadKeys();
	
	public function __construct($importKeys=self::OPT_USE_KEYS_ALL) {
		switch ($importKeys) {
			case self::OPT_USE_KEYS_NONE:
				break;
			
			case self::OPT_USE_KEYS_ALL:
				$this->useAllKeysRecursive();
				break;
			
			default:
				$this->usePrimaryKeyRecursive();
				break;
		}
		
		$this->tableAlias = self::getUniqueAlias($this->getTableName());
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

	public function setParent(RecordsetBuilderInterface $parentBuilder, $relationName, $clauses) {
		$this->absolutePath = $parentBuilder->getAbsolutePath() != "" ? $parentBuilder->getAbsolutePath() . "." . $relationName : $relationName;
		$this->parentBuilder = $parentBuilder;
		$this->clauses = $clauses;
		
		// Importa automaticamente i campi che fanno parte della JOIN
		foreach ($this->clauses as $parentField => $childField) {
			$this->parentBuilder->using($parentField);
			$this->using($childField);
		}
		
		if ($parentBuilder->importKeys == RecordsetBuilder::OPT_USE_KEYS_PRIMARY) {
			$this->usePrimaryKeyRecursive();
		} else if ($parentBuilder->importKeys == RecordsetBuilder::OPT_USE_KEYS_ALL) {
			$this->useAllKeysRecursive();
		}
	}
	
	public function useAllKeys() {
		$keyList = $this->loadKeys();

		if (!\is_null($keyList) && \count($keyList) > 0) {
			foreach ($keyList as $name => $key) {
				$this->keyList[$name] = array();
				foreach ($key as $fieldName) {
					$this->using($fieldName);
					$this->keyList[$name][] = $this->searchMetaType($fieldName);
				}
			}
		}
	}
	
	public function useAllKeysRecursive() {
		$this->useAllKeys();
		
		foreach ($this->hasOneRelationBuilderList as $builder) {
			$builder->useAllKeysRecursive();
		}
		
		$this->importKeys = RecordsetBuilder::OPT_USE_KEYS_ALL;
	}

	public function usePrimaryKey() {
		$keyList = $this->loadKeys();
		
		if (!\is_null($keyList) && \count($keyList) > 0) {
			list($keyName, $keyFields) = each($keyList);
			$this->keyList[$keyName] = array();
			foreach ($keyFields as $fieldName) {
				$this->using($fieldName);
				$this->keyList[$keyName][] = $this->searchMetaType($fieldName);
			}
		}
	}
	
	public function usePrimaryKeyRecursive() {
		$this->usePrimaryKey();
		
		foreach ($this->hasOneRelationBuilderList as $builder) {
			$builder->usePrimaryKeyRecursive();
		}
		
		$this->importKeys = RecordsetBuilder::OPT_USE_KEYS_PRIMARY;
	}
	
//	private function loadRequiredMetaType($name) {
//		$metaType = $this->loadMetaType($name);
//		if (!$metaType) {
//			throw new \system\InternalErrorException("Campo $name inesistente");
//		}
//		return $metaType;
//	}
//	private function loadRequiredHasOneRelationBuilder($name) {
//		$builder = $this->loadHasOneRelationBuilder($name);
//		if (!$builder) {
//			throw new \system\InternalErrorException("Relazione has one $name inesistente");
//		}
//		return $builder;
//	}
	private function loadRequiredHasManyRelationBuilder($name, RecordsetBuilderInterface $builder) {
		$builder = $this->loadHasManyRelationBuilder($name, $builder);
		if (!$builder) {
			throw new \system\InternalErrorException("Relazione has many $name inesistente");
		}
		foreach ($builder->getClauses() as $parentField => $childField) {
			$this->using($parentField);
			$builder->using($childField);
		}
		return $builder;
	}

	protected function setRecordsetClass($className) {
		if (!\is_callable($className)) {
			throw new \system\InternalErrorException("Nome classe non valido");
		}
		$this->recordsetClass = $className;
	}

	public function replaceAliasPrefix($prefix, $nchars=0) {
		if ($nchars > 0) {
			$suffix = substr($this->tableAlias, $nchars);
		} else {
			$suffix = $this->tableAlias;
		}
		$this->tableAlias = $prefix . $suffix;

		foreach ($this->hasManyRelationBuilderList as $rel) {
			$rel->replaceAliasPrefix($prefix, $nchars);
		}
		foreach ($this->hasOneRelationBuilderList as $rel) {
			$rel->replaceAliasPrefix($prefix, $nchars);
		}
	}
	
	/**
	 * Restituisce un nuovo recordset
	 * @param array $data
	 * @return RecordsetInterface
	 */
	public function newRecordset($data=null) {
		if (empty($this->recordsetClass)) {
			return new Recordset($this, $data);
		} else {
			$func = $this->recordsetClass;
			return new $func($this, $data);
		}
	}

	/**
	 * Restituisce la lista degli oggetti RecordsetBuilderInterface associati alle relazioni 1-N
	 * direttamente figlie del nodo corrente
	 * @return RecordsetBuilderInterface[]
	 */
	public function getHasManyRelationBuilderList() {
		return $this->hasManyRelationBuilderList;
	}
	/**
	 * Restituisce la lista degli oggetti RecordsetBuilderInterface associati alle relazioni *-1
	 * direttamente figlie del nodo corrente
	 * @return RecordsetBuilderInterface[]
	 */
	public function getHasOneRelationBuilderList() {
		return $this->hasOneRelationBuilderList;
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
	 * Cerca un oggetto RecordsetBuilderInterface associato ad una relazione esplorando l'albero del recordset
	 * a partire dal nodo corrente e prendendo in considerazione soltanto relazioni del tipo <strong>HAS ONE</strong>
	 * @param string $path Path relativo della relazione
	 * @return RecordsetBuilderInterface Il metodo restituisce null se il path non corrisponde a nessuna proprieta'
	 * @throws \system\InternalErrorException Se il path non corrisponde ad un campo ma bensi' ad una relazione
	 */
	public function searchRelationBuilder($path) {
		$res = $this->searchProperty($path);
		if ($res == null || $res instanceof RecordsetBuilderInterface) {
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
	protected function searchProperty($path) {
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

				if (\array_key_exists($relation, $this->hasOneRelationBuilderList)) {
					$this->paths[$path] = $this->hasOneRelationBuilderList[$relation]->searchProperty(substr($path, $dotPosition+1));
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
	 * Restituisce l'oggetto RecordsetBuilderInterface corrispondente al nodo padre del nodo corrente
	 * @return RecordsetBuilderInterface
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
	
	
	protected function evalSelectExpression($expression) {
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
				if (!\array_key_exists($current, $this->metaTypeList) && !\array_key_exists($current, $this->hasOneRelationBuilderList)) {

					$relation = null;
					// Cerco la proprieta' tra i campi da importare
					$field = $this->loadMetaType($current);

					if (!\is_null($field)) {
						// La proprieta' e' un campo: lo importo
						$this->metaTypeList[$current] = $field;
					} else {
						// La proprieta' non e' un campo...
						// La cerco tra le has one relations
						$relation = $this->loadHasOneRelationBuilder($current);
						if (!\is_null($relation)) {
							// La proprieta' e' una relazione: la importo
							$this->hasOneRelationBuilderList[$current] = $relation;
							$this->relationBuilderList[$current] = $relation;
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
				if (\array_key_exists($current, $this->hasOneRelationBuilderList)) {
					$relation = $this->hasOneRelationBuilderList[$current];
				}
				else {
					// Cerco la relazione tra le has one relation
					$relation = $this->loadHasOneRelationBuilder($current);
					if (!\is_null($relation)) {
						// La proprieta' e' una relazione: la importo
						$this->hasOneRelationBuilderList[$current] = $relation;
						$this->relationBuilderList[$current] = $relation;
					} else {
						// La proprieta' non e' una relazione.. ERRORE
						throw new \system\InternalErrorException("Path $path non corrisponde a nessuna relazione valida");
					}
				}

				$relation->using($rest);
			}
		}
	}

	/**
	 * Aggiunge all'albero del recordset un RecordsetBuilderInterface corrispondente ad una relazione di tipo HAS MANY
	 * @param string $path Path relativo per la relazione
	 * @param RecordsetBuilderInterface $builder Builder della relazione
	 */
	public function setHasManyRelationBuilder($path, RecordsetBuilderInterface $builder) {
		$dotPosition = strpos($path, ".");
		
		if ($dotPosition === false) {
			
			$this->hasManyRelationBuilderList[$path] = $this->loadRequiredHasManyRelationBuilder($path, $builder);
			$this->relationBuilderList[$path] = $this->hasManyRelationBuilderList[$path];
			
		} else {
			$relation = substr($path, 0, $dotPosition);
			
			if (\array_key_exists($relation, $this->relationBuilderList)) {
				$this->relationBuilderList[$relation]->setHasManyRelationBuilder(substr($path, $dotPosition+1), $builder);
			} else {
				throw new \system\InternalErrorException("Relazione $relation non presente nelle relations list o non importata nel recordset");
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
		
		$keyName = $this->getRecordModeKeyName();
		
		if ($read) {
			$mode = "read_mode";
		} else {
			$mode = "edit_mode";
		}
		
		if (!\is_null($keyName)) {
			
			$this->using("record_mode.$mode", "record_mode.owner_id", "record_mode.group_id");
			
			$recordModeBuilder = $this->searchRelationBuilder("record_mode");
			$rmType = $recordModeBuilder->searchMetaType("$mode");
			$ownerType = $recordModeBuilder->searchMetaType("owner_id");
			$groupType = $recordModeBuilder->searchMetaType("group_id");
			
			// CONDIZIONE 1: record mode > NOBODY
			$rmFilter = new FilterClauseGroup(
				new FilterClause($rmType, ">", \module\core\model\XmcaRecordMode::MODE_NOBODY)
			);
			
			// Tolta la condizione 1, se l'utente è un superutente, non c'è nient'altro da verificare
			if (!\module\core\model\XmcaUser::isSuperuser(\system\Login::getLoggedUserId())) {
				// SOLTANTO SE l'utente NON e' un un SUPERUSER
				// l'accesso si restringe a queste condizioni:
				// modalità = ANYONE
				// OPPURE modalità >= GRUPPO e utente nel gruppo del record
				// OPPURE modalità >= OWNER e utente owner
				
				$anyoneFilter = new FilterClause($rmType, "=", \module\core\model\XmcaRecordMode::MODE_ANYONE);
				
				if (!\system\Login::getInstance()->isAnonymous()) {
					$groupFilter = new FilterClauseGroup(
						new FilterClause($rmType, ">=", \module\core\model\XmcaRecordMode::MODE_SU_OWNER_GROUP),
						"AND",
						new CustomClause($recordModeBuilder->getTableAlias() . "." . $groupType->getName() . " IN (SELECT group_id FROM xmca_user_group WHERE user_id = " .	\system\Login::getLoggedUserId() . ")")
					);

					$ownerFilter = new FilterClauseGroup(
						new FilterClause($rmType, ">=", \module\core\model\XmcaRecordMode::MODE_SU_OWNER),
						"AND",
						new FilterClause($ownerType, "=", \system\Login::getLoggedUserId())
					);

					// unisco tutte le condizioni
					$rmFilter->addClauses("AND", new FilterClauseGroup(
						$anyoneFilter,
						"OR",
						$ownerFilter,
						"OR",
						$groupFilter
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

	public function isRecordModed() {
		return false;
	}
	public function isRecordModeLogged() {
		return $this->isRecordModed() && \config\settings()->RECORD_MODE_LOGGED;
	}
	public function getRecordModeKeyName() {
		return null;
	}
}
?>