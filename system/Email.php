<?php
namespace system;

class Email {
	private $from = array();
	private $to = array();
	private $body = "";
	private $subject = "";

	function __construct() {
		$this->setFrom(\config\settings()->GENERAL_EMAIL, \config\settings()->SITE_NAME);
	}
	function setFrom($email, $name="") {
		$this->from["email"] = $email;
		$this->from["name"] = str_replace('"', '', $name); // elimino gli apici doppi
	}
	function setTo($email, $name="") {
		$this->to["email"] = $email;
		$this->to["name"] = str_replace('"', '', $name); // elimino gli apici doppi
	}
	function setBody($body) {
		$this->body = $body;
	}
	function setSubject($subject) {
		$this->subject = $subject;
	}
	function send() {
		$mailHead = 'From:' 
			. ($this->from["name"] != "" ? ' "'. $this->from["name"] . '"' : '')
			. ($this->from["email"] != "" ? ' <'. $this->from["email"] .'>' : '')
			. "\n";
		
//		$mailBody =  "~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~\r\n";
//		$mailBody .= "  " . \config\settings()->SITE_NAME . "\r\n";
//		$mailBody .= "~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~\r\n\r\n";

		$mailBody = $this->body ."\r\n\r\n";

		$mailBody .= "~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~\r\n";
		$mailBody .= "  " . \config\settings()->SITE_ADDRESS . " \r\n";
		$mailBody .= "  " . \config\settings()->SITE_TITLE . "\r\n";
		$mailBody .= "~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~\r\n";
		
		return @mail($this->to["email"], $this->subject, $mailBody, $mailHead);
	}
}
?>