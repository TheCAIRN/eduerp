<?php
class Workspace {
	private $currentScreen;
	private $dbconn;
	public function __construct($link=null) {
		if (isset($_SESSION) && isset($_SESSION['currentScreen'])) $this->currentScreen = $_SESSION['currentScreen'];
		else $this->currentScreen = 0;
		$this->dbconn = $link;
	} // construct()
	public function render() {
		switch ($this->currentScreen) {
			case 0: echo $this->dashboard(); break;
			case 1: $ent = new Entity($this->dbconn); echo $ent->searchPage(); break;
			case 2: echo $this->coreSubmenu(); break;
			case 3: echo $this->contactSubmenu(); break;
			case 4: echo $this->itemSubmenu(); break;
			case 5: $vend = new Vendors($this->dbconn); echo $vend->searchPage(); break;
			case 6: $freight = new Freight($this->dbconn); echo $freight->searchPage(); break;
			case 7: $purch = new Purchasing($this->dbconn); echo $purch->searchPage(); break;
			case 8: /* TO DO People */ break;
			case 9: /* TO DO Addresses */ break;
			case 10: $item = new ItemManager($this->dbconn); echo $item->searchPage(); break;
			case 102: $ent = new Entity($this->dbconn); $ent->listRecords(); break;
			case 110: $item = new ItemManager($this->dbconn); $item->listRecords(); break;
			case 202: $ent = new Entity($this->dbconn); $ent->display($_SESSION['currentID']); break;
		}
		
	} // render()
	public function setCurrentScreen($screen) {
		if (!is_numeric($screen) && !ctype_digit($screen)) return;
		if ($screen < 0 || $screen > 202) return;
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
	private function coreSubmenu() {
		
	}
	private function contactSubmenu() {
		// TODO: Only display those modules the user has permissions to.
		$module_list = ['Addresses','Person'];
		$html = '';
		foreach ($module_list as $module) {
			$html .= '<DIV id="'.str_replace(' ','',$module).'ModuleIcon" class="DashboardIcon" onClick="selectModule(this);">'.$module."</DIV>\r\n";
		}
		$this->currentScreen = 3;
		return $html;
	}
	private function itemSubmenu() {
		// TODO: Only display those modules the user has permissions to.
		$module_list = ['Item Setup','Item Attributes','Item Categories','Item Types','GTIN Master','Inventory Lookup'];
		$html = '';
		foreach ($module_list as $module) {
			$html .= '<DIV id="'.str_replace(' ','',$module).'ModuleIcon" class="DashboardIcon" onClick="selectModule(this);">'.$module."</DIV>\r\n";
		}
		$this->currentScreen = 4;
		return $html;
	}
} // class Workspace
?>