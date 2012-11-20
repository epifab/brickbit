<?php
function smarty_modifier_date_format($x, $y=true) {
	if ($x === null) {
		return "";
	}
	$days = array("Domenica", "Lunedì", "Martedì", "Mercoledì", "Giovedì", "Venerdì", "Sabato");
	$months = array("Gennaio", "Febbraio", "Marzo", "Aprile", "Maggio", "Giugno", "Luglio", "Agosto", "Settembre", "Ottobre", "Novembre", "Dicembre");

	$d = $days[date('w', $x)];
	$m = $months[date('n', $x) - 1];

	$dataCompleta = $d . date(' j ', $x) . $m . date(' Y', $x);

	if (!$y) {
		return $dataCompleta;
	}

	if ($x > time()) {
		// evento passato
		$m = (int)date("n", $x) - (int)date("n");
		$y = (int)date("Y", $x) - (int)date("Y");
		$d = (int)date("j", $x) - (int)date("j");
		$h = (int)date("H", $x) - (int)date("H");
		$i = (int)date("i", $x) - (int)date("i");
		$s = (int)date("s", $x) - (int)date("s");
		$past = false;
	}
	else {
		// evento futuro
		$d = (int)date("j") - (int)date("j", $x);
		$m = (int)date("n") - (int)date("n", $x);
		$y = (int)date("Y") - (int)date("Y", $x);
		$h = (int)date("H") - (int)date("H", $x);
		$i = (int)date("i") - (int)date("i", $x);
		$s = (int)date("s") - (int)date("s", $x);
		$past = true;
	}

	$noDays = $d + $m*30 + $y*365;

	if ($noDays == 0) {
		return '<span title="' . $dataCompleta . '">Oggi</span>';
	} else {
		if ($noDays == 1) {
			if ($past) {
				return '<span title="' . $dataCompleta . '">Ieri</span>';
			} else {
				return '<span title="' . $dataCompleta . '">Domani</span>';
			}
		} else if ($noDays == 2) {
			if ($past) {
				return '<span title="' . $dataCompleta . '">L\'altro Ieri</span>';
			} else {
				return '<span title="' . $dataCompleta . '">Dopo domani</span>';
			}
		} else if ($noDays == 7) {
			$time = "una settimana";
		} else if ($noDays == 14) {
			$time = "due settimane";
		} else if ($noDays == 21) {
			$time = "tre settimane";
		} else if ($noDays > 27 && $noDays < 32) {
			$time = "circa un mese";
		} else if ($noDays > 360 && $noDays < 370) {
			$time = "circa un anno";
		} else if ($noDays < 27) {
			$time = $noDays . " giorni";
		} else {
			return $dataCompleta;
		}
	}

	if ($past) {
		return '<span title="'. $dataCompleta .'">' . strtoupper(substr($time,0,1)) . substr($time,1) . ' fa</span>';
	} else {
		return '<span title="'. $dataCompleta .'">Tra ' . $time . '</span>';
	}
}
?>