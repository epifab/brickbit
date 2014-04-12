<?php
namespace system\utils;

use system\Main;

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
  
  public static function langLabel($lang) {
    static $langs = array(
      'it' => 'Italiano',
      'en' => 'English',
    );
    return $langs[$lang];
  }
  
  private static function initLang($langId = null) {
    self::$lang = empty($langId)
      ? Main::setting('defaultLang')
      : $langId;
    
    $callback = array('\\lang\\' . \ucfirst($langId), 'vocabulary');
    if (\is_callable($callback)) {
      self::$vocabulary = \call_user_func($callback);
    }
    if (!\is_array(self::$vocabulary)) {
      self::$vocabulary = array();
    }
  }
  
  public static function setLang($langId) {
    if (\in_array($langId, Main::getLanguages())) {
      Main::setSession('system', 'lang', $langId);
      self::initLang();
    }
  }
  
  public static function getLang() {
    if (\is_null(self::$lang)) {
      self::initLang();
    }
    return self::$lang;
  }
  
  public static function format($sentence, $args = array(), $vocabulary = array()) {
    if (\array_key_exists($sentence, $vocabulary)) {
      if (\is_callable($vocabulary[$sentence])) {
        // The vocabulary may contain function for dynamic sentence translations
        $sentence = \call_user_func($vocabulary[$sentence], $args);
      } else {
        // Simple translation
        $sentence = $vocabulary[$sentence];
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
  
  public static function translate($sentence, $args = null) {
    // Make sure the vocabulary has been loaded
    self::getLang();
    return self::format($sentence, $args, self::$vocabulary);
  }
}
