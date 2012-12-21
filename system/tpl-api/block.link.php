<?php
/**
 * Link
 * @param array $args
 * 	string 'url': url,
 * 	string 'class': 'a' element class,
 *		boolean 'dialog': true
 * 	array 'args': argument to pass within the url,
 * ## OPZIONI FINESTRA
 * 	int 'width': larghezza della maschera,
 *		int 'height': altezza della maschera,
 *		int 'maxWidth': larghezza massima della maschera,
 *		int 'maxHeight': altezza massima della maschera,
 *		string 'target': eventuale id elemento html designato a contenere la maschera (popup disabilitato),
 *		boolean 'popup': se true la maschera apparirà come popup,
 * ## HANDLES JS
 *		string 'onForm': function(xmcaResponse) {}, funzione javascript da lanciare alla visualizzazione di contenuto form
 *		string 'onRead': function(xmcaResponse) {}, funzione javascript da lanciare alla visualizzazione di contenuto read
 *		string 'onSuccess': function(xmcaResponse) {}, funzione javascript da lanciare alla visualizzazione di contenuto success
 *		string 'onError': function(xmcaResponse) {}, funzione javascript da lanciare alla visualizzazione di contenuto error
 * ## OK BUTTON (FORM)
 *		boolean 'okButton': se true verrà visualizzato un bottone di conferma (per i form)
 *		string 'okButtonLabel': etichetta del bottone di conferma (default: 'Save') (per i form)
 *		string 'okButtonOnClick': function(stdHandler) {stdHandler();}, funzione javascript da lanciare al click sul bottone di conferma (per i form)
 * ## CANCEL BUTTON (FORM)
 *		boolean 'koButton': se tre verrà visualizzato un bottone di annullamento (per i form)
 *		string 'koButtonLabel': etichetta del bottone di annullamento (default: 'Cancel') (per i form)
 *		string 'koButtonOnClick': function(stdHandler) {stdHandler();}, funzione javascript da lanciare al click sul bottone di annullamento (per i form)
 * ## RESPONSE / WAIT
 *		boolean 'showResponse': se true verrà visualizzata una finestra con la response positiva ad aggiornamento completato
 *		boolean 'waitMessages': se true verrà visualizzato un messaggio di attesa
 *		string 'waitMessagesLabel': etichetta messaggio di attesa (default: 'Please wait')
 * @return string 
 */
function smarty_block_link($params, $content, &$smarty, $repeat) {
	
	if (!$repeat) {
		$url = system\Utils::getParam($params, 'url', array('required' => true, 'prefix' => \config\settings()->BASE_DIR));
		$ajax = system\Utils::getParam($params, 'ajax', array('default' => true, 'options' => array(false, true)));
		$class = system\Utils::getParam($params, 'class', array('default' => 'link'));
		$args = \system\Utils::getParam($params, 'args', array('default' => array()));

		if ($ajax) {
			$confirm = system\Utils::getParam($args, 'confirm', array('default' => false, 'options' => array(false, true)));
			if ($confirm) {
				$confirmTitle = str_replace("'", "\\'", system\Utils::getParam($args, 'confirmTitle', array('default' => '')));
				$confirmQuest = str_replace("'", "\\'", system\Utils::getParam($args, 'confirmQuest', array('default' => '')));
				$action = "xmca.confirm('" . $confirmTitle . "', '" . $confirmQuest . "', " . system\Utils::php2Js($args) . "); return false;";
			} else {
				$action = "xmca.request(" . system\Utils::php2Js($args) . "); return false;";
			}
		}
		return '<a href="' . system\Utils::addUrlArgs($url, $args) . '"' 
			. (empty($class) ? '' : ' class="' . $class . '"')
			. (empty($action) ? '' : ' onclick="' . $action . '"') . '>' 
			. $content 
			. '</a>';
	}
}
?>