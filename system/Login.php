<?php
namespace system;

define("LOGIN_COOKIE_TIME", (time()+(3600*24*5))); // 5 giorni


class Login {
	
	/**
	 * @var Login
	 */
	private static $login;
	
	private $user;

	private function __construct() {	}
	
	/**
	 * @return Login
	 */
	private static function getInstance() {
		if (\is_null(self::$login)) {
			self::$login = new self();
			
			self::$login->user = self::getLoginSession();

			if (!\is_null(self::$login->user)) { // login propagato da sessione
				// istruzioni per login da sessione
				self::$login->setLoginSession(); // salvo i dati di login nella sessione
			} else {
				self::$login->user = self::getLoginCookie();
				if (!\is_null(self::$login->user)) { // login propagato da cookie
					// istruzioni per login da cookie
					self::$login->user->last_login = \time();
					self::$login->user->update();
					self::$login->setLoginSession(); // salvo i dati di login nella sessione
					self::$login->setLoginCookie(); // refresh del cookie di login
				}
			}
		}
		return self::$login;
	}
	
	public static function isAnonymous() {
		return !self::getLoggedUserId();
	}
	
	public static function isLogged() {
		return !self::isAnonymous();
	}
	
	public static function isSuperuser() {
		return self::getLoggedUserId() == 1;
	}
	
	public static function getLoggedUser() {
		return self::getInstance()->user;
	}
	
	public static function getLoggedUserId() {
		return self::getLoggedUser() ? self::getLoggedUser()->id : 0;
	}
	
	/**
	 * Controlla che esista un utente corrispondente all'username e la userpass (criptate) passate al metodo
	 * e restituisce l'oggetto di tipo Login corrispondente all'utente selezionato / null in caso i dati siano errati
	 * @param String $cryptedEmail username criptata
	 * @param String $cryptedPassword userpass criptata
	 * @return Login oggetto di tipo login inizializzato / null se i dati non sono validi
	 */
	private static function getUserByLoginData($cryptedEmail, $cryptedPassword) {
		$rsb = new model\RecordsetBuilder('user');
		$rsb->usingAll();
		
		$rsb->setFilter(new model\FilterClauseGroup(
			new \system\model\FilterClause($rsb->password, "=", $cryptedPassword),
			"AND",
			new \system\model\CustomClause("MD5(LOWER(" . $rsb->email->getSelectExpression() . ")) = " . \system\model\MetaString::stdProg2Db($cryptedEmail))
		));
		
		return $rsb->selectFirst();
	}

	/* ---------------------------------------- *
	 *  salvataggio e rimozione della login
	 *  nella sessione e nei cookie
	 * ---------------------------------------- */
	/**
	 * Scrive i dati dell'utente loggato nella sessione
	 */
	private function setLoginSession() {
		$_SESSION["login"] = array(
			'id' => $this->user->id,
			'username' => \md5($this->user->email),
			'userpass' => $this->user->password, // already crypted
			'ip' => \system\HTMLHelpers::getIpAddress()
		);
	}

	/**
	 * Invia un cookie verso il client con i dati dell'utente loggato
	 */
	private function setLoginCookie() {
		$contents = \md5($this->user->email) . "%%" . $this->user->password;
		$domains = \array_reverse(\explode(".", $_SERVER["HTTP_HOST"]));
		\setcookie("login", $contents, LOGIN_COOKIE_TIME, "/", \count($domains) >= 3 ? "." . $domains[1] . '.' . $domains[0] : "");
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
		$domains = \array_reverse(\explode(".", $_SERVER["HTTP_HOST"]));
		\setcookie("login", "", time()-3600, "/", \count($domains) >= 3 ? "." . $domains[1] . '.' . $domains[0] : "");
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
				throw new LoginException(\system\Lang::translate('You seem to be logged in from an other ip address. Please try to log in again later.'));
			} else {
				return self::getUserByLoginData(@$_SESSION["login"]["username"], @$_SESSION["login"]["userpass"]);
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
				return self::getUserByLoginData($cryptedEmail, $cryptedPassword);
			}
		}
		return null;
	}

	/**
	 * Valida il login utilizzando (se esistono) i dati postati dal form di login
	 * @return User
	 */
	public static function login() {
		// restituisce true se sono stati propagati correttamente i dati di login dal form
		//  in tal caso, l'oggetto conterr� tutti i valori dei campi sql dell'utente loggato
		//  se richiesto i dati di login vengono salvati attraverso SetLoginCookie
		//  inoltre l'array $this->services conterr� l'id di tutti i servizi ai quali � associato l'utente
		
		if (!self::isAnonymous()) {
			return self::getInstance()->user;
//			throw new LoginException(\system\Lang::translate('You are already logged in.'));
		}
		else if (!isset($_COOKIE['PHPSESSID'])) { // Per il login i cookie devono essere abilitati.
			throw new LoginException(\system\Lang::translate('Your browser does not support cookies. Please enable cookies in order to log in.'));
		}
		else if (!\array_key_exists("username", $_POST) || $_POST["username"] == "") {
			throw new LoginException(\system\Lang::translate("Email not sent."));
		}
		else if (!\array_key_exists("userpass", $_POST) || $_POST["userpass"] == "") {
			throw new LoginException(\system\Lang::translate("Password not sent."));
		}
		
		else {
			self::getInstance()->user = self::getUserByLoginData(\md5(\strtolower($_POST["username"])), \md5($_POST["userpass"]));
			
			if (!\is_null(self::getInstance()->user)) {
				self::getInstance()->user->last_login = \time();
				self::getInstance()->user->update();
				
				self::getInstance()->setLoginSession(); // salvo i dati di login nella sessione
				if (!\array_key_exists("remember", $_POST)) { // salvo i dati di login nei cookie (se richiesto dall'utente)
					self::getInstance()->setLoginCookie();
				}
				return self::getInstance()->user;
			}
			else {
				throw new LoginException(\system\Lang::translate('Wrong username or password.'));
			}
		}
	}

	public static function logout() {
		if (!self::isAnonymous()) {
			self::getInstance()->unsetLoginCookie();
			self::getInstance()->unsetLoginSession();
			self::getInstance()->user = null;
		}
	}

//	public static function genPassword() {
//		return \substr(\md5(\date("st")),0,8); // userpass generata in maniera casuale
//	}

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
