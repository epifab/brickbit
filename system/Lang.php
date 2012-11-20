<?php
namespace system;

class Lang {
	const DATE_VISUALIZATION_YMD = 1;
	const DATE_VISUALIZATION_MDY = 2;
	const DATE_VISUALIZATION_DMY = 3;
	
	private static $instance = null;

	protected $phrases = array();
	protected $langId = null;
	
	/**
	 * @return Lang
	 */
	public static function getInstance() {
		if (\is_null(Lang::$instance)) {
			Lang::$instance = new Lang();
			if (\array_key_exists("lang", $_REQUEST)) {
				$_SESSION["lang"] = $_REQUEST["lang"];
			}
			Lang::$instance->setLang(\array_key_exists("lang", $_SESSION) ? $_SESSION["lang"] : \config\settings()->DEFAULT_LANG);
		}
		return Lang::$instance;
	}
	
	public static function getLangId() {
		return self::getInstance()->langId;
	}

	public function setLang($langId) {
		if ($this->langId == $langId) {
			// nothing
		} else if (\array_key_exists($langId, $this->phrases)) {
			// langId already loaded
			$this->langId = $langId;
		} else {
			require_once "lang/$langId.php";
			$function = "\\lang\\" . $langId;
			$this->phrases[$langId] = $function();
			$this->langId = $langId;
		}
	}

	/**
	 * Restituisce una frase dal dizionario
	 * @throws InternalErrorException
	 * @throws LangException 
	 */
	public static function get() {
		$lang = self::getInstance();
		
		$elem = \func_get_args();
		if (count($elem) == 0) {
			throw new InternalErrorException("Nessun parametro trasmesso al metodo");
		}

		if (!\array_key_exists($elem[0], $lang->phrases[$lang->langId])) {
			if (\config\settings()->DEFAULT_LANG != $lang->langId 
				&& \array_key_exists(\config\settings()->DEFAULT_LANG, $lang->phrases)
				&& \array_key_exists($elem[0], $lang->phrases[\config\settings()->DEFAULT_LANG])) {
				@\vprintf($lang->phrases[\config\settings()->DEFAULT_LANG][$elem[0]], \array_slice($elem, 1));
			} else {
				throw new LangException("Frase " . $elem[0] . " non trovata sul dizionario");
			}
		}
		\vprintf($lang->phrases[$lang->langId][$elem[0]], \array_slice($elem, 1));
	}
}
?>