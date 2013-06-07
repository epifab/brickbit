<?php
namespace lang;

class It {
	public static function vocabulary() {
		return array(
			"HELLO EVERYONE!" => function() {
				if (\system\utils\Login::isLogged()) {
					return "CIAO " . \system\utils\Login::getLoggedUser()->email;
				} else {
					return "CIAO PORCODDIO";
				}
			},
			"Once you choose a URN you shouldn't change it anymore." => "Una volta scelta una URN non dovresti cambiarla piu.",
			"In order to get the highest rating from search engines you should choose a URN containing important keywords directly related to the content itself." =>
				"Per ottenere i migliori risultati dai motori di ricerca dovresti scegliere una URN contenente importanti parole chiave direttamente correlate al contenuto stesso.",
			"Each word should be separeted by the dash characted." => "Le parole dovrebbero essere separate da un trattino.",
			"Please note also that two different contents, translated in @lang, must have two different URNs.",
			// Simple translation with no arguments
			"The page you were looking for doesn't exist" => "La pagina che stavi cercando non esiste",
			// Simple translation with an argument
			"Hi @user! How is it going?" => "Ciao @user! Come va?",

			"January" => "Gennaio",
			"February" => "Febbraio",
			"March" => "Marzo",
			"April" => "Aprile",
			"May" => "Maggio",
			"June" => "Giugno",
			"July" => "Luglio",
			"August" => "Agosto",
			"September" => "Settembre",
			"October" => "Ottobre",
			"November" => "Novembre",
			"December" => "Dicembre",

			"@month" => function($args) {
				switch ($args["@month"]) {
					case 1: return "Gennaio";
					case 2: return "Febbraio";
					case 3: return "Marzo";
					case 4: return "Aprile";
					case 5: return "Maggio";
					case 6: return "Giugno";
					case 7: return "Luglio";
					case 8: return "Agosto";
					case 9: return "Settembre";
					case 10: return "Ottobre";
					case 11: return "Novembre";
					case 12: return "Dicembre";
				}
			},

			"@month/@day/@year" => "@day/@month/@year",

			"@number articles found" => function($args) {
				switch ($args["@number"]) {
					case 0:
						return "Nessun articolo trovato";
						break;
					case 1:
						return "&Egrave; stato trovato un articolo";
						break;
					default:
						return "Sono stati trovati @number articoli";
				}
			}
		);		
	}
}
?>