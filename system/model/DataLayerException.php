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
		parent::__construct(\system\Lang::translate("Data access error."), 0, null);
		if (!empty($file)) {
			$this->file = $file;
		}
		if (!empty($line)) {
			$this->line = $line;
		}
		$this->query = $query;
		$this->sqlError = $sqlError;
		$this->action = $action;
		$this->message = $this->getTxtMessage();
	}
	
	public function getAction() {
		return $this->action;
	}

	public function getTxtMessage() {
		if (empty($this->message)) {
			$this->message =
				\system\Lang::translate("Data access error.") . "\r\n" .
				"\r\nFile ". (!empty($this->file) ? $this->file : "?") .", Line ". (!empty($this->line) ? $this->line : "?") .".\r\n\r\n";

			switch ($this->action) {
				case DataLayerException::ACTION_CONNECTION:
					$this->message .= \system\Lang::translate("Unable to connect to the DBMS.") . "\r\n";
					break;
				case DataLayerException::ACTION_DB_SELECTION:
					$this->message .= \system\Lang::translate("Unable to connect to the DBMS.") . "\r\n";
					break;
				case DataLayerException::ACTION_RETRIEVE_ID:
					$this->message .= \system\Lang::translate("Unable to retrieve the last inserted id.") . "\r\n";
					break;
				default:
					break;
			}
			if (!empty($this->sqlError)) {
				$this->message .= \system\Lang::translate("Sql error details: @details", array('@details' => $this->sqlError)) . "\r\n\r\n";
			}
			if (!empty($this->query)) {
				$this->message .= \system\Lang::translate("Sql query: @query", array('@query' => $this->query)) . "\r\n\r\n";
			}
		}
		return $this->message;
	}

	public function getHtmlMessage() {
		if (empty($this->htmlMessage)) {
			$this->htmlMessage =
				"<h3>" . \system\Lang::translate("Data access error.") . "</h3>" .
				"<p>File ". (!empty($this->file) ? $this->file : "?") .", line ". (!empty($this->line) ? $this->line : "?") .".</p>";

			switch ($this->action) {
				case DataLayerException::ACTION_CONNECTION:
					$this->htmlMessage .= "<p>" . \system\Lang::translate("Unable to connect to the DBMS.") . "</p>";
					break;
				case DataLayerException::ACTION_DB_SELECTION:
					$this->htmlMessage .= "<p>" . \system\Lang::translate("Unable to connect to the DBMS.") . "</p>";
					break;
				case DataLayerException::ACTION_RETRIEVE_ID:
					$this->htmlMessage .= "<p>" . \system\Lang::translate("Unable to retrieve the last inserted id.") . "</p>";
					break;
				default:
					break;
			}
			if (!empty($this->sqlError)) {
				$this->htmlMessage .= "<p>" . \system\Lang::translate("Sql error details: @details", array('@details' => $this->sqlError)) . "</p>";
			}
			if (!empty($this->query)) {
				$this->htmlMessage .= "<p>" . \system\Lang::translate("Sql query: @query", array('@query' => $this->query)) . "</p>";
			}
		}
		return $this->htmlMessage;
	}
}
?>