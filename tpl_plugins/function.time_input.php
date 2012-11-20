<?php
function smarty_function_time_input($args) {
	if (!array_key_exists("name", $args)) {
		throw new \system\InternalErrorException("Parametro name non trasmesso");
	}
	$name = $args["name"];

	// Se true fa selezionare anche i secondi
	$seconds = array_key_exists("seconds", $args) ? true : false;
	
	$secondsInterval = array_key_exists("secondsInterval", $args) ? $args["secondsInterval"] : 1;
	$minutesInterval = array_key_exists("minutesInterval", $args) ? $args["minutesInterval"] : 1;
	$hoursInterval = array_key_exists("hoursInterval", $args) ? $args["hoursInterval"] : 1;
	
	if (array_key_exists("value", $args)) {
		$h = date("H", $args["value"]);
		$i = date("i", $args["value"]);
		$s = date("s", $args["value"]);
	} else {
		$h = 0;
		$i = 0;
		$s = 0;
	}
	
	echo '<select name="' . $name . '[h]" class="xs time">';
	for ($j = 0; $j < 24; $j += $hoursInterval) {
		echo '<option value="' . $j . '"' . ($h == $j ? ' selected="selected"' : '') . '>' . ($j < 10 ? '0'.$j : $j) . '</option>';
	}
	echo '</select>';
	
	echo '<select name="' . $name . '[i]" class="xs time">';
	for ($j = 0; $j < 60; $j += $minutesInterval) {
		echo '<option value="' . $j . '"' . ($i == $j ? ' selected="selected"' : '') . '>' . ($j < 10 ? '0'.$j : $j) . '</option>';
	}
	echo '</select>';
	
	if ($seconds) {
		echo '<select name="' . $name . '[s]" class="xs time">';
		for ($j = 0; $j < 60; $j += $secondsInterval) {
			echo '<option value="' . $j . '"' . ($s == $j ? ' selected="selected"' : '') . '>' . ($j < 10 ? '0'.$j : $j) . '</option>';
		}
		echo '</select>';
	} else {
		echo '<input type="hidden" name="' . $name . '[s]" value="0"/>';
	}
}
?>