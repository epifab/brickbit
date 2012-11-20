<?php
namespace system\model;

/**
 * Classe per la gestione di eccezioni verificatesi sullo strato di accesso ai dati.
 * @author Fabio Epifani
 */
class DataLayerException extends \Exception {
	const ACTION_STANDARD = 0;
	const ACTION_CONNECTION = 1;
	const ACTION_DB_SELECTION = 2;
	const ACTION_RETRIEVE_ID = 3;
	const ACTION_NO_RESULTS = 4;

	private $action = DataLayerException::ACTION_STANDARD;

	private $query = "";
	private $sqlError = "";

	private $htmlMessage = "";

	public function __construct($file, $line, $query, $sqlError=null, $action=DataLayerException::ACTION_STANDARD) {
		parent::__construct("Errore di accesso ai dati", 0, null);
		if (!empty($file)) {
			$this->file = $file;
		}
		if (!empty($line)) {
			$this->line = $line;
		}
		$this->query = $query;
		$this->sqlError = $sqlError;
		$this->action = $action;
	}
	
	public function getAction() {
		return $this->action;
	}

	public function getTxtMessage() {
		if (empty($this->message)) {
			$this->message =
				"Errore di accesso ai dati.\r\n" .
				"\r\nFile ". (!empty($this->file) ? $this->file : "?") .", Line ". (!empty($this->line) ? $this->line : "?") .".\r\n\r\n";

			switch ($this->action) {
				case DataLayerException::ACTION_CONNECTION:
					$this->message .= "Impossibile connettersi con il DBMS.\r\n";
					break;
				case DataLayerException::ACTION_DB_SELECTION:
					$this->message .= "Impossibile connettersi al database.\r\n";
					break;
				case DataLayerException::ACTION_RETRIEVE_ID:
					$this->message .= "Impossibile recuperare l'ID dell'ultimo record inserito.\r\n";
					break;
				default:
					break;
			}
			if (!empty($this->sqlError)) {
				$this->message .= "Dettagli errore: " . $this->sqlError . "\r\n\r\n";
			}
			if (!empty($this->query)) {
				$this->message .= "Dettagli della query che ha generato l'errore:\r\n" . $query . "\r\n";
			}
		}
		return $this->message;
	}

	public function getHtmlMessage() {
		if (empty($this->htmlMessage)) {
			$this->htmlMessage = 
				"<h3>Errore di accesso ai dati.</h3>\r\n\r\n" .
				"<p>File ". (!empty($this->file) ? $this->file : "Sconosciuta") .", Line: ". (!empty($this->line) ? $this->line : "Sconosciuto") .".</p>\r\n\r\n";

			switch ($this->action) {
				case DataLayerException::ACTION_CONNECTION:
					$this->htmlMessage .= "<p>Impossibile connettersi con il DBMS.</p>\r\n";
					break;
				case DataLayerException::ACTION_DB_SELECTION:
					$this->htmlMessage .= "<p>Impossibile connettersi al database.</p>\r\n";
					break;
				case DataLayerException::ACTION_RETRIEVE_ID:
					$this->htmlMessage .= "<p>Impossibile recuperare l'ID dell'ultimo record inserito.</p>\r\n";
					break;
				default:
					break;
			}
			if (!empty($this->sqlError)) {
				$this->htmlMessage .= "<p><strong>Dettagli errore:</strong><br/>" . $this->sqlError . "</p>";
			}
			if (!empty($this->query)) {
				$this->htmlMessage .= "<p><strong>Dettagli della query che ha generato l'errore:</strong><br/><textarea rows=\"6\" cols=\"80\">" . \htmlentities($this->query, ENT_NOQUOTES, "UTF-8") . "</textarea></p>";
			}
		}
		return $this->htmlMessage;
	}
}
?>