<?php
namespace lang;

function it() {
	return array(
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
?>