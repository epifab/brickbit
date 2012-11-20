<?php
namespace system;

class Validation {
	public static function checkNotNullable($str) {
		 if (is_null($str)) {
			 throw new ValidationException("Valore NULL non ammissibile");
		 }
	}
	
	public static function checkSet($x, $set) {
		 if (!\in_array($x, $set)) {
			 throw new ValidationException("Opzione non valida");
		 }
	}
	
	public static function checkRange($str, $min, $max) {
		if (!is_null($min) && $str < $min || !is_null($max) && $str > $max) {
			throw new ValidationException("Valore fuori dal range");
		}
	}
	public static function checkPattern($str, $pattern) {
		if (!\preg_match("/" . $pattern . "/", $str)) {
			throw new ValidationException("Formato non valido");
		}
	}
	public static function checkEmail($str) {
		if (!\preg_match("/^[a-zA-Z0-9]+(\\.?[a-zA-Z0-9\\-\\_]+)*\\@[a-zA-Z]+(\\.?[a-zA-Z0-9\-\_]+)*\.[a-zA-Z]{2,}$/", $str)) {
			throw new ValidationException("Email non valida");
		}
	}
	public static function checkUrlFormat($str) {
		if (!\preg_match("/^((http\:\/\/)?[a-zA-Z0-9_\-]+(?:\.[a-zA-Z0-9_\-]+)*\.[a-zA-Z]{2,4}(?:\/[a-zA-Z0-9_]+)*(?:\/[a-zA-Z0-9_]+\.[a-zA-Z]{2,4}(?:\?[a-zA-Z0-9_]+\=[a-zA-Z0-9_]+)?)?(?:\&[a-zA-Z0-9_]+\=[a-zA-Z0-9_]+)*)$/", $str)) {
			throw new ValidationException("URL non valido");
		}
	}
	public static function checkSimpleText($str) {
		if (empty($str)) {
			throw new ValidationException("Campo vuoto.");
		} else if (\preg_match("/[\<\>]/", $str)) {
			throw new ValidationException("Il campo non può contenere parentesi angolari.");
		} else if (!\preg_match("/[a-zA-Z0-9]/", $str)) {
			throw new ValidationException("Il campo deve contenere almeno un carattere alfanumerico.");
		}
	}
	public static function checkAlNum($str) {
		if (!\preg_match("/^[a-zA-Z0-9_]+$/", $str)) {
			throw new ValidationException("Sono ammessi soltanto lettere, numeri e il carattere di underscore '_'.");
		}
	}
	public static function checkPassword($str) {
		if (strlen($str) < 6) {
			throw new ValidationException("Una password deve essere composta almeno da 6 caratteri");
		} else if (!\preg_match("/([a-zA-Z][0-9\.\_\-\@\:\;\,]+)|([0-9\.\_\-\@\:\;\,][a-zA-Z]+)/", $str)) {
			throw new ValidationException("Una password deve essere costituita almeno da una lettera ed un numero");
		}
	}
	public static function checkNotEmpty($str) {
		if (empty($str)) {
			throw new ValidationException("Nessun valore specificato");
		}
	}
	public static function checkSize($str, $minsize, $maxsize) {
		if (!\is_null($minsize) && \strlen($str) < $minsize) {
			throw new ValidationException(($minsize == 1 ? "Nessun valore specificato" : "Sono richiesti almeno " . $minsize . " caratteri"));
		}
		if (!\is_null($maxsize) && \strlen($str) > $maxsize) {
			throw new ValidationException("Immettere al massimo " . $maxsize . " caratteri");
		}
	}
	public static function checkCodiceFiscale($str) {
		if (strlen($str) != 16) {
			throw new ValidationException("Codice fiscale non valido");
		}
	}
	public static function checkPhoneNumber($str) {
		if (!\preg_match("/^(\+[0-9]{2})?[0-9]{6,}$/", $str)) {
			throw new ValidationException("Numero telefonico non valido.<br/>Inserire soltanto le cifre che lo compongono");
		}
	}
	public static function checkCAP($str) {
		if (!\preg_match("/^[0-9]{5,5}$/", $str)) {
			throw new ValidationException("CAP non valido");
		}
	}
}
?>
