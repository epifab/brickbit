<?php
namespace system\exceptions;

use system\utils\SqlFormatter;

/**
 * Classe per la gestione di eccezioni verificatesi sullo strato di accesso ai dati.
 * @author Fabio Epifani
 */
class SqlQueryError extends DataLayerError {
  
  const ACTION_STANDARD = 0;
  const ACTION_CONNECTION = 1;
  const ACTION_DB_SELECTION = 2;
  const ACTION_RETRIEVE_ID = 3;
  const ACTION_NO_RESULTS = 4;

  private $action = SqlQueryError::ACTION_STANDARD;

  private $query = "";
  private $sqlError = "";

  public function __construct($query, $sqlError=null, $action=SqlQueryError::ACTION_STANDARD) {
    parent::__construct('Data access error');
    $this->query = $query;
    $this->sqlError = $sqlError;
    $this->action = $action;
  }
  
  public function getDetails() {
    $message = '';

    switch ($this->action) {
      case SqlQueryError::ACTION_CONNECTION:
        $message .= '<p>Unable to connect to the DBMS.</p>';
        break;
      case SqlQueryError::ACTION_DB_SELECTION:
        $message .= '<p>Unable to select the database.</p>';
        break;
      case SqlQueryError::ACTION_RETRIEVE_ID:
        $message .= '<p>Unable to retrieve the last inserted id.</p>';
        break;
      default:
        break;
    }
    if (!empty($this->sqlError)) {
      $message .= '<p>Sql error details: ' . $this->sqlError . '</p>';
    }
    if (!empty($this->query)) {
      $message .= '<p>Sql query:</p>' . SqlFormatter::format($this->query);
    }
    return $message;
  }
}
