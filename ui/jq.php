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
		$cs = array('Dashboard','Entities','CoreLookups','Contacts','Items','Vendors','Freight','Purchasing','Production','Customers','Sales',
			'People','Addresses','ItemSetup','ItemAttributes','ItemCategories','ItemTypes','GTINMaster','InventoryLookup','BillofMaterials','VendorCatalog'
			);
		$setcs = array_search($module,$cs); // Set the current screen to the index # of the $cs array.
		if (is_integer($setcs)) $ws->setCurrentScreen($setcs);
		else {
			$messagebar->addError("The selected module has not been installed in this system.");
			$link->close();
			return;
		}
	} elseif ($command=='listResultsAgain') {
		if (isset($_SESSION['currentScreen']) && ($_SESSION['currentScreen']>=200 && $_SESSION['currentScreen']<300)) {
			$ws->setCurrentScreen($_SESSION['currentScreen']-100);
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
		} elseif ($module=='VendorSearch') {
			if (isset($_SESSION['activeModule']) && $_SESSION['activeModule'] instanceof Vendor) 
				$modObject = $_SESSION['activeModule'];
			else {
				$modObject = new Vendor($link);
				$_SESSION['activeModule'] = $modObject;
			}
		} elseif ($module=='VendorCatalogSearch') {
			if (isset($_SESSION['activeModule']) && $_SESSION['activeModule'] instanceof VendorCatalog) 
				$modObject = $_SESSION['activeModule'];
			else {
				$modObject = new VendorCatalog($link);
				$_SESSION['activeModule'] = $modObject;
			}
		} elseif ($module=='BOMSearch') {
			if (isset($_SESSION['activeModule']) && $_SESSION['activeModule'] instanceof BOM) 
				$modObject = $_SESSION['activeModule'];
			else {
				$modObject = new BOM($link);
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
		} elseif ($module=='ItemManager') {
			$modObject = new ItemManager($link);
		} elseif ($module=='Vendor') {
			$modObject = new Vendor($link);
		} elseif ($module=='VendorCatalog') {
			$modObject = new VendorCatalog($link);
		} elseif ($module=='BOM') {
			$modObject = new BOM($link);
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
	} elseif ($command=='newRecord') {
		if (!isset($_SESSION['currentScreen'])) {
			$messagebar->addError("Please select a module first.");
			$link->close();
			return;
		}
		if ($_SESSION['currentScreen']%100==7) {
			$modObject = new Purchasing($link);
		} elseif ($_SESSION['currentScreen']%100==19) {
			$modObject = new BOM($link);
		}
		if (is_null($modObject)) {
			$messagebar->addError("The selected module is not available at the moment.  Please wait a few minutes and try again.");
			$link->close();
			return;
		}
		$modObject->newRecord();
	} elseif ($command=='insertRecord' || $command=='updateRecord') {
		// NOTE: all text in $_POST['module'] will be lower case!
		$modObject = null;
		if (!isset($_POST['module'])) {
			$messagebar->addError("Please select a module first.");
			$link->close();
			return;
		}
		if ($_POST['module']=='purchasing') {
			$modObject = new Purchasing($link);
		} elseif ($_POST['module']=='bom') {
			$modObject = new BOM($link);
		}
		if ($command=='insertRecord')
			$modObject->insertRecord();
		else 
			$modObject->updateRecord();
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
// TODO: This line is only temporary until a true login system is implemented.
if (!isset($_SESSION['dbuserid'])) $_SESSION['dbuserid'] = 1;
jquery();
?>