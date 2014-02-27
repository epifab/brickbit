<?php
namespace system\utils;

use system\exceptions\LoginError;

define("LOGIN_COOKIE_TIME", (time()+(3600*24*5))); // 5 giorni


class Login {
  
  /**
   * @var Login
   */
  private static $login;
  
  private $user;
  
  private static $users = array();
  private static $usersByEmail = array();

  private function __construct() {  }
  
  /**
   * @return Login
   */
  private static function getInstance() {
    if (\is_null(self::$login)) {
      self::$login = new self();
      
      self::$login->user = self::getLoginSession();

      if (!\is_null(self::$login->user)) {
        // login via session
        self::$login->setLoginSession();
      }
      else {
        // login via cookie
        self::$login->user = self::getLoginCookie();
        
        if (!\is_null(self::$login->user)) {
          // update user last login
          self::$login->user->last_login = \time();
          self::$login->user->update();
          self::$login->setLoginSession(); // set login session
          self::$login->setLoginCookie(); // set login cookie
        }
        else {
          // not logged in
          self::$login->user = self::getAnonymousUser();
        }
      }
    }
    return self::$login;
  }
  
  public static function isAnonymous() {
    return self::getLoggedUserId() == 0;
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
    return !self::getLoggedUser() ? 0 : self::getLoggedUser()->id;
  }
  
  public static function getAnonymousUser() {
    static $rs = null;
    if (!$rs) {
      $rsb = new \system\model\RecordsetBuilder('user');
      $rsb->usingAll();
      $rs = $rsb->newRecordset();
    }
    return $rs;
  }
  
  /**
   * Controlla che esista un utente corrispondente all'username e la userpass (criptate) passate al metodo
   * e restituisce l'oggetto di tipo Login corrispondente all'utente selezionato / null in caso i dati siano errati
   * @param String $cryptedEmail username criptata
   * @param String $cryptedPassword userpass criptata
   * @return Login oggetto di tipo login inizializzato / null se i dati non sono validi
   */
  private static function getUserByLoginData($cryptedEmail, $cryptedPassword) {
    if (\array_key_exists($cryptedEmail, self::$usersByEmail)) {
      if (self::$usersByEmail[$cryptedEmail]->password == $cryptedPassword) {
        return self::$usersByEmail[$cryptedEmail];
      } else {
        return null;
      }
    } else {
      $rsb = new \system\model\RecordsetBuilder('user');
      $rsb->usingAll();

      $rsb->setFilter(new \system\model\FilterClauseGroup(
        new \system\model\FilterClause($rsb->password, "=", $cryptedPassword),
        "AND",
        new \system\model\CustomClause("MD5(LOWER(" . $rsb->email->getSelectExpression() . ")) = " . \system\metatypes\MetaString::stdProg2Db($cryptedEmail))
      ));

      $user = $rsb->selectFirst();
      if ($user) {
        self::$users[$user->id] = $user;
        self::$usersByEmail[$cryptedEmail] = $user;
        return $user;
      } else {
        return null;
      }
    }
  }
  
  public static function getUser($uid, $reset=false) {
    if (!$reset && \array_key_exists($uid, self::$users)) {
      return self::$users[$uid];
    }
    else {
      $rsb = new \system\model\RecordsetBuilder('user');
      $rsb->usingAll();
      self::$users[$uid] = $rsb->selectFirstBy(array('id' => $uid));
      if (self::$users[$uid]) {
        self::$usersByEmail[self::$users[$uid]->email] = self::$users[$uid];
      }
    }
    return self::$users[$uid];
  }
  
  /**
   * Saves login session
   */
  private function setLoginSession() {
    Utils::setSession('system', 'login', array(
      'id' => $this->user->id,
      'username' => \md5(\strtolower($this->user->email)), // crypt email
      'userpass' => $this->user->password, // already crypted
      'ip' => \system\utils\HTMLHelpers::getIpAddress()
    ));
  }

  /**
   * Destroys login session
   */
  private static function unsetLoginSession() {
    Utils::unsetSession('system', 'login');
  }

  /**
   * Saves login cookie
   */
  private function setLoginCookie() {
    $contents = \md5(\strtolower($this->user->email)) . "%%" . $this->user->password;
    $domains = \array_reverse(\explode(".", $_SERVER["HTTP_HOST"]));
    // localhost          => localhost
    // ciderbit.local     => ciderbit.local
    // www.ciderbit.local => ciderbit.local
    \setcookie("login", $contents, LOGIN_COOKIE_TIME, "/", \count($domains) == 1 ? $domains[0] : $domains[1] . '.' . $domains[0]);
  }

  /**
   * Destroys login cookie
   */
  private static function unsetLoginCookie() {
    $domains = \array_reverse(\explode('.', $_SERVER['HTTP_HOST']));
    // localhost          => localhost
    // ciderbit.local     => ciderbit.local
    // www.ciderbit.local => ciderbit.local
    \setcookie("login", "", time()-3600, "/", count($domains) == 1 ? $domains[0] : $domains[1] . '.' . $domains[0]);
  }

  /**
   * Login via session
   * @return Login The login object or null for unexisting or invalid login 
   *  session data
   */
  private static function getLoginSession() {
    $loginSession = Utils::getSession('system', 'login', array());
    if (!empty($loginSession)) {
      if ($loginSession['ip'] != HTMLHelpers::getIpAddress()) {
        \system\utils\Log::create('login', 'User <em>@id</em> seems to be already logged in from two different ip address.', array('@name' => $loginSession['id']), \system\LOG_NOTICE);
      }
      else {
        return self::getUserByLoginData($loginSession['username'], $loginSession['userpass']);
      }
    }
    return null;
  }

  /**
   * Login via cookie
   * @return Login The login object or null for unexisting or invalid login
   *  cookie data
   */
  private static function getLoginCookie() {
    if (\array_key_exists('login', $_COOKIE)) {
      $cryptedEmail = \strtok($_COOKIE['login'], '%%'); // username criptata
      $cryptedPassword = \strtok('%%'); // userpass criptata
      if (!empty($cryptedEmail) && !empty($cryptedPassword)) {
        return self::getUserByLoginData($cryptedEmail, $cryptedPassword);
      }
    }
    return null;
  }

  /**
   * Validate login input data.
   * @return \system\model\RecordsetInterface The user object if login data are 
   *  correct, null otherwise
   */
  public static function login($loginData) {
    if (!self::isAnonymous()) {
      return self::getInstance()->user;
    }
    else if (!isset($_COOKIE['PHPSESSID'])) { // Per il login i cookie devono essere abilitati.
      throw new LoginError('Your browser does not support cookies. Please enable cookies in order to log in.');
    }
    else if (!\array_key_exists('name', $loginData) || $loginData['name'] == '') {
      throw new LoginError('Email not sent.');
    }
    else if (!\array_key_exists('pass', $loginData) || $loginData['pass'] == '') {
      throw new LoginError('Password not sent.');
    }
    
    else {
      self::getInstance()->user = self::getUserByLoginData(\md5(\strtolower($loginData['name'])), \md5($loginData['pass']));
      
      if (!\is_null(self::getInstance()->user)) {
        self::getInstance()->user->last_login = \time();
        self::getInstance()->user->update();
        
        self::getInstance()->setLoginSession(); // salvo i dati di login nella sessione
        if (!\array_key_exists('remember', $loginData)) { // salvo i dati di login nei cookie (se richiesto dall'utente)
          self::getInstance()->setLoginCookie();
        }
        return self::getInstance()->user;
      }
      else {
        throw new LoginError('Wrong username or password.');
      }
    }
  }
  
  /**
   * Changes the logged in user
   * @param \system\model\RecordsetInterface $user User object
   */
  public static function forceLogin($user) {
    self::getInstance()->user = $user;
    self::getInstance()->setLoginSession();
    self::unsetLoginCookie();
  }

  /**
   * Logout
   */
  public static function logout() {
    if (!self::isAnonymous()) {
      self::unsetLoginCookie();
      self::unsetLoginSession();
      self::getInstance()->user = null;
    }
  }

//  public static function genPassword() {
//    return \substr(\md5(\date("st")),0,8); // userpass generata in maniera casuale
//  }

//  private function sendNewPassword($userpass, $usernameAddress) {
//    $username = new Email();
//
//    $username->setTo($usernameAddress);
//    $username->setSubject("username and userpass for login");
//
//    $msg = "Username and userpass for login:\r\n";
//    $msg .= " Email: ". $this->username ."\r\n";
//    $msg .= " Password: ". $userpass ."\r\n\r\n";
//    $msg .= "This username is being sent to you because of a request to became a versolafine.it member.\r\n";
//    $msg .= "If this message was sent in error, please disregard it and no further username will be sent to you on this subject.\r\n\r\n";
//    $msg .= "Thanks and welcome!";
//
//    $username->Body($msg);
//
//    return $username->Send();
//  }
}
