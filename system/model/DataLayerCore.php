<?php
namespace system\model;

/**
 * Classe per l'accesso ai dati.
 * E' possibile utilizzarla per interfacciarsi in maniera totalmente trasparente con mysql o sqlserver
 * @author Fabio Epifani
 */
class DataLayerCore {
	protected $connection = null;
	
	private $dbmsType = \config\Config::DBMS_MYSQL;
	private $dbName;
	private $dbHost;
	
	private $transactionActive = false;
	
	/**
	 * @var DataLayerCore
	 */
	private static $instance = null;

	/**
	 * Restituisce un'istanza dello strato di accesso ai dati
	 * @return DataLayerCore
	 */
	public static function getInstance() {
		if (\is_null(self::$instance)) {
			self::$instance = new DataLayerCore(
				\config\settings()->DB_HOST,
				\config\settings()->DB_USER,
				\config\settings()->DB_PASS,
				\config\settings()->DB_NAME,
				\config\settings()->DB_TYPE
			);
		}
		return self::$instance;
	}

	protected function __construct($dbHost, $dbUser, $dbPass, $dbName, $dbms) {

		$this->setDBMS($dbms);

		$this->connection = $this->sqlConnect($dbHost, $dbUser, $dbPass);
		if ($this->dbmsType == \config\Config::DBMS_MSSQL) {
			if (ini_set('mssql.charset', 'utf-8') === false) {
				\system\Log::add(\system\Lang::translate("Unable to set mssql.charset in php ini configuration file."));
			} else {
				\system\Log::add(\system\Lang::translate("mssql.charset has been set to @value", array('@value' => \ini_get('mssql.charset'))));
			}
		}
		if (!$this->connection) {
			throw new DataLayerException(__FILE__, __LINE__, null, $this->sqlError(), DataLayerException::ACTION_CONNECTION);
		}
		if ($this->dbmsType == \config\Config::DBMS_MYSQL && $this->sqlQuery("SET NAMES 'utf8'") === false) {
			throw new DataLayerException(__FILE__, __LINE__, "SET NAMES 'utf8'", $this->connection);
		}
		if ($this->sqlSelectDb($dbName) === false) {
			throw new DataLayerException(__FILE__, __LINE__, null, $this->sqlError(), DataLayerException::ACTION_DB_SELECTION);
		}
	}

	public function  __destruct() {
		$this->sqlClose();
	}

	
	// Metodi per la connessione e la disconnessione con il DBMS
	private function sqlConnect($dbHost, $dbUser, $dbPass) {
		$this->dbHost = $dbHost;

		\system\Log::add(\system\Lang::translate("Connection to @dbms server at @host. User: @user.", array(
			'@dbms' => $this->dbmsType == \config\Config::DBMS_MSSQL ? "MySql" : "SqlServer",
			'@host' => $dbHost,
			'@user' => $dbUser
		)));

		$res = ($this->dbmsType == \config\Config::DBMS_MYSQL ? \mysql_connect($dbHost, $dbUser, $dbPass) : \mssql_connect($dbHost, $dbUser, $dbPass));
		if ($res) {
			\system\Log::add("<p>Connessione stabilita correttamente</p>");
		}
		return $res;
	}
	private function sqlClose() {
		$res = ($this->dbmsType == \config\Config::DBMS_MYSQL ? \mysql_close($this->connection) : \mssql_close($this->connection));
		if ($res) {
			\system\Log::add("<p>Connessione con il DBMS chiusa correttamente</p>");
		}
		return $res;
	}
	private function sqlSelectDb($dbName) {
		$this->dbName = $dbName;
		\system\Log::add("<p><strong>Tentativo di connessione a DB " . $dbName . "</strong></p>");

		$res = ($this->dbmsType == \config\Config::DBMS_MYSQL ? \mysql_select_db($dbName, $this->connection) : \mssql_select_db($dbName, $this->connection));
		if ($res) {
			\system\Log::add("<p>Database <strong>" . $dbName . "</strong> selezionato</p>");
		}
		return $res;
	}

	// Metodi pubblici per l'esecuzione di query
	/**
	 * Esegue la query SQL
	 * @param string $query Sql Query
	 * @return resource 
	 * For SELECT, SHOW, DESCRIBE, EXPLAIN and other statements returning resultset, sqlQuery returns a resource on success, or false on error.
	 * For other type of SQL statements, INSERT, UPDATE, DELETE, DROP, etc, sqlQuery returns true on success or false on error.
	 * The returned result resource should be passed to sqlFetchArray to access the returned data.
	 * Use sqlNumRows to find out how many rows were returned for a SELECT statement or sqlAffectedRows to find out how many rows were affected by a DELETE, INSERT, REPLACE, or UPDATE statement.
	 * sqlQuery will also fail and return false if the user does not have permission to access the table(s) referenced by the query.
	 */
	protected function sqlQuery($query) {
		\system\Log::add('<div><p><strong>Esecuzione Query SQL DB ' . $this->dbName . ' (' . $this->dbHost . ')</strong></p><textarea rows="10" cols="80">' . \htmlentities($query,ENT_NOQUOTES,'UTF-8') . '</textarea></div>');

		if ($this->dbmsType == \config\Config::DBMS_MSSQL) {
			return \mssql_query($query, $this->connection);
		} else {
			return \mysql_query($query, $this->connection);
		}
	}
	/**
	 * Libera il risultato in memoria
	 * @param resource $result Risultato di una query SELECT, SHOW, EXPLAIN o DESCRIBE eseguita tramite il metodo sqlQuery
	 * @return bool Returns true on success or false on failure.
	 */
	public function sqlFreeResult($result) {
		return ($this->dbmsType == \config\Config::DBMS_MYSQL ? \mysql_free_result($result) : \mssql_free_result($result));
	}
	/**
	 * Recupera una riga dal risultato caricandola in un array associativo o con chiavi numeriche
	 * @param resource $result Risultato di una query SELECT eseguita tramite il metodo sqlQuery
	 * @param bool $resultTypeNumerical [optional] Settato a true restituisce un'array con chiavi numeriche
	 * @return array
	 * Array di stringhe che corrispondono alla riga recuperata o false se non ci sono pie' righe nel risultato.
	 * Il tipo di array dipende dal parametro opzionale resultTypeNumerical
	 * Se resultTypeNumerical e' settato a true verre' restituito un'array con chiavi numeriche
	 * Se e' settato a false verre' restituito un'array associativo
	 */
	public function sqlFetchArray($result, $resultTypeNumerical=false) {
		if ($this->dbmsType == \config\Config::DBMS_MYSQL) {
			$resultType = $resultTypeNumerical ? \MYSQL_NUM : \MYSQL_ASSOC;
			return \mysql_fetch_array($result, $resultType);
		} else {
			$resultType = $resultTypeNumerical ? \MSSQL_NUM : \MSSQL_ASSOC;
			return \mssql_fetch_array($result, $resultType);
		}
	}
	/**
	 * Restituisce il numero di righe risultante da una query di aggiornamento come INSERT, UPDATE o DELETE eseguita tramite il metodo sqlQuery
	 * @return int Numero di righe coinvolte nell'esecuzione della query
	 */
	public function sqlAffectedRows() {
		return ($this->dbmsType == \config\Config::DBMS_MYSQL ? \mysql_affected_rows($this->connection) : \mssql_rows_affected($this->connection));
	}
	/**
	 * Restituisce il numero di righe risultante da una query SELECT, SHOW, DESCRIBE, EXPLAIN eseguita tramite il metodo sqlQuery
	 * @param resource $resource Risultato di una query SELECT eseguita tramite il metodo sqlQuery
	 * @return int Numero di righe del risultato o false in caso di errore
	 */
	public function sqlNumRows($resource) {
		return ($this->dbmsType == \config\Config::DBMS_MYSQL ? \mysql_num_rows($resource) : \mssql_num_rows($resource));
	}
	/**
	 * Recupera il messaggio di errore inviato dal DBMS
	 * @return string Restituisce l'ultima stringa di errore inviata dal DBMS o una stringa vuota in caso non si sia verificato nessun errore
	 */
	public function sqlError() {
		return ($this->dbmsType == \config\Config::DBMS_MYSQL ? \mysql_error($this->connection) : \mssql_get_last_message());
	}

	/**
	 * Restituisce l'ID generato dal DBMS MySql nell'ultima query
	 * @return integer l'ID generato per un campo AUTO_INCREMENT dalla precendete query, 0 se la precedente query non ha generato un valore AUTO_INCREMENT
	 */
	public function sqlLastInsertId() {
		if ($this->dbmsType == \config\Config::DBMS_MYSQL) {
			return \mysql_insert_id($this->connection);
		} else {
			throw new DataLayerException(__FILE__, __LINE__, null, "Impossibile recuperare l'ultimo ID generato su DBMS SqlServer");
		}
	}
	
	public function sqlRealEscapeStrings($string) {
		if ($this->dbmsType == \config\Config::DBMS_MYSQL) {
			return \mysql_real_escape_string(stripslashes($string));
		} else {
			return \mb_ereg_replace("'", "''", $string);
		}
	}


	protected function setDBMS($dbms) {
		switch ($dbms) {
			case \config\Config::DBMS_MYSQL:
			case \config\Config::DBMS_MSSQL:
				$this->dbmsType = $dbms;
				break;
			default:
				throw new \system\InternalErrorException('Invalid DBMS.');
				break;
		}
	}

	/* ---------------------------------------------
	 * Metodi pubblici per l'esecuzione di query
	 * --------------------------------------------- */

	/**
	 * Esegue la query e restituisce il valore della prima colonna della prima riga
	 * o null nel caso la query non abbia prodotto alcun risultato.
	 * <p>Solleva l'eccezione DataLayerException
	 * se l'esecuzione della query non va a buon fine.</p>
	 * @param string $query Query sql SELECT
	 * @param string $file costante __FILE__
	 * @param int $line costante __LINE__
	 * @return mixed Risultato scalare
	 * @throws DataLayerException
	 */
	public function executeScalar($query, $file, $line) {
		$res = $this->sqlQuery($query);
		if (!$res) {
			throw new DataLayerException($file, $line, $query, $this->sqlError());
		}
		$arr = $this->sqlFetchArray($res, true);
		if (!$arr || empty($arr[0])) {
			\system\Log::add(\system\Lang::translate("No results for the previous query.") . "<br/>");
			return null;
		}
		$x = $arr[0];
		$this->sqlFreeResult($res);
		\system\Log::add(\system\Lang::translate("Query result: <em>@value</em>.", array('@value' => $x)) . "<br/>");
	
		return $x;
	}

	/**
	 * Esegue la query e restituisce la prima prima riga restituita
	 * o null nel caso la query non abbia prodotto alcun risultato.
	 * <p>Solleva l'eccezione DataLayerException
	 * se l'esecuzione della query non va a buon fine.</p>
	 * @param string $query Query sql SELECT
	 * @param string $file costante __FILE__
	 * @param int $line costante __LINE__
	 * @return array Risultato scalare
	 * @throws DataLayerException
	 */
	public function executeRow($query, $file, $line) {
		$res = $this->sqlQuery($query);
		if (!$res) {
			throw new DataLayerException($file, $line, $query, $this->sqlError());
		}
		$arr = $this->sqlFetchArray($res, true);
		if (!$arr) {
			\system\Log::add(\system\Lang::translate("No results for the previous query.") . "<br/>");
			return null;
		}
		$x = $arr;
		$this->sqlFreeResult($res);
		\system\Log::add(\system\Lang::translate("Query result: <em>@value</em>.", array('@value' => print_r($x, true))) . "<br/>");
	
		return $x;
	}

	/**
	 * Esegue la query e ne restituisce il risultato.
	 * <p>Solleva l'eccezione DataLayerException
	 * se l'esecuzione della query non va a buon fine.</p>
	 * @param string $query Query sql SELECT
	 * @param string $file costante __FILE__
	 * @param int $line costante __LINE__
	 * @return resource Sql resource
	 * @throws DataLayerException
	 */
	public function executeQuery($query, $file, $line) {
		$res = $this->sqlQuery($query);
		if (!$res) {
			throw new DataLayerException($file, $line, $query, $this->sqlError());
		}
		return $res;
	}
	
	public function executeQueryArray($query, $file, $line) {
		$result = $this->executeQuery($query, $file, $line);
		$x = array();
		\system\Utils::log('DataLayer', print_r($x, TRUE));
		while ($arr = $this->sqlFetchArray($result)) {
			\system\Utils::log('DataLayer', print_r($x, TRUE));
			$x[] = $arr;
		}
		\system\Utils::log('DataLayer', print_r($x, TRUE));
		return $x;
	}

	/**
	 * Esegue la query di aggiornamento (INSERT, UPDATE o DELETE)
	 * e restituisce il numero di righe interessate dall'aggiornamento.
	 * <p>Solleva l'eccezione DataLayerException
	 * se l'esecuzione della query non va a buon fine.</p>
	 * @param string $query Query sql SELECT
	 * @param string $file costante __FILE__
	 * @param int $line costante __LINE__
	 * @return int Numero di righe interessate dall'aggiornamento
	 * @throws DataLayerException
	 */
	public function executeUpdate($query, $file, $line) {
		$res = $this->sqlQuery($query);
		if (!$res) {
			throw new DataLayerException($file, $line, $query, $this->sqlError());
		}
		$nRows = $this->sqlAffectedRows();
		return $nRows;
	}
	
	public function beginTransaction() {
		if (!$this->transactionActive) {
			if ($this->dbmsType == \config\Config::DBMS_MYSQL) {
				$this->executeQuery("BEGIN", __FILE__, __LINE__);
			} else {
				$this->executeQuery("BEGIN TRANSACTION", __FILE__, __LINE__);
			}
			$this->transactionActive = true;
		}
	}
	
	public function commitTransaction() {
		if ($this->transactionActive) {
			if ($this->dbmsType == \config\Config::DBMS_MYSQL) {
				$this->executeQuery("COMMIT", __FILE__, __LINE__);
			} else {
				$this->executeQuery("COMMIT TRANSACTION", __FILE__, __LINE__);
			}
			$this->transactionActive = false;
		}
	}
	
	public function rollbackTransaction() {
		if ($this->transactionActive) {
			if ($this->dbmsType == \config\Config::DBMS_MYSQL) {
				$this->executeQuery("ROLLBACK", __FILE__, __LINE__);
			} else {
				$this->executeQuery("ROLLBACK TRANSACTION", __FILE__, __LINE__);
			}
			$this->transactionActive = false;
		}
	}
}
?>