<?php
namespace system\exceptions;

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
  
//  public function getTxtMessage() {
//    if (empty($this->message)) {
//      $this->message =
//        \system\utils\Lang::translate("Data access error.") . "\r\n" .
//        "\r\nFile ". (!empty($this->file) ? $this->file : "?") .", Line ". (!empty($this->line) ? $this->line : "?") .".\r\n\r\n";
//
//      switch ($this->action) {
//        case SqlQueryError::ACTION_CONNECTION:
//          $this->message .= \system\utils\Lang::translate("Unable to connect to the DBMS.") . "\r\n";
//          break;
//        case SqlQueryError::ACTION_DB_SELECTION:
//          $this->message .= \system\utils\Lang::translate("Unable to connect to the DBMS.") . "\r\n";
//          break;
//        case SqlQueryError::ACTION_RETRIEVE_ID:
//          $this->message .= \system\utils\Lang::translate("Unable to retrieve the last inserted id.") . "\r\n";
//          break;
//        default:
//          break;
//      }
//      if (!empty($this->sqlError)) {
//        $this->message .= \system\utils\Lang::translate("Sql error details: @details", array('@details' => $this->sqlError)) . "\r\n\r\n";
//      }
//      if (!empty($this->query)) {
//        $this->message .= \system\utils\Lang::translate("Sql query: @query", array('@query' => $this->query)) . "\r\n\r\n";
//      }
//    }
//    return $this->message;
//  }

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
      $message .= '<p>Sql query: ' . $this->query . '</p>';
    }
    return $message;
  }
}
