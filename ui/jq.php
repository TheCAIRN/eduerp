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
	
	/**************************************************************************
	 * MAIN MENU
	 **************************************************************************/
	if ($command=='mainMenu') {
		unset($_SESSION['activeModule']);
		$ws->setCurrentScreen(0);
	/**************************************************************************
	 * SUB MENU
	 **************************************************************************/
	} elseif ($command=='moduleSubMenu') {
		// Only activate submenus for top-level dashboard icons here.  Third-level menus are 
		// selected from the Workspace directly.
		if (!isset($_POST['module'])) {
			$messagebar->addWarning("The selected module has not been installed in this system.");
			$link->close();
			return;
		}
		$module = $_POST['module'];
		if ($module=='Contacts') {
			$ws->setCurrentScreen(3);
		} elseif ($module=='Items') {
			$ws->setCurrentScreen(4);
		} elseif ($module=='Freight') {
			$ws->setCurrentScreen(6);
		} elseif ($module=='Customers') {
			$ws->setCurrentScreen(9);
		} elseif ($module=='Sales') {
			$ws->setCurrentScreen(10);
		} elseif ($module=='Insights') {
			$ws->setCurrentScreen(29);
		} elseif ($module=='Accounting') {
			$ws->setCurrentScreen(34);
		}
		// Workspace::setCurrentScreen renders immediately.
	/**************************************************************************
	 * FARM OUT JQUERY COMMANDS TO MODULES
	 **************************************************************************/
	} elseif ($command=='itemjq') {
		$modObj = new ItemManager($link);
		$modObj->jquery();
	/**************************************************************************
	 * PRESENT SEARCH FORM OR SUBMENU
	 **************************************************************************/
	} elseif ($command=='moduleSearchSpace') {
		// present the search screen for the selected module.
		if (!isset($_POST['module'])) {
			$messagebar->addWarning("The selected module has not been installed in this system.");
			$link->close();
			return;
		}
		$module = $_POST['module'];
		$cs = array('Dashboard','Entities','CoreLookups','Contacts','Items','Vendors','Freight','Purchasing','Production','Customers','Sales',
			'People','Addresses','ItemSetup','ItemAttributes','ItemCategories','ItemTypes','GTINMaster','InventoryLookup','BillofMaterials','VendorCatalog',
			'EntityResource','CustomerCatalog','CustomerTypes','Customer','CustomerDC','CustomerStoreTypes','CustomerStores','Consumers','Insights','DashboardSetup',
			'ReportSetup','Dashboards','Reports','Accounting','ChartOfAccounts','GLPeriods','GLAccounts','GLBalances','GLJournal','FreightVendorTypes','FreightVendors',
			'InboundFreight','OutboundFreight','SalesOrders','SalesPayments','SalesOrderTypes','Admin','SystemOptions','UserAccounts','SecurityGroups','Permissions',
			'Currency','Country','Language','State','UOMTypes','UOM','UOMConversions','Terms','NoteTypes','AttachmentTypes','CancellationReasonCodes','BOMSteps'
			);
		$setcs = array_search($module,$cs); // Set the current screen to the index # of the $cs array.
		if (is_integer($setcs)) $ws->setCurrentScreen($setcs);
		else {
			$messagebar->addWarning("The selected module has not been installed in this system.");
			$link->close();
			return;
		}
	/**************************************************************************
	 * LIST SEARCH RESULTS
	 **************************************************************************/
	} elseif ($command=='listResultsAgain') {
		if (isset($_SESSION['currentScreen']) && ($_SESSION['currentScreen']>=2000 && $_SESSION['currentScreen']<3000)) {
			$ws->setCurrentScreen($_SESSION['currentScreen']-1000);
		}
	/**************************************************************************
	 * EXECUTE SEARCH
	 **************************************************************************/
	} elseif ($command=='executeSearch') {
		if (!isset($_POST['module'])) {
			$messagebar->addWarning("The selected module has not been installed in this system.");
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
		} elseif ($module=='PurchasingSearch') {
			if (isset($_SESSION['activeModule']) && $_SESSION['activeModule'] instanceof Purchasing) 
				$modObject = $_SESSION['activeModule'];
			else {
				$modObject = new Purchasing($link);
				$_SESSION['activeModule'] = $modObject;
			}
		} elseif ($module=='ItemSearch') {
			if (isset($_SESSION['activeModule']) && $_SESSION['activeModule'] instanceof ItemManager) 
				$modObject = $_SESSION['activeModule'];
			else {
				$modObject = new ItemManager($link);
				$_SESSION['activeModule'] = $modObject;
			}
		} elseif ($module=='AddressesSearch') {
			if (isset($_SESSION['activeModule']) && $_SESSION['activeModule'] instanceof Addresses) 
				$modObject = $_SESSION['activeModule'];
			else {
				$modObject = new Addresses($link);
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
		} elseif ($module=='EntityResourceSearch') {
			if (isset($_SESSION['activeModule']) && $_SESSION['activeModule'] instanceof EntityResource) 
				$modObject = $_SESSION['activeModule'];
			else {
				$modObject = new EntityResource($link);
				$_SESSION['activeModule'] = $modObject;
			}
		} elseif ($module=='InventoryManagerSearch') {
			if (isset($_SESSION['activeModule']) && $_SESSION['activeModule'] instanceof InventoryManager) 
				$modObject = $_SESSION['activeModule'];
			else {
				$modObject = new InventoryManager($link);
				$_SESSION['activeModule'] = $modObject;
			}
		} elseif ($module=='BOMSearch') {
			if (isset($_SESSION['activeModule']) && $_SESSION['activeModule'] instanceof BOM) 
				$modObject = $_SESSION['activeModule'];
			else {
				$modObject = new BOM($link);
				$_SESSION['activeModule'] = $modObject;
			}
		} elseif ($module=='CustomerTypesSearch') {
			if (isset($_SESSION['activeModule']) && $_SESSION['activeModule'] instanceof CustomerTypes) 
				$modObject = $_SESSION['activeModule'];
			else {
				$modObject = new CustomerTypes($link);
				$_SESSION['activeModule'] = $modObject;
			}
		} elseif ($module=='CustomerSearch') {
			if (isset($_SESSION['activeModule']) && $_SESSION['activeModule'] instanceof Customer) 
				$modObject = $_SESSION['activeModule'];
			else {
				$modObject = new Customer($link);
				$_SESSION['activeModule'] = $modObject;
			}
		} elseif ($module=='CustomerDCSearch') {
			if (isset($_SESSION['activeModule']) && $_SESSION['activeModule'] instanceof CustomerDC) 
				$modObject = $_SESSION['activeModule'];
			else {
				$modObject = new CustomerDC($link);
				$_SESSION['activeModule'] = $modObject;
			}
		} elseif ($module=='CustomerStoreTypesSearch') {
			if (isset($_SESSION['activeModule']) && $_SESSION['activeModule'] instanceof CustomerTypes) 
				$modObject = $_SESSION['activeModule'];
			else {
				$modObject = new CustomerTypes($link);
				$_SESSION['activeModule'] = $modObject;
			}
		} elseif ($module=='CustomerStoresSearch') {
			if (isset($_SESSION['activeModule']) && $_SESSION['activeModule'] instanceof CustomerStores) 
				$modObject = $_SESSION['activeModule'];
			else {
				$modObject = new CustomerStores($link);
				$_SESSION['activeModule'] = $modObject;
			}
		} elseif ($module=='ConsumersSearch') {
			if (isset($_SESSION['activeModule']) && $_SESSION['activeModule'] instanceof Consumers) 
				$modObject = $_SESSION['activeModule'];
			else {
				$modObject = new Consumers($link);
				$_SESSION['activeModule'] = $modObject;
			}
		} elseif ($module=='SalesOrdersSearch') {
			if (isset($_SESSION['activeModule']) && $_SESSION['activeModule'] instanceof SalesOrders) 
				$modObject = $_SESSION['activeModule'];
			else {
				$modObject = new SalesOrders($link);
				$_SESSION['activeModule'] = $modObject;
			}
		} elseif ($module=='BOMStepsSearch') {
			if (isset($_SESSION['activeModule']) && $_SESSION['activeModule'] instanceof BOMSteps)
				$modObject = $_SESSION['activeModule'];
			else {
				$modObject = new BOMSteps($link);
				$_SESSION['activeModule'] = $modObject;
			}
		} else {
			$messagebar->addWarning("The selected module has not been installed in this system.");
			$link->close();
			return;			
		}
		if (is_null($modObject)) {
			$messagebar->addWarning("The selected module is not available at the moment.  Please wait a few minutes and try again.");
			$link->close();
			return;
		}
		$modObject->executeSearch($searchParameters);
	/**************************************************************************
	 * VIEW RECORD
	 **************************************************************************/
	} elseif ($command=='viewRecord') {
		if (!isset($_POST['module'])) {
			$messagebar->addWarning("The selected module has not been installed in this system.");
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
		} elseif ($module=='Purchasing') {
			$modObject = new Purchasing($link);
		} elseif ($module=='ItemManager') {
			$modObject = new ItemManager($link);
		} elseif ($module=='Addresses') {
			$modObject = new Addresses($link);
		} elseif ($module=='Vendor') {
			$modObject = new Vendor($link);
		} elseif ($module=='VendorCatalog') {
			$modObject = new VendorCatalog($link);
		} elseif ($module=='EntityResource') {
			$modObject = new EntityResource($link);
		} elseif ($module=='InventoryManager') {
			$modObject = new InventoryManager($link);
		} elseif ($module=='BOM') {
			$modObject = new BOM($link);
		} elseif ($module=='CustomerTypes') {
			$modObject = new CustomerTYpes($link);
		} elseif ($module=='Customer') {
			$modObject = new Customer($link);
		} elseif ($module=='CustomerDC') {
			$modObject = new CustomerDC($link);
		} elseif ($module=='CustomerStoreTypes') {
			$modObject = new CustomerStoreTypes($link);
		} elseif ($module=='CustomerStores') {
			$modObject = new CustomerStores($link);
		} elseif ($module=='Consumers') {
			$modObject = new Consumers($link);
		} elseif ($module=='SalesOrders') {
			$modObject = new SalesOrders($link);
		} elseif ($module=='BOMSteps') {
			$modObject = new BOMSteps($link);
		} else {
			$messagebar->addWarning("The selected module has not been installed in this system.");
			$link->close();
			return;			
		}
		if (is_null($modObject)) {
			$messagebar->addWarning("The selected module is not available at the moment.  Please wait a few minutes and try again.");
			$link->close();
			return;
		}
		if (!($modObject->isIDValid($id))) {
			$messagebar->addError("The selected record ID is not valid for this module.");
			$link->close();
			return;
		}
		$modObject->display($id);
	/**************************************************************************
	 * DISPLAY NEW RECORD FORM
	 **************************************************************************/
	} elseif ($command=='newRecord') {
		$modObject = null;
		if (!isset($_SESSION['currentScreen'])) {
			$messagebar->addError("Please select a module first.");
			$link->close();
			return;
		}
		switch ($_SESSION['currentScreen']%1000) {
			case 5: $modObject = new Vendor($link); break;
			case 7: $modObject = new Purchasing($link); break;
			case 12: $modObject = new Addresses($link); break;
			case 13: $modObject = new ItemManager($link); break;
			case 19: $modObject = new BOM($link); break;
			case 21: $modObject = new EntityResource($link); break;
			case 23: $modObject = new CustomerTypes($link); break;
			case 24: $modObject = new Customer($link); break;
			case 25: $modObject = new CustomerDC($link); break;
			case 26: $modObject = new CustomerStoreTypes($link); break;
			case 27: $modObject = new CustomerStores($link); break;
			case 28: $modObject = new Consumers($link); break;
			case 44: $modObject = new SalesOrders($link); break;
			case 63: $modObject = new BOMSteps($link); break;
		}
		if (is_null($modObject)) {
			$messagebar->addWarning("The selected module is not available at the moment.  Please wait a few minutes and try again.");
			$link->close();
			return;
		}
		$modObject->newRecord();
	/**************************************************************************
	 * DISPLAY EDIT RECORD FORM
	 **************************************************************************/
	} elseif ($command=='editRecord') {
		$modObject = null;
		if (!isset($_SESSION['currentScreen'])) {
			$messagebar->addError("Please select a module first.");
			$link->close();
			return;
		}
		$module = $_POST['module'];
		$id = $_POST['id'];
		$modObject = null;
		if ($module=='Entity') {
			$modObject = new Entity($link);
		} elseif ($module=='Purchasing') {
			$modObject = new Purchasing($link);
		} elseif ($module=='ItemManager') {
			$modObject = new ItemManager($link);
		} elseif ($module=='Addresses') {
			$modObject = new Addresses($link);
		} elseif ($module=='Vendor') {
			$modObject = new Vendor($link);
		} elseif ($module=='VendorCatalog') {
			$modObject = new VendorCatalog($link);
		} elseif ($module=='EntityResource') {
			$modObject = new EntityResource($link);
		} elseif ($module=='BOM') {
			$modObject = new BOM($link);
		} elseif ($module=='CustomerTypes') {
			$modObject = new CustomerTYpes($link);
		} elseif ($module=='Customer') {
			$modObject = new Customer($link);
		} elseif ($module=='CustomerDC') {
			$modObject = new CustomerDC($link);
		} elseif ($module=='CustomerStoreTypes') {
			$modObject = new CustomerStoreTypes($link);
		} elseif ($module=='CustomerStores') {
			$modObject = new CustomerStores($link);
		} elseif ($module=='Consumers') {
			$modObject = new Consumers($link);
		} elseif ($module=='SalesOrders') {
			$modObject = new SalesOrders($link);
		} elseif ($module=='BOMSteps') {
			$modObject = new BOMSteps($link);
		} else {
			$messagebar->addWarning("The selected module has not been installed in this system.");
			$link->close();
			return;			
		}
		if (is_null($modObject)) {
			$messagebar->addWarning("The selected module is not available at the moment.  Please wait a few minutes and try again.");
			$link->close();
			return;
		}
		$modObject->editRecord($id);
	/**************************************************************************
	 * COMMIT INSERTED OR UPDATED RECORD
	 **************************************************************************/
	} elseif ($command=='insertRecord' || $command=='updateRecord') {
		// NOTE: all text in $_POST['module'] will be lower case!
		$modObject = null;
		if (!isset($_POST['module'])) {
			$messagebar->addError("Please select a module first.");
			$link->close();
			return;
		}
		if ($_POST['module']=='itemmanager') {
			$modObject = new ItemManager($link);
		} elseif ($_POST['module']=='purchasing') {
			$modObject = new Purchasing($link);
		} elseif ($_POST['module']=='bom') {
			$modObject = new BOM($link);
		} elseif ($_POST['module']=='entityresource') {
			$modObject = new EntityResource($link);
		} elseif ($_POST['module']=='addresses') {
			$modObject = new Addresses($link);
		} elseif ($_POST['module']=='customer') {
			$modObject = new Customer($link);
		} elseif ($_POST['module']=='salesorders') {
			$modObject = new SalesOrders($link);
		} elseif ($_POST['module']=='bomsteps') {
			$modObject = new BOMSteps($link);
		}
		if (is_null($modObject)) {
			$messagebar->addWarning("The selected module is not available at the moment.  Please wait a few minutes and try again.");
			$link->close();
			return;
		}
		if ($command=='insertRecord')
			$modObject->insertRecord();
		else 
			$modObject->updateRecord();
	/**************************************************************************
	 * ATTACH/DETACH FILE
	 **************************************************************************/
	} elseif ($command=='attachFile') {
		$att = new Attachments($link);
		$att->insertRecord();
	} elseif ($command=='detachFile') {
		$att = new Attachments($link);
		$att->removeRecord();
	/**************************************************************************
	 * ADD/REMOVE NOTE
	 **************************************************************************/
	} elseif ($command=='addNote') {
		$mod = new Notes($link);
		$mod->insertRecord();
	} elseif ($command=='removeNote') {
		$mod = new Notes($link);
		$mod->removeRecord();
	/**************************************************************************
	 * LOGOFF
	 **************************************************************************/
	} elseif ($command=='logoff') {
		$_SESSION['link']->close();
		unset($_SESSION['link']);
	/**************************************************************************
	 * CLEAR MESSAGES
	 **************************************************************************/
	} elseif ($command=='clearMessages') {
		$messagebar->clear();
	/**************************************************************************
	 * EMBEDDED FUNCTIONS
	 **************************************************************************/
	} elseif ($command=='embedded') {
		if (!isset($_POST['module']) || !isset($_POST['mode']) || !isset($_POST['id']) || !isset($_POST['data'])) {
			$messagebar->addError("An invalid JQ Embedded command has been received.");
		} else {
			$module = $_POST['module'];
			$mode = $_POST['mode'];
			$modObject = null;
			if ($module=='addresses') {
				$modObject = new Addresses($link);
			} elseif ($module=='ItemManager') {
				$modObject = new ItemManager($link);
			} else {
				$messagebar->addWarning("The requested JQ Embedded module is not installed in this system.");
			}
			if (!is_null($modObject)) {
				echo $modObject->embed($_POST['id'],$_POST['mode'],$_POST['data']);
			}
		}
	/**************************************************************************
	 * UNKNOWN COMMAND
	 **************************************************************************/
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