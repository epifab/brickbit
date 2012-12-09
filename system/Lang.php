<?php
namespace system;
/*
 * How the lang system work:
 *  
 * $vocabulary = array (
 * 	'Hai mangiato @numero confetti' => function($args) {
 * 		if ($args["@numero"] == 0) {
 * 			return 'Non hai mangiato nessun confetto';
 * 		} else if ($args["@numero"] == 1) {
 * 			return 'Hai mangiato un confetto';
 * 		} else {
 * 			return 'Hai mangiato ' . $args["@numero"] . ' confetti';
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
		$function = '\\lang\\' . $langId;
		self::$vocabulary = function_exists($function) ? $function() : array();
	}
	
	public static function setLang($langId) {
		if (\array_key_exists($langId, \config\settings()->LANGS)) {
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
				$sentence = self::$vocabulary[$sentence]($args);
			} else {
				// Simple translation
				$sentence = self::$vocabulary[$sentence];
			}
		}
		if (!\is_null($args)) {
			$sentence = \preg_replace(\array_keys($args), \array_values($args), $sentence);
		}
		return $sentence;
	}
}
?>