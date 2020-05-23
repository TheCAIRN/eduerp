<?php
class Toolbar {
	public function __construct() {
		
	} // __construct()
	public function render() {
		$html = '';
		$html .= '<BUTTON class="toolbarButton" id="logoutButton" title="Log out" onClick="logout();">[X</BUTTON>';
		$html .= '<BUTTON class="toolbarButton" id="passwordButton" title="Change Password" onClick="changePassword();">PW</BUTTON>';
		$html .= '<BUTTON class="toolbarButton" id="homeButton" title="Home" onClick="mainMenu();">|^|</BUTTON>';
		if (isset($_SESSION['currentScreen'])) {
			$cs = $_SESSION['currentScreen'];
			$mod = '';
			$subscreens = array(0,2,3,4,9,10,17,29,34,47);
			switch ($cs) {
				case 1: $mod="'EntitySearch'"; break;
				case 5: $mod="'VendorSearch'"; break;
				case 7: $mod="'PurchasingSearch'"; break;
				case 8: $mod="'ProductionSearch'"; break;
				case 12: $mod="'AddressesSearch'"; break;
				case 13: $mod="'ItemSearch'"; break;
				case 18: $mod="'InventoryManagerSearch'"; break;
				case 19: $mod="'BOMSearch'"; break;
				case 20: $mod="'VendorCatalogSearch'"; break;
				case 23: $mod="'CustomerTypesSearch'"; break;
				case 24: $mod="'CustomerSearch'"; break;
				case 25: $mod="'CustomerDCSearch'"; break;
				case 26: $mod="'CustomerStoreTypesSearch'"; break;
				case 27: $mod="'CustomerStoresSearch'"; break;
				case 28: $mod="'ConsumersSearch'"; break;
				case 37: $mod="'GLAccountsSearch'"; break;
				case 44: $mod="'SalesOrdersSearch'"; break;
				case 48: $mod="'SystemOptionsSearch'"; break;
				case 59: $mod="'TermsSearch'"; break;
				case 1002:
				case 2002: 
				case 3002: 
				case 4002: $mod="'Entity'"; break;
				case 1005:
				case 2005: 
				case 3005: 
				case 4005: $mod="'Vendor'"; break;
				case 1007:
				case 2007: 
				case 3007: 
				case 4007: $mod="'Purchasing'"; break;
				case 1008:
				case 2008:
				case 3008:
				case 4008: $mod="'Production'"; break;
				case 1012:
				case 2012:
				case 3012: 
				case 4012: $mod="'Addresses'"; break;
				case 1013:
				case 2013: 
				case 3013: 
				case 4013: $mod="'ItemManager'"; break;
				case 1018:
				case 2018: $mod="'InventoryManager'"; break;
				case 1019:
				case 2019:
				case 3019: 
				case 4019: $mod="'BOM'"; break;
				case 1020:
				case 2020: 
				case 3020: $mod="'VendorCatalog'"; break;
				case 1023:
				case 2023:
				case 3023:
				case 4023: $mod="'CustomerTypes'"; break;
				case 1024:
				case 2024:
				case 3024: 
				case 4024: $mod="'Customer'"; break;
				case 1025:
				case 2025:
				case 3025: $mod="'CustomerDC'"; break;
				case 1026:
				case 2026:
				case 3026: $mod="'CustomerStoreTypes'"; break;
				case 1027:
				case 2027:
				case 3027: $mod="'CustomerStores'"; break;
				case 1028:
				case 2028:
				case 3028: $mod="'Consumers'"; break;
				case 1037:
				case 2037:
				case 3037:
				case 4037: $mod="'GLAccounts'"; break;
				case 1044:
				case 2044: 
				case 3044: 
				case 4044: $mod="'SalesOrders'"; break;
				case 1048:
				case 2048:
				// case 3048: System Options cannot be created.
				case 4048: $mod = "'SystemOptions'"; break;
				case 1059:
				case 2059:
				case 3059:
				case 4059: $mod="'Terms'"; break;
				case 1063: 
				case 2063:
				case 3063:
				case 4063: $mod="'BOMSteps'"; break;
			}
			if ($cs >= 1 && $cs < 1000 && array_search($cs,$subscreens)==false) {
				// Submenu or search
				$html .= '<BUTTON class="toolbarButton" id="clearButton" title="Clear">C</BUTTON>';
				$html .= '<BUTTON class="toolbarButton" id="executeButton" title="Execute" onClick="executeSearch('.$mod.');">X</BUTTON>';
			}
			if ($cs >= 1000 && $cs < 2000) {
				// Search results list
				$html .= '<BUTTON class="toolbarButton" id="newSearchButton" title="New Search" onClick="newSearch('.$mod.');">8</BUTTON>';
				
			}
			if ($cs >= 2000 && $cs < 3000) {
				// View record
				if (!isset($_SESSION['idarray']) || count($_SESSION['idarray'])<5) $_SESSION['idarray'] = array(0,0,0,0,0);
				$html .= '<BUTTON class="toolbarButton" id="firstButton" title="First" onClick="viewRecord('.$mod.','.$_SESSION['idarray'][0].');">&lt;&lt;</BUTTON>';
				$html .= '<BUTTON class="toolbarButton" id="prevButton" title="Previous" onClick="viewRecord('.$mod.','.$_SESSION['idarray'][1].');">&lt;</BUTTON>';
				$html .= '<LABEL class="toolbarLabel" id="currentRecordNumber">'.$_SESSION['idarray'][2].'</LABEL>';
				$html .= '<BUTTON class="toolbarButton" id="nextButton" title="Next" onClick="viewRecord('.$mod.','.$_SESSION['idarray'][3].');">&gt;</BUTTON>';
				$html .= '<BUTTON class="toolbarButton" id="lastButton" title="Last" onClick="viewRecord('.$mod.','.$_SESSION['idarray'][4].');">&gt;&gt;</BUTTON>';
				$html .= '<BUTTON class="toolbarButton" id="listResultsButton" title="Results" onClick="returnToResultsList('.($cs-1000).')">L</BUTTON>';
				if ($cs!=2018) {
					// Inventory Management can't edit records.
					$html .= '&nbsp;';
					$html .= '<BUTTON class="toolbarButton" id="editRecordButton" title="Edit Record" onClick="editRecord('.$mod.','.$_SESSION['idarray'][2].');">E</BUTTON>'; 
					$html .= '<BUTTON class="toolbarButton" id="copyRecordButton" title="Copy Record" onClick="copyRecord('.$mod.','.$_SESSION['idarray'][2].');">C</BUTTON>';
				}
				$html .= '&nbsp;';
			}
			if (!in_array($cs,$subscreens) && $cs%1000!=18) {
				// Inventory Management and subscreens can't create new records.
				$html .= '<BUTTON class="toolbarButton" id="newRecordButton" title="New Record" onClick="newRecord();">N</BUTTON>';
				$html .= '<BUTTON class="toolbarButton" id="importRecordsButton" title="Import Records" onClick="importRecords();">I</BUTTON>';
			}
			if ($cs >= 3000 && $cs < 4000) {
				// Create record
				$html .= '<BUTTON class="toolbarButton" id="newSearchButton" title="New Search" onClick="newSearch('.$mod.');">8</BUTTON>';
				$html .= '&nbsp;';
				$html .= '<BUTTON class="toolbarButton" id="saveButton" title="Save" onClick="saveRecord('.$mod.');">S</BUTTON>';
			}
			if ($cs >= 4000 && $cs < 5000) {
				// Update record
				if (!isset($_SESSION['idarray']) || count($_SESSION['idarray'])<5) $_SESSION['idarray'] = array(0,0,0,0,0);
				$html .= '<BUTTON class="toolbarButton" id="viewRecordButton" title="View Record" onClick="viewRecord('.$mod.','.$_SESSION['idarray'][2].');">V</BUTTON>';
				$html .= '<BUTTON class="toolbarButton" id="undoButton" title="Undo Changes" onClick="editRecord('.$mod.','.$_SESSION['idarray'][2].');">U</BUTTON>'; 
				$html .= '<BUTTON class="toolbarButton" id="saveButton" title="Save" onClick="saveRecord('.$mod.');">S</BUTTON>';
			}
		}
		echo $html;
	} // render()
} // class Toolbar
?>
