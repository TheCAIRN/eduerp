<?php
class Workspace {
	private $currentScreen;
	public function __construct() {
		if (isset($_SESSION) && isset($_SESSION['currentScreen'])) $this->currentScreen = $_SESSION['currentScreen'];
		else $this->currentScreen = 0;
	} // construct()
	public function render() {
		switch ($this->currentScreen) {
			case 0: echo $this->dashboard();
		}
		
	} // render()
	public function setCurrentScreen($screen) {
		if (!is_numeric($screen) && !ctype_digit($screen)) return;
		if ($screen < 0 || $screen > 0) return;
		$this->currentScreen = $screen;
		$_SESSION['currentScreen'] = $screen;
		$this->render();
	}
	private function dashboard() {
		// TODO: Only display those modules the user has permissions to.
		$module_list = ['Entities','Core Lookups','Contacts','Items','Vendors','Freight','Purchasing'];
		$html = '';
		foreach ($module_list as $module) {
			$html .= '<DIV id="'.str_replace(' ','',$module).'ModuleIcon" class="DashboardIcon" onClick="selectModule(this);">'.$module."</DIV>\r\n";
		}
		return $html;
	}
} // class Workspace
?>