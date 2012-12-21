<?php
namespace system;

class HTMLHelpers {
	public static function getTagUrl($x) {
		return \str_replace(' ', '_', $x);
	}
	
	public static function getIpAddress() {
		static $ipAddress = null;
		if (is_null($ipAddress)) {
			if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
				$ipAddress = $_SERVER['HTTP_CLIENT_IP'];
			} else if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
				$ipAddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
			} else {
				$ipAddress = $_SERVER['REMOTE_ADDR'];
			}
		}
		return $ipAddress;
	}

	/**
	 * Stampa le intestazioni html
	 * @param out
	 * @param title Titolo della pagina
	 */
	public static function printPageHeader($title) {
		echo '<?xml version="1.0" encoding="ISO-8859-1"?>' .
			 '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">' .
			 '<html xmlns="http://www.w3.org/1999/xhtml">' .
			 '<head><title>' . $title . '</title></head><body>';
	}

	/**
	 * Chiude i tag body e html
	 * @param out Print writer
	 */
	public static function printPageFooter() {
		echo '</body></html>';
	}

	public static function makeLoginErrorPage($templateManager, $datamodel, $message=null) {
		HTMLHelpers::makeErrorPage($templateManager, $datamodel, $message);
	}

	public static function isAjaxRequest() {
		return \array_key_exists("xmca_ajax_request", $_REQUEST) ||
				 (\array_key_exists('HTTP_X_REQUESTED_WITH', $_SERVER) && (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) || \strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) != 'xmlhttprequest'));
	}

	private static function printVar($arg) {
		$msg = '';
		if (\is_array($arg)) {
			$msg .= 'array(';
			$first = true;
			foreach ($arg as $k => $v) {
				$first ? $first = false : $msg .= ", ";
				$msg .= self::printVar($k) . " => " . self::printVar($v);
			}
			$msg .= ')';
		} else if (\is_object($arg)) {
			$msg .= '[object ' . get_class($arg) . ']';
		} else if (\is_null($arg)) {
			$msg .= 'null';
		} else if (\is_string($arg)) {
			$msg .= '"' . $arg . '"';
		} else {
			$msg .= $arg;
		}
		return $msg;
	}
	
	/**
	 * Genera una pagina HTML di errore, con un messaggio specificato dall'utente
	 * @param tpl Template manager
	 * @param message Messaggio di errore
	 * @param out Print writer
	 */
	public static function makeErrorPage(TemplateManager $templateManager, $datamodel, $mainException, $executionTime=0) {
		$msg = "";
		
		$exception = $mainException;
		
		if ($exception->getCode() != ErrorCodes::AUTHORIZATION) {
			while ($exception != null) {
				if (\method_exists($exception, "getHtmlMessage")) {
					$msg .= $exception->getHtmlMessage();
				}
				else {
					$msg .=
						'<h3>' . $exception->getMessage() . '</h3>'
						. '<h4>Dettagli eccezione</h4>'
						. '<p>' . $exception->getFile() . ' ' . $exception->getLine() . '</p>';

					$trace = $exception->getTrace();

					if (count($trace) > 0) {
						$msg .= '<ol>';

						$i = count($trace);

						foreach ($trace as $t) {
							$msg .= '<li value="' . $i . '"><p><code>';

							$i--;

							if (array_key_exists('class', $t) && !empty($t['class'])) {
								$msg .= $t['class'] . '->';
							}
							$msg .= '<b>' . $t['function'] . '</b>(';

							$first = true;

							if (\array_key_exists("args", $t)) {
								foreach ($t['args'] as $arg) {
									$first ? $first = false : $msg .= ', ';
									$msg .= self::printVar($arg);
								}
							}
							$msg .= '</code>)<br/> ' . @$t['file'] . ' ' . @$t['line'] . '</p></li>';
						}
						$msg .= '</ol>';
					}
				}
				$exception = $exception->getPrevious();
			}
			if ($executionTime > 0) {
				$msg .= '<p>Tempo di esecuzione: ';
				if ($executionTime < 1) {
					$msg .= round($executionTime * 1000, 0) . ' ms.</p>';
				} else {
					$msg .= $executionTime . ' sec.</p>';
				}
			}
		}
		
		switch ($mainException->getCode()) {
			case ErrorCodes::AUTHORIZATION:
				$title = "Permessi non sufficienti";
				$msg = "&Egrave; necessario effettuare il login prima di accedere a questo contenuto";
				break;
			
			default:
				$title = "Errore interno dello script";
				$msg .= \system\Log::get();
				break;
		}
		
		$datamodel["errorTitle"] = $title;
		$datamodel["errorMessage"] = $msg;
		$datamodel["url"] = "Error";

		try {
			
			$templateManager->setMainTemplate("error");
			
			$templateManager->process($datamodel);
			
		} catch (TemplateManagerException $ex) {
			
			// Non e' stato trovato il template di errore
			
			if (HTMLHelpers::isAjaxRequest()) {

				echo '<div class="fatal_error"><h2>' . $title . '</h2>'. $msg .'</div>';

			} else {

				HTMLHelpers::printPageHeader("Errore");
				echo '<div class="fatal_error"><h2>' . $title . '</h2>'. $msg .'</div>';
				HTMLHelpers::printPageFooter();

			}
		}
	}
	
//	public static function makeValidationErrorPage(TemplateManager $templateManager, $datamodel) {
//		
//		$datamodel["url"] = "Error";
//
//		try {
//			
//			$templateManager->process("layout/Forbidden", $datamodel);
//			
//		} catch (TemplateManagerException $ex) {
//			
//			// Non e' stato trovato il template di errore
//			
//			if (HTMLHelpers::isAjaxRequest()) {
//
//				echo '<div class="fatal_error"><h2>' . $title . '</h2>'. $msg .'</div>';
//
//			} else {
//
//				HTMLHelpers::printPageHeader("Errore");
//				echo '<div class="fatal_error"><h2>' . $title . '</h2>'. $msg .'</div>';
//				HTMLHelpers::printPageFooter();
//
//			}
//		}
//	}
}
?>