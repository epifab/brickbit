<?php

function smarty_block_xmca_autocomplete($args, $content, Smarty_Internal_Data &$smarty, &$repeat) {
	static $count = array();

	if (!$repeat) {
		// Calcolo id univoco
		$vars = $smarty->getTemplateVars();
		if (!array_key_exists($vars["private"]["requestId"], $count)) {
			$count[$vars["private"]["requestId"]] = 1;
		} else {
			$count[$vars["private"]["requestId"]]++;
		}
		$id = "xmca_" . $vars["private"]["requestId"] . "_autocomplete_" . $count[$vars["private"]["requestId"]];
		
		//////////////////////
		// MODEL
		//////////////////////
		if (!array_key_exists("model", $args) || !is_string($args["model"])) {
			throw new system\InternalErrorException("Parametro model non valido");
		}
		$model = $args["model"];
		
		//////////////////////
		// PATHS
		//////////////////////
		if (!array_key_exists("paths", $args) || !is_array($args["paths"])) {
			throw new system\InternalErrorException("Parametro paths non valido");
		}
		$paths = system\Utils::php2js($args["paths"]);
		
		//////////////////////
		// KEY
		//////////////////////
		if (!array_key_exists("key", $args) || !is_string($args["key"])) {
			throw new system\InternalErrorException("Parametro key non valido");
		}
		$key = $args["key"];
		
		//////////////////////
		// LABEL
		//////////////////////
		if (!array_key_exists("label", $args) || !is_string($args["label"])) {
			throw new system\InternalErrorException("Parametro label non valido");
		}
		$label = $args["label"];

		$class = @$args["class"];
		$name = @$args["name"];
		$defaultValue = @$args["defaultValue"];
		$defaultKey = @$args["defaultKey"];
//		$extendedArgs = system\Utils::php2js(@$args["args"]);
		
		
		$matches = array();
		preg_match_all("/\@\[[a-zA-Z\.0-9_]+\]/", $content, $matches, PREG_OFFSET_CAPTURE);
				
		$display = "$('<' + 'div>'";
		
		$j = 0;
		foreach ($matches[0] as $match) {
			$k = $match[1];
			if ($k > $j) {
				$offset = $k - $j;
				$display .= " + '" . addslashes(substr($content,$j,$offset)) . "'";
			}
			$fieldExpr = substr($match[0],2,-1);
			$display .= " + item['" . $fieldExpr . "']";
			$j = $k + strlen($match[0]);
		}
		$display .= " + '" . addslashes(substr($content,$j)) . "<' + '/div>')";

		$html =
			'<input type="text" id="' . $id . '" value="' . addslashes($defaultValue) . '"' . (empty($class) ? '' : ' class="' . $class . '"') . '/>'
			. '<input type="hidden" name="' . $name . '" id="' . $id . '_key" value="' . $defaultKey . '"/>';
		
// amp Xml 2 Ajax Mysql Php
		$js =
			'$(document).ready(function(){' . "\n"
			. '	options = {' . "\n"
			. '		width: 320,' . "\n"
			. '		dataType: "json",' . "\n"
			. '		highlight: false,' . "\n"
			. '		scroll: true,' . "\n"
			. '		scrollHeight: 300,' . "\n"
			. '		mustMatch: true,' . "\n"
			. '		extraParams: {' . "\n"
			. '			model: "' . $model . '",' . "\n"
			. '			paths: ' . $paths . ',' . "\n"
			. '			label: "' . $label . '"' . "\n"
			. '		},' . "\n"
			. '		parse: function(data) {' . "\n"
			. '			var parsed = [];' . "\n"
			. '			data = data.recordsets;' . "\n"
			. '			for (var i = 0; i < data.length; i++) {' . "\n"
			. '				parsed[parsed.length] = {' . "\n"
			. '					data: data[i],' . "\n"
			. '					value: data[i]["' . $label . '"],' . "\n"
			. '					result: data[i]["' . $label . '"]' . "\n"
			. '				};' . "\n"
			. '			}' . "\n"
			. '			return parsed;' . "\n"
			. '		},' . "\n"
			. '		formatItem: function(item) {' . "\n"
			. '			return ' . $display. ';' . "\n"
			. '		}' . "\n"
			. '	};' . "\n"
			. '	$("#' . $id . '").autocomplete("Autocomplete.html", options)' . "\n"
			. '		.result(function(e, data) {' . "\n"
			. '			$("#' . $id . '_key").val(data["' . $key . '"]);' . "\n"
			. '		});' . "\n"
			. '	});' . "\n";

		system\Utils::addJsCode($js, $smarty);
		
		return $html;
	}
}
?>