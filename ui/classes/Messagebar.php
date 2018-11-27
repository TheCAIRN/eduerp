<?php
class Messagebar {
	/*
	 * When setting a message, the class will be the highest level of message being presented.
	 */
	public function __construct() {
		
	} // __construct()
	public function render() {
		if (isset($_SESSION) && isset($_SESSION['message']))
			echo '<DIV id="message" class="'.$_SESSION['message'][0].'Message">'.$_SESSION['message'][1].'<BUTTON id="clearMessages" onClick="clearMessages();">Remove</BUTTON></DIV>';
		if (isset($_SEESION) && isset($_SESSION['barstatus'])) $_SESSION['barstatus'] &= 247; // Clear bit 4.
	} // render()
	public function clear() {
		if (isset($_SESSION) && isset($_SESSION['message'])) unset($_SESSION['message']);
	}
	public function addError($msg) {
		if (isset($_SESSION) && !isset($_SESSION['message'])) $_SESSION['message'] = array('error',$msg);
		elseif (isset($_SESSION['message'])) {
			$_SESSION['message'][1] .= '<BR />'.$msg;
			$_SESSION['message'][0] = 'error'; // There is no higher level.
		}
		if (!isset($_SESSION['barstatus'])) $_SESSION['barstatus'] = 8;
		else $_SESSION['barstatus'] |= 8; // Turn on bit 4, for messagebar updates.
	} // addError()
	public function addWarning($msg) {
		if (isset($_SESSION) && !isset($_SESSION['message'])) $_SESSION['message'] = array('warning',$msg);
		elseif (isset($_SESSION['message'])) {
			$_SESSION['message'][1] .= '<BR />'.$msg;
			if ($_SESSION['message'][0]!='error') $_SESSION['message'][0] = 'warning'; 
		}
		if (!isset($_SESSION['barstatus'])) $_SESSION['barstatus'] = 8;
		else $_SESSION['barstatus'] |= 8; // Turn on bit 4, for messagebar updates.
	} // addWarning()
	public function addInfo($msg) {
		if (isset($_SESSION) && !isset($_SESSION['message'])) $_SESSION['message'] = array('info',$msg);
		elseif (isset($_SESSION['message'])) {
			$_SESSION['message'][1] .= '<BR />'.$msg;
			if ($_SESSION['message'][0]=='success') $_SESSION['message'][0] = 'info'; 
		}
		if (!isset($_SESSION['barstatus'])) $_SESSION['barstatus'] = 8;
		else $_SESSION['barstatus'] |= 8; // Turn on bit 4, for messagebar updates.
	} // addInfo() 
	public function addSuccess($msg) {
		if (isset($_SESSION) && !isset($_SESSION['message'])) $_SESSION['message'] = array('success',$msg);
		elseif (isset($_SESSION['message'])) {
			$_SESSION['message'][1] .= '<BR />'.$msg;
		}
		if (!isset($_SESSION['barstatus'])) $_SESSION['barstatus'] = 8;
		else $_SESSION['barstatus'] |= 8; // Turn on bit 4, for messagebar updates.
	} // addSuccess()
} // class Messagebar
?>