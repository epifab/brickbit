<?php
namespace system\utils;
/*
 * How the lang system work:
 *  
 * $vocabulary = array (
 *   'You eat @numero sweets' => function($args) {
 *     if ($args["@numero"] == 0) {
 *       return 'Non hai mangiato nessun dolcetto';
 *     } else if ($args["@numero"] == 1) {
 *       return 'Hai mangiato un dolcetto';
 *     } else {
 *       return 'Hai mangiato ' . $args["@numero"] . ' dolcetti';
 *     }
 *   },
 *   'Hello @user! How is it going?' => 'Ciao @user! Come stai?',
 * );
 */

class Lang {
  private static $lang = null;
  private static $vocabulary = null;
  
  private static function initLang($langId = null) {
    self::$lang = empty($langId)
      ? \system\Main::session('system', 'lang', \system\Main::setting('defaultLang'))
      : $langId;
    
    $callback = array('\\lang\\' . \ucfirst($langId), 'vocabulary');
    if (\is_callable($callback)) {
      self::$vocabulary = \call_user_func($callback);
    }
    if (!\is_array(self::$vocabulary)) {
      self::$vocabulary = array();
    }
  }
  
  public static function langPath($langId) {
    return 'http://' . $langId . \substr(\system\Main::getDomain(), \strpos(\system\Main::getDomain(), ".")) . $_SERVER["REQUEST_URI"];
  }
  
  public static function setLang($langId) {
    if (\in_array($langId, \system\Main::setting('languages'))) {
      \system\Main::setSession('system', 'lang', $langId);
      self::initLang();
    }
  }
  
  public static function getLang() {
    if (\is_null(self::$lang)) {
      self::initLang();
    }
    return self::$lang;
  }
  
  public static function translate($sentence, $args = null) {
    // Make sure the vocabulary has been loaded
    self::getLang();
    
    if (\array_key_exists($sentence, self::$vocabulary)) {
      if (\is_callable(self::$vocabulary[$sentence])) {
        // The vocabulary may contain function for dynamic sentence translations
        $sentence = \call_user_func(self::$vocabulary[$sentence], $args);
      } else {
        // Simple translation
        $sentence = self::$vocabulary[$sentence];
      }
    }
    if (!empty($args)) {
      foreach ($args as $key => $value) {
        switch (substr($key, 0, 1)) {
          case '@':
            $value = \htmlentities($value);
            break;
          case '%':
            break;
          case '!':
            break;
        }
        $sentence = \str_replace($key, $value, $sentence);
      }
    }
    return $sentence;
  }
}
