<?php
namespace system;

use module\core\model\XmcaUser;

define("LOGIN_COOKIE_TIME", (time()+(3600*24*5))); // 5 giorni


class Login {
	
	/**
	 * @var Login
	 */
	private static $login;
	
	private $id;
	private $username;
	private $userpass;
	
	private $user;

	private function __construct($id) {
		$this->id = $id;
	}
	
	/**
	 * @return Login
	 */
	public static function getInstance() {
		
		if (\is_null(Login::$login)) {
			Login::$login = Login::getLogin();
			
			if (\is_null(Login::$login)) {
				// Anonymous user
				Login::$login = new Login(0);
				Login::$login->username = '';
				Login::$login->userpass = '';
			}
		}
		return Login::$login;
	}
	
	public static function getLoggedUserId() {
		return Login::getInstance()->id;
	}
	
	public function isAnonymous() {
		return $this->id == 0;
	}
	
	public function getUser() {
		if (!$this->isAnonymous()) {
			if (\is_null($this->user)) {
				$userBuilder = new XmcaUser();
				$userBuilder->using(
					"id",
					"full_name",
					"last_login"
				);
				$userBuilder->setFilter(new FilterClause($userBuilder->searchMetaType("id"), "EQ", $this->getId()));
				$this->user = $userBuilder->selectFirst();
			}
			
			return $this->user;
		}
		return null;
	}
	
//	public static function checkEditPermission($componentName, $tableName, $recordId) {
//		return XmcaRecordMode::checkEditPermission(Login::getLoggedUserId(), $tableName, $recordId);
//	}
//
//	public static function checkReadPermission($componentName, $tableName, $recordId) {
//		$login = Login::getInstance();
//		if (\is_null($login)) {
//			return null;
//		}
//		return XmcaUser::checkReadPermission($login->id, $componentName, $tableName, $recordId);
//	}

	public function getId() {
		return $this->id;
	}

//	public static function saveLogin($id, $cryptedEmail, $cryptedPassword) {
//		$login = new Login();
//		$login->id = $id;
//		$login->username = $cryptedEmail;
//		$login->userpass = $cryptedPassword;
//		$login->setLoginSession();
//		return $login;
//	}

	/**
	 * Controlla che esista un utente corrispondente all'username e la userpass (criptate) passate al metodo
	 * e restituisce l'oggetto di tipo Login corrispondente all'utente selezionato / null in caso i dati siano errati
	 * @param String $cryptedEmail username criptata
	 * @param String $cryptedPassword userpass criptata
	 * @return Login oggetto di tipo login inizializzato / null se i dati non sono validi
	 */
	private static function getLoginByLoginData($cryptedEmail, $cryptedPassword) {
		
		$id = XmcaUser::getIdByLoginData($cryptedEmail, $cryptedPassword);
		
		if (\is_null($id)) {
			return null;
		}
		else {
			$login = new Login($id);
			
			$login->username = $cryptedEmail;
			$login->userpass = $cryptedPassword;
			
			return $login;
		}
	}

	/* ---------------------------------------- *
	 *  salvataggio e rimozione della login
	 *  nella sessione e nei cookie
	 * ---------------------------------------- */
	/**
	 * Scrive i dati dell'utente loggato nella sessione
	 */
	private function setLoginSession() {
		$_SESSION["login"] = array();
		$_SESSION["login"]["id"] = $this->id;
		$_SESSION["login"]["username"] = $this->username;
		$_SESSION["login"]["userpass"] = $this->userpass;
		$_SESSION["login"]["ip"] = HTMLHelpers::getIpAddress();
	}

	/**
	 * Invia un cookie verso il client con i dati dell'utente loggato
	 */
	private function setLoginCookie() {
		$contents = $this->username . "%%" . $this->userpass;
		\setcookie("login", $contents, LOGIN_COOKIE_TIME);
	}

	/**
	 * Elimina i dati della sessione riguardanti il login
	 */
	private static function unsetLoginSession() {
		unset($_SESSION["login"]);
	}

	/**
	 * Elimina il cookie riguardante i dati di login
	 */
	private static function unsetLoginCookie() {
		\setcookie("login", "", time()-3600);
	}

	/* ---------------------------------------- *
	 *  controllo e recupero dei dati della login
	 *  da sessione, cookie o form
	 * ---------------------------------------- */
	/**
	 * Valida il login utilizzando (se esistono) i dati scritti nella sessione
	 * @return Login restituisce l'oggetto login corrispondente ai dati della sessione / null se i dati non esistono o non sono validi
	 */
	private static function getLoginSession() {
		// restituisce true se sono stati propagati correttamente i dati di login da sessione
		//  in tal caso, l'oggetto conterra' tutti i valori dei campi sql dell'utente loggato

		if (\array_key_exists("login", $_SESSION)) {

			if (@$_SESSION["login"]["ip"] != HTMLHelpers::getIpAddress()) {
				throw new LoginException("Al momento risulti connesso con un altro indirizzo IP. Sei pregato di rieffettuare il login.");
			} else {
				return Login::getLoginByLoginData(@$_SESSION["login"]["username"], @$_SESSION["login"]["userpass"]);
			}
		}
		return null;
	}

	/**
	 * Valida il login utilizzando (se esistono) i dati scritti nel cookie
	 * @return Login restituisce l'oggetto login corrispondente ai dati del cookie / null se i dati non esistono o non sono validi
	 */
	private static function getLoginCookie() {
		if (\array_key_exists("login", $_COOKIE)) {
			$cryptedEmail = \strtok($_COOKIE["login"], "%%"); // username criptata
			$cryptedPassword = \strtok("%%"); // userpass criptata
			if ($cryptedEmail != "" && $cryptedPassword != "") {
				return Login::getLoginByLoginData($cryptedEmail, $cryptedPassword);
			}
		}
		return null;
	}

	/**
	 * Valida il login utilizzando (se esistono) i dati postati dal form di login
	 * @return Login restituisce l'oggetto login corrispondente ai dati del form / null se i dati non esistono o non sono validi
	 */
	public static function getPostedLogin() {
		// restituisce true se sono stati propagati correttamente i dati di login dal form
		//  in tal caso, l'oggetto conterr� tutti i valori dei campi sql dell'utente loggato
		//  se richiesto i dati di login vengono salvati attraverso SetLoginCookie
		//  inoltre l'array $this->services conterr� l'id di tutti i servizi ai quali � associato l'utente

		if (!isset($_COOKIE['PHPSESSID'])) { // Per il login i cookie devono essere abilitati.
			throw new LoginException("Il tuo browser non supporta i cookie. Abilita i cookie per effettuare il login.");
			return false;
		}
		else if (!\array_key_exists("username", $_POST) || $_POST["username"] == "") {
			throw new LoginException("Email non trasmessa");
			return false;
		}
		else if (!\array_key_exists("userpass", $_POST) || $_POST["userpass"] == "") {
			throw new LoginException("Password non trasmessa");
			return false;
		}
		
		else {
			$login = Login::getLoginByLoginData(\md5(\strtolower($_POST["username"])), \md5($_POST["userpass"]));
			
			if (!\is_null($login)) {
				// Username e userpass corretti.
				XmcaUser::updateLastLogin($login->id); // aggiorno data ultimo login
				
				$login->setLoginSession(); // salvo i dati di login nella sessione
				if (!\array_key_exists("remember", $_POST)) { // salvo i dati di login nei cookie (se richiesto dall'utente)
					$login->setLoginCookie();
				}
				
				self::$login = $login;
				
				return $login;
			}
			else {
				throw new LoginException("Indirizzo email o password non validi");
			}
		}
	}

	/**
	 * Recupera i dati dell'utente loggato (se esiste)
	 * @return Login oggetto login / null se l'utente non è loggato
	 */
	private static function getLogin() {
		$login = Login::getLoginSession();
		if (!\is_null($login)) { // login propagato da sessione
			// istruzioni per login da sessione
			$login->setLoginSession(); // salvo i dati di login nella sessione
		} else {
			$login = Login::getLoginCookie();
			if (!\is_null($login)) { // login propagato da cookie
				// istruzioni per login da cookie
				XmcaUser::updateLastLogin($login->getId()); // aggiorno data ultimo login
				$login->setLoginSession(); // salvo i dati di login nella sessione
				$login->setLoginCookie(); // refresh del cookie di login
			}
		}

		return $login;
	}

	public function logout() {
		$this->unsetLoginCookie();
		$this->unsetLoginSession();
		
		$this->id = 0;
		$this->username = '';
		$this->userpass = '';
	}

	public static function genPassword() {
		return substr(md5(date("st")),0,8); // userpass generata in maniera casuale
	}

//	private function sendNewPassword($userpass, $usernameAddress) {
//		$username = new Email();
//
//		$username->setTo($usernameAddress);
//		$username->setSubject("username and userpass for login");
//
//		$msg = "Username and userpass for login:\r\n";
//		$msg .= " Email: ". $this->username ."\r\n";
//		$msg .= " Password: ". $userpass ."\r\n\r\n";
//		$msg .= "This username is being sent to you because of a request to became a versolafine.it member.\r\n";
//		$msg .= "If this message was sent in error, please disregard it and no further username will be sent to you on this subject.\r\n\r\n";
//		$msg .= "Thanks and welcome!";
//
//		$username->Body($msg);
//
//		return $username->Send();
//	}
}
?>
