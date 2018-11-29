<?php
/*
 * Setup section for all jquery functions
 */
session_name('eduerpcfg');
session_start();
spl_autoload_register(function ($class) {
    include 'classes/' . $class . '.php';
});
/*
 * End of setup section
 */
 
// Main function for handling jqueries.  Requires POST - will not accept GET operations.
function jquery() {
	if (!isset($_POST['jquery'])) return;
	$command = $_POST['jquery'];
	if (!ctype_alnum($command)) return; // No special characters, including whitespace or punctuation, are allowed in these jquery commands

	$messagebar = new Messagebar();
	//if (!isset($_SESSION['link'])) {
		include('globals.php');
		$link = new mysqli($dbhost,$dbuser,$dbpass,$dbname);
		if ($link->connect_error) {
			$messagebar->addError($link->connect_error);
			unset($link);
	//	} else {
	//		$_SESSION['link'] = $link;
		}
	//} else {
	//	$link = $_SESSION['link'];
	//}
	
	if ($command=='mainMenu') {
		unset($_SESSION['activeModule']);
		$_SESSION['currentScreen'] = 0;
		$ws = new Workspace();
		$ws->render();
	} elseif ($command=='moduleSearchSpace') {
		// present the search screen for the selected module.
		if (!isset($_POST['module'])) {
			$messagebar->addError("The selected module has not been installed in this system.");
			$link->close();
			return;
		}
		$module = $_POST['module'];
		$modObject = null;
		if ($module=='Entities') {
			$modObject = new Entity($link);
			$_SESSION['currentScreen'] = 1;
		} elseif ($module=='CoreLookups') {
			
			$_SESSION['currentScreen'] = 2;
		} elseif ($module=='Contacts') {
			// Generate sub-menu for People and Addresses
			
			$_SESSION['currentScreen'] = 3;
		} elseif ($module=='Items') {
			
			$_SESSION['currentScreen'] = 4;
		} elseif ($module=='Vendors') {
			
			$_SESSION['currentScreen'] = 5;
		} elseif ($module=='Freight') {
			
			$_SESSION['currentScreen'] = 6;
		} elseif ($module=='Purchasing') {
			
			$_SESSION['currentScreen'] = 7;
		} elseif ($module=='Humans') {
			
			$_SESSION['currentScreen'] = 8;
		} elseif ($module=='Addresses') {
			
			$_SESSION['currentScreen'] = 9;
		} else {
			$messagebar->addError("The selected module has not been installed in this system.");
			$link->close();
			return;
		}
		if (is_null($modObject)) {
			$messagebar->addError("The selected module is not available at the moment.  Please wait a few minutes and try again.");
			$link->close();
			return;
		}
		$modObject->searchPage();
		$_SESSION['activeModule'] = $modObject;
	} elseif ($command=='logoff') {
		$_SESSION['link']->close();
		unset($_SESSION['link']);
	} else {
		$messagebar->addWarning("Invalid jquery option.");
		$link->close();
		return;
	}
	$link->close();
}
jquery();
?>