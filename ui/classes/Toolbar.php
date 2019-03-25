<?php
class Toolbar {
	public function __construct() {
		
	} // __construct()
	public function render() {
		$html = '<BUTTON class="toolbarButton" id="homeButton" title="Home" onClick="mainMenu();">H</BUTTON>';
		if (isset($_SESSION['currentScreen'])) {
			$cs = $_SESSION['currentScreen'];
			$mod = '';
			switch ($cs) {
				case 1: $mod="'EntitySearch'"; break;
				case 5: $mod="'VendorSearch'"; break;
				case 7: $mod="'PurchasingSearch'"; break;
				case 13: $mod="'ItemSearch'"; break;
				case 20: $mod="'VendorCatalogSearch'"; break;
				case 202: 
				case 302: $mod="'Entity'"; break;
				case 205: 
				case 305: $mod="'Vendor'"; break;
				case 207: 
				case 307: $mod="'Purchasing'"; break;
				case 213: 
				case 313: $mod="'ItemManager'"; break;
				case 219:
				case 319: $mod="'BOM'"; break;
				case 220: 
				case 320: $mod="'VendorCatalog'"; break;
			}
			if ($cs >= 1 && $cs < 100) {
				// Submenu or search
				$html .= '<BUTTON class="toolbarButton" id="clearButton" title="Clear">C</BUTTON>';
				$html .= '<BUTTON class="toolbarButton" id="executeButton" title="Execute" onClick="executeSearch('.$mod.');">X</BUTTON>';
			}
			if ($cs >= 100 && $cs < 200) {
				// Search results list
				
			}
			if ($cs >= 200 && $cs < 300) {
				// View record
				if (!isset($_SESSION['idarray']) || count($_SESSION['idarray'])<5) $_SESSION['idarray'] = array(0,0,0,0,0);
				$html .= '<BUTTON class="toolbarButton" id="firstButton" title="First" onClick="viewRecord('.$mod.','.$_SESSION['idarray'][0].');">&lt;&lt;</BUTTON>';
				$html .= '<BUTTON class="toolbarButton" id="prevButton" title="Previous" onClick="viewRecord('.$mod.','.$_SESSION['idarray'][1].');">&lt;</BUTTON>';
				$html .= '<LABEL class="toolbarLabel" id="currentRecordNumber">'.$_SESSION['idarray'][2].'</LABEL>';
				$html .= '<BUTTON class="toolbarButton" id="nextButton" title="Next" onClick="viewRecord('.$mod.','.$_SESSION['idarray'][3].');">&gt;</BUTTON>';
				$html .= '<BUTTON class="toolbarButton" id="lastButton" title="Last" onClick="viewRecord('.$mod.','.$_SESSION['idarray'][4].');">&gt;&gt;</BUTTON>';
				// ** TODO: $html .= '<BUTTON class="toolbarButton" id="listResults" title="Results" onClick="returnToResultsList();">L</BUTTON>';
			}
			if (!in_array($cs,array(0,2,3,4,17)))
				$html .= '<BUTTON class="toolbarButton" id="newRecordButton" title="New Record" onClick="newRecord();">N</BUTTON>';
			if ($cs >= 300 && $cs < 400) {
				// Edit record
				$html .= '&nbsp;';
				$html .= '<BUTTON class="toolbarButton" id="saveButton" title="Save" onClick="saveRecord('.$mod.');">S</BUTTON>';
			}
		}
		echo $html;
	} // render()
} // class Toolbar
?>