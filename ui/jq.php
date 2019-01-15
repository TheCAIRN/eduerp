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
	} elseif ($command=='moduleSubMenu') {
		if (!isset($_POST['module'])) {
			$messagebar->addError("The selected module has not been installed in this system.");
			$link->close();
			return;
		}
		$module = $_POST['module'];
		$modObject = null;
		if ($mule=='Items') {
			
		}
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
	} elseif ($command=='executeSearch') {
		if (!isset($_POST['module'])) {
			$messagebar->addError("The selected module has not been installed in this system.");
			$link->close();
			return;
		}
		$module = $_POST['module'];
		$modObject = null;
		$searchParameters = array();
		if (isset($_POST['searchParameters']) && is_array($_POST['searchParameters'])) {
			$searchParameters = $_POST['searchParameters'];
		}
		if ($module=='EntitySearch') {
			if (isset($_SESSION['activeModule']) && $_SESSION['activeModule'] instanceof Entity) 
				$modObject = $_SESSION['activeModule'];
			else {
				$modObject = new Entity($link);
				$_SESSION['activeModule'] = $modObject;
			}
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
		$modObject->executeSearch($searchParameters);
	} elseif ($command=='viewRecord') {
		if (!isset($_POST['module'])) {
			$messagebar->addError("The selected module has not been installed in this system.");
			$link->close();
			return;
		}
		if (!isset($_POST['id'])) {
			$messagebar->addError("No record has been selected to display.");
			$link->close();
			return;
		}
		$module = $_POST['module'];
		$id = $_POST['id'];
		$modObject = null;
		if ($module=='Entity') {
			$modObject = new Entity($link);
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
		if (!($modObject->isIDValid($id))) {
			$messagebar->addError("The selected record ID is not valid for this module.");
			$link->close();
			return;
		}
		$modObject->display($id);
	} elseif ($command=='logoff') {
		$_SESSION['link']->close();
		unset($_SESSION['link']);
	} elseif ($command=='clearMessages') {
		$messagebar->clear();
	} else {
		$messagebar->addWarning("Invalid jquery option: ".$command);
		$link->close();
		return;
	}
	$link->close();
}
jquery();
?>