<?php
class Toolbar {
	public function __construct() {
		
	} // __construct()
	public function render() {
		$html = '<BUTTON class="toolbarButton" id="homeButton" title="Home" onClick="mainMenu();">H</BUTTON>';
		if (isset($_SESSION['currentScreen'])) {
			$cs = $_SESSION['currentScreen'];
			if ($cs >= 1 && $cs < 100) {
				// Submenu or search
				$mod = '';
				if ($cs==1) $mod="'EntitySearch'";
				$html .= '<BUTTON class="toolbarButton" id="clearButton" title="Clear">C</BUTTON>';
				$html .= '<BUTTON class="toolbarButton" id="executeButton" title="Execute" onClick="executeSearch('.$mod.');">X</BUTTON>';
			}
			if ($cs >= 100 && $cs < 200) {
				// Search results list
				
			}
			if ($cs >= 200 && $cs < 300) {
				// View record
				$mod = '';
				switch ($cs) {
					case 202: $mod="'Entity'"; break;
					case 205: $mod="'Vendor'"; break;
					case 210: $mod="'ItemManager'"; break;
				}
				if (!isset($_SESSION['idarray']) || count($_SESSION['idarray'])<5) $_SESSION['idarray'] = array(0,0,0,0,0);
				$html .= '<BUTTON class="toolbarButton" id="firstButton" title="First" onClick="viewRecord('.$mod.','.$_SESSION['idarray'][0].');">&lt;&lt;</BUTTON>';
				$html .= '<BUTTON class="toolbarButton" id="prevButton" title="Previous" onClick="viewRecord('.$mod.','.$_SESSION['idarray'][1].');">&lt;</BUTTON>';
				$html .= '<LABEL class="toolbarLabel" id="currentRecordNumber">'.$_SESSION['idarray'][2].'</LABEL>';
				$html .= '<BUTTON class="toolbarButton" id="nextButton" title="Next" onClick="viewRecord('.$mod.','.$_SESSION['idarray'][3].');">&gt;</BUTTON>';
				$html .= '<BUTTON class="toolbarButton" id="lastButton" title="Last" onClick="viewRecord('.$mod.','.$_SESSION['idarray'][4].');">&gt;&gt;</BUTTON>';
			}
		}
		echo $html;
	} // render()
} // class Toolbar
?>