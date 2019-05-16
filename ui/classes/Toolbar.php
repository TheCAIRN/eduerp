<?php
class Toolbar {
	public function __construct() {
		
	} // __construct()
	public function render() {
		$html = '<BUTTON class="toolbarButton" id="homeButton" title="Home" onClick="mainMenu();">H</BUTTON>';
		if (isset($_SESSION['currentScreen'])) {
			$cs = $_SESSION['currentScreen'];
			$mod = '';
			$subscreens = array(0,2,3,4,9,17,29,34);
			switch ($cs) {
				case 1: $mod="'EntitySearch'"; break;
				case 5: $mod="'VendorSearch'"; break;
				case 7: $mod="'PurchasingSearch'"; break;
				case 12: $mod="'AddressesSearch'"; break;
				case 13: $mod="'ItemSearch'"; break;
				case 20: $mod="'VendorCatalogSearch'"; break;
				case 2002: 
				case 3002: $mod="'Entity'"; break;
				case 2005: 
				case 3005: $mod="'Vendor'"; break;
				case 2007: 
				case 3007: $mod="'Purchasing'"; break;
				case 2012:
				case 3012: $mod="'Addresses'"; break;
				case 2013: 
				case 3013: $mod="'ItemManager'"; break;
				case 2019:
				case 3019: $mod="'BOM'"; break;
				case 2020: 
				case 3020: $mod="'VendorCatalog'"; break;
				case 2023:
				case 3023: $mod="'CustomerTypes'"; break;
				case 2024:
				case 3024: $mod="'Customer'"; break;
				case 2025:
				case 3025: $mod="'CustomerDC'"; break;
				case 2026:
				case 3026: $mod="'CustomerStoreTypes'"; break;
				case 2027:
				case 3027: $mod="'CustomerStores'"; break;
				case 2028:
				case 3028: $mod="'Consumers'"; break;
			}
			if ($cs >= 1 && $cs < 1000 && array_search($cs,$subscreens)==false) {
				// Submenu or search
				$html .= '<BUTTON class="toolbarButton" id="clearButton" title="Clear">C</BUTTON>';
				$html .= '<BUTTON class="toolbarButton" id="executeButton" title="Execute" onClick="executeSearch('.$mod.');">X</BUTTON>';
			}
			if ($cs >= 1000 && $cs < 2000) {
				// Search results list
				
			}
			if ($cs >= 2000 && $cs < 3000) {
				// View record
				if (!isset($_SESSION['idarray']) || count($_SESSION['idarray'])<5) $_SESSION['idarray'] = array(0,0,0,0,0);
				$html .= '<BUTTON class="toolbarButton" id="firstButton" title="First" onClick="viewRecord('.$mod.','.$_SESSION['idarray'][0].');">&lt;&lt;</BUTTON>';
				$html .= '<BUTTON class="toolbarButton" id="prevButton" title="Previous" onClick="viewRecord('.$mod.','.$_SESSION['idarray'][1].');">&lt;</BUTTON>';
				$html .= '<LABEL class="toolbarLabel" id="currentRecordNumber">'.$_SESSION['idarray'][2].'</LABEL>';
				$html .= '<BUTTON class="toolbarButton" id="nextButton" title="Next" onClick="viewRecord('.$mod.','.$_SESSION['idarray'][3].');">&gt;</BUTTON>';
				$html .= '<BUTTON class="toolbarButton" id="lastButton" title="Last" onClick="viewRecord('.$mod.','.$_SESSION['idarray'][4].');">&gt;&gt;</BUTTON>';
				$html .= '<BUTTON class="toolbarButton" id="listResults" title="Results" onClick="returnToResultsList('.($cs-1000).')">L</BUTTON>';
			}
			if (!in_array($cs,$subscreens))
				$html .= '<BUTTON class="toolbarButton" id="newRecordButton" title="New Record" onClick="newRecord();">N</BUTTON>';
			if ($cs >= 3000 && $cs < 4000) {
				// Edit record
				$html .= '<BUTTON class="toolbarButton" id="newSearchButton" title="New Search" onClick="newSearch('.$mod.');">8</BUTTON>';
				$html .= '&nbsp;';
				$html .= '<BUTTON class="toolbarButton" id="saveButton" title="Save" onClick="saveRecord('.$mod.');">S</BUTTON>';
			}
		}
		echo $html;
	} // render()
} // class Toolbar
?>