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
	include('globals.php');
	$link = new mysqli($dbhost,$dbuser,$dbpass,$dbname);
	if ($link->connect_error) {
		$messagebar->addError($link->connect_error);
		unset($link);
	}
	$ws = new Workspace($link);
	
	if ($command=='mainMenu') {
		unset($_SESSION['activeModule']);
		$ws->setCurrentScreen(0);
	} elseif ($command=='moduleSubMenu') {
		if (!isset($_POST['module'])) {
			$messagebar->addError("The selected module has not been installed in this system.");
			$link->close();
			return;
		}
		$module = $_POST['module'];
		if ($mule=='Contacts') {
			$ws->setCurrentScreen(3);
		} elseif ($mule=='Items') {
			$ws->setCurrentScreen(4);
		}
		// Workspace::setCurrentScreen renders immediately.
	} elseif ($command=='moduleSearchSpace') {
		// present the search screen for the selected module.
		if (!isset($_POST['module'])) {
			$messagebar->addError("The selected module has not been installed in this system.");
			$link->close();
			return;
		}
		$module = $_POST['module'];
		if ($module=='Entities') {
			$ws->setCurrentScreen(1);
		} elseif ($module=='CoreLookups') {
			$ws->setCurrentScreen(2);
		} elseif ($module=='Contacts') {
			// Generate sub-menu for People and Addresses
			$ws->setCurrentScreen(3);
		} elseif ($module=='Items') {
			// Generate various sub-menus for Item setup and inventory
			$ws->setCurrentScreen(4);
		} elseif ($module=='Vendors') {
			$ws->setCurrentScreen(5);
		} elseif ($module=='Freight') {
			$ws->setCurrentScreen(6);
		} elseif ($module=='Purchasing') {
			$ws->setCurrentScreen(7);
		} elseif ($module=='People') {
			$ws->setCurrentScreen(8);
		} elseif ($module=='Addresses') {
			$ws->setCurrentScreen(9);
		} elseif ($module=='ItemSetup') {
			$ws->setCurrentScreen(10);
		} else {
			$messagebar->addError("The selected module has not been installed in this system.");
			$link->close();
			return;
		}
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
		} elseif ($module=='ItemSearch') {
			if (isset($_SESSION['activeModule']) && $_SESSION['activeModule'] instanceof ItemManager) 
				$modObject = $_SESSION['activeModule'];
			else {
				$modObject = new ItemManager($link);
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