<?php
namespace system;

class ErrorCodes {
	/**
	 * Errore dovuto agli argomenti inviati al server
	 */
	const REQUEST_ARGUMENT = 1;
	/**
	 * Errore dovuto al livello di sicurezza
	 */
	const AUTHORIZATION = 2;
	/**
	 * Errore di accesso ai dati
	 */
	const DATA_LAYER = 3;
	/**
	 * Errore di visualizzazione di un template
	 */
	const TEMPLATE = 4;
	/**
	 * Errore interno generico dello script
	 */
	const INTERNAL = 5;
}
?>