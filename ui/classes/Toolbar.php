<?php
class Toolbar {
	public function __construct() {
		
	} // __construct()
	public function render() {
		$html = '<BUTTON class="toolbarButton" id="homeButton" title="Home">H</BUTTON>';
		if (isset($_SESSION['currentScreen'])) {
			$cs = $_SESSION['currentScreen'];
			if ($cs >= 1 && $cs < 100) {
				// Submenu or search
				$html .= '<BUTTON class="toolbarButton" id="clearButton" title="Clear">C</BUTTON>';
				$html .= '<BUTTON class="toolbarButton" id="executeButton" title="Execute">X</BUTTON>';
			}
			if ($cs >= 100 && $cs < 200) {
				// Search results list
				
			}
			if ($cs >= 200 && $cs < 300) {
				// View record
				$html .= '<BUTTON class="toolbarButton" id="firstButton" title="First">&lt;&lt;</BUTTON>';
				$html .= '<BUTTON class="toolbarButton" id="prevButton" title="Previous">&lt;</BUTTON>';
				$html .= '<BUTTON class="toolbarButton" id="nextButton" title="Next">&gt;</BUTTON>';
				$html .= '<BUTTON class="toolbarButton" id="lastButton" title="Last">&gt;&gt;</BUTTON>';
			}
		}
		echo $html;
	} // render()
} // class Toolbar
?>