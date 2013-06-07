<?php
namespace system\utils;
/*
 * How the lang system work:
 *  
 * $vocabulary = array (
 * 	'You eat @numero sweets' => function($args) {
 * 		if ($args["@numero"] == 0) {
 * 			return 'Non hai mangiato nessun dolcetto';
 * 		} else if ($args["@numero"] == 1) {
 * 			return 'Hai mangiato un dolcetto';
 * 		} else {
 * 			return 'Hai mangiato ' . $args["@numero"] . ' dolcetti';
 * 		}
 * 	},
 * 	'Hello @user! How is it going?' => 'Ciao @user! Come stai?',
 * );
 */

class Lang {
	private static $lang = null;
	private static $vocabulary = null;
	
	private static function initLang($langId=null) {
		if (\is_null($langId)) {
			$langId = \array_key_exists("lang", $_SESSION) ? $_SESSION["lang"] : \config\settings()->DEFAULT_LANG;
		}
		self::$lang = $langId;
		$callback = array('\\lang\\' . \ucfirst($langId), 'vocabulary');
		if (\is_callable($callback)) {
			self::$vocabulary = \call_user_func($callback);
		}
		if (!\is_array(self::$vocabulary)) {
			self::$vocabulary = array();
		}
	}
	
	public static function langPath($langId) {
		return 'http://' . $langId . \substr(\config\settings()->DOMAIN, \strpos($_SERVER["HTTP_HOST"], ".")) . $_SERVER["REQUEST_URI"];
	}
	
	public static function setLang($langId) {
		if (\in_array($langId, \config\settings()->LANGUAGES)) {
			$_SESSION["lang"] = $langId;
			self::initLang();
		}
	}
	
	public static function getLang() {
		if (\is_null(self::$lang)) {
			self::initLang();
		}
		return self::$lang;
	}
	
	public static function translate($sentence, $args=null) {
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
		if (!\is_null($args)) {
			foreach ($args as $key => $value) {
				$sentence = \str_replace($key, $value, $sentence);
			}
		}
		return $sentence;
	}
}
?>