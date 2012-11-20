<?php
/**
 * Visualizza un controllo per il lancio di un componente esterno
 * @param array $args
 * string 'component': nome del componente,
 * array 'args': argomenti aggiuntivi da passare al componente,
 * ## OPZIONI FINESTRA
 * int 'width': larghezza della maschera,
 * int 'height': altezza della maschera,
 * int 'maxWidth': larghezza massima della maschera,
 * int 'maxHeight': altezza massima della maschera,
 * string 'target': eventuale id elemento html designato a contenere la maschera (popup disabilitato),
 * boolean 'popup': se true la maschera apparirà come popup,
 * ## HANDLES JS
 * string 'onForm': function(xmcaResponse) {}, funzione javascript da lanciare alla visualizzazione di contenuto form
 * string 'onRead': function(xmcaResponse) {}, funzione javascript da lanciare alla visualizzazione di contenuto read
 * string 'onSuccess': function(xmcaResponse) {}, funzione javascript da lanciare alla visualizzazione di contenuto success
 * string 'onError': function(xmcaResponse) {}, funzione javascript da lanciare alla visualizzazione di contenuto error
 * ## OK BUTTON (FORM)
 * boolean 'okButton': se true verrà visualizzato un bottone di conferma (per i form)
 * string 'okButtonLabel': etichetta del bottone di conferma (default: 'Save') (per i form)
 * string 'okButtonOnClick': function(stdHandler) {stdHandler();}, funzione javascript da lanciare al click sul bottone di conferma (per i form)
 * ## CANCEL BUTTON (FORM)
 * boolean 'koButton': se tre verrà visualizzato un bottone di annullamento (per i form)
 * string 'koButtonLabel': etichetta del bottone di annullamento (default: 'Cancel') (per i form)
 * string 'koButtonOnClick': function(stdHandler) {stdHandler();}, funzione javascript da lanciare al click sul bottone di annullamento (per i form)
 * ## RESPONSE / WAIT
 * boolean 'showResponse': se true verrà visualizzata una finestra con la response positiva ad aggiornamento completato
 * boolean 'waitMessages': se true verrà visualizzato un messaggio di attesa
 * string 'waitMessagesLabel': etichetta messaggio di attesa (default: 'Please wait')
 * @return string 
 */
function smarty_function_xmca_control_action($args) {
	if (!array_key_exists("component", $args) || !is_string($args["component"])) {
		throw new system\InternalErrorException("Parametro component non valido");
	}
	
	$php2JsVars = function ($args) use (&$php2JsVars) {
		$jsVars = "{";
		foreach ($args as $k => $v) {
			!isset($first) ? $first = true : $jsVars .= ",";
			$jsVars .= "'" . $k . "': ";
			if (\is_array($v)) {
				$jsVars .= $php2JsVars($v);
			} else {
				if (is_bool($v)) {
					$jsVars .= $v ? "true" : "false";
				} else if (is_integer($v)) {
					$jsVars .= $v;
				} else if (substr(trim($v),0,8) == "function") {
					$jsVars .= $v;
				} else {
					$jsVars .= "'" . $v . "'";
				}
			}
		}
		return $jsVars . "}";
	};
	
	$jsArgs = $php2JsVars($args);
	
	return "xmca.request($jsArgs);";

//	$componentAddr = $args["component"] . "." . \config\settings()->COMPONENT_EXTENSION;
//
//	$ajax = !array_key_exists("ajax", $args) || $args["ajax"];
//
//	if (!array_key_exists("args", $args)) {
//		$componentArgs = $ajax ? '{}' : '';
//	} else {
//		$componentArgs = $ajax ? '{' : '?';
//		
//		if (!is_array($args["args"])) {
//			throw new system\InternalErrorException("Parametro args non valido");
//		}
//		$first = true;
//		foreach ($args["args"] as $key => $val) {
//			if (is_null($val)) {
//				$val = '';
//			}
//			if (!is_scalar($val)) {
//				throw new system\InternalErrorException("Parametro args non valido");
//			}
//			if ($ajax) {
//				$first ? $first = false : $componentArgs .= ",";
//			} else {
//				$first ? $first = false : $componentArgs .= "&amp;";
//			}
//			$componentArgs .= $ajax ? "'$key': '$val'" : "$key=" . urlencode($val);
//		}
//		
//		$componentArgs .= $ajax ? '}' : '';
//	}
//	
//	$title = array_key_exists("title", $args) ? $args["title"] : "Lancia componente " . $args["component"];
//	$class = "xmca_control" . (array_key_exists("class", $args) ? " " . $args["class"] : "");
//	if ($ajax) {
//		$optional = "";
//		$optional .= array_key_exists("showResponse", $args) ? ", showResponse: " . (($args["showResponse"]) ? "true" : "false") : "";
//		$optional .= array_key_exists("width", $args) ? ", width: " . $args["width"] : "";
//		$optional .= array_key_exists("height", $args) ? ", height: " . $args["height"] : "";
//		$optional .= array_key_exists("onForm", $args) ? ", onForm: " . $args["onForm"] : "";
//		$optional .= array_key_exists("onSuccess", $args) ? ", onSuccess: " . $args["onSuccess"] : "";
//		$optional .= array_key_exists("onError", $args) ? ", onError: " . $args["onError"] : "";
//		$optional .= array_key_exists("okButton", $args) ? ", okButton: " . (($args["okButton"]) ? "true" : "false") : "";
//		$optional .= array_key_exists("koButton", $args) ? ", koButton: " . (($args["koButton"]) ? "true" : "false") : "";
//		
// 		return "<button onclick=\"xmcaEditRequest({'component': '{$args['component']}', 'args': $componentArgs $optional}); return false;\" class=\"$class\">$title</button>";
//	} else {
//		return "<a href=\"$componentAddr$componentArgs\" class=\"$class\" title=\"$title\">$title</a>";
//	}
}
?>