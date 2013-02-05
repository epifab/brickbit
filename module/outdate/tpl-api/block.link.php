<?php
/**
 * Link
 * @param array $params
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
		$params['url'] = system\Utils::getParam('url', $params, array('required' => true, 'prefix' => \config\settings()->BASE_DIR));

		$url = $params['url'];
		$ajax = system\Utils::getParam('ajax', $params, array('default' => true, 'options' => array(false, true)));
		$class = system\Utils::getParam('class', $params, array('default' => 'link'));
		$params['system'] = array(
			'requestType' => 'MAIN',
//			'requestId' => null
		);
		$jsArgs = system\Utils::php2Js($params); //array_merge(array('url' => $url), \system\Utils::getParam('args', $params, array('default' => array()))));

		if ($ajax) {
			$confirm = system\Utils::getParam('confirm', $params, array('default' => false, 'options' => array(false, true)));
			if ($confirm) {
				$confirmTitle = str_replace("'", "\\'", system\Utils::getParam('confirmTitle', $params, array('default' => '')));
				$confirmQuest = str_replace("'", "\\'", system\Utils::getParam('confirmQuest', $params, array('default' => '')));
				$action = "xmca.confirm('" . $confirmTitle . "', '" . $confirmQuest . "', " . $jsArgs . "); return false;";
			} else {
				$action = "xmca.request(" . $jsArgs . "); return false;";
			}
		}
		return '<a href="' . $url . '"' 
			. (empty($class) ? '' : ' class="' . $class . '"')
			. (empty($action) ? '' : ' onclick="' . $action . '"') . '>' 
			. $content 
			. '</a>';
	}
}
?>