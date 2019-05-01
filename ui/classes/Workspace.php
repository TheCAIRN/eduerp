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
			case 5: $vend = new Vendor($this->dbconn); echo $vend->searchPage(); break;
			case 6: $freight = new Freight($this->dbconn); echo $freight->searchPage(); break;
			case 7: $purch = new Purchasing($this->dbconn); echo $purch->searchPage(); break;
			case 8: $prod = new Production($this->dbconn); echo $prod->searchPage(); break;
			case 9: echo $this->customerSubMenu(); break; 
			case 10: $sales = new Sales($this->dbconn); echo $sales->searchPage(); break;
			case 11: /* TO DO People */ break;
			case 12: $addr = new Addresses($this->dbconn); echo $addr->searchPage(); break;
			case 13: $item = new ItemManager($this->dbconn); echo $item->searchPage(); break;
			case 14: $item = new ItemAttributes($this->dbconn); echo $item->searchPage(); break;
			case 15: $item = new ItemCategories($this->dbconn); echo $item->searchPage(); break;
			case 16: $item = new ItemTypes($this->dbconn); echo $item->searchPage(); break;
			case 17: echo $this->gtinSubmenu(); break;
			case 18: $item = new InventoryManager($this->dbconn); echo $item->searchPage(); break;
			case 19: $bom = new BOM($this->dbconn); echo $bom->searchPage(); break;
			case 20: $vc = new VendorCatalog($this->dbconn); echo $vc->searchPage(); break;
			case 21: $entres = new EntityResource($this->dbconn); echo $entres->searchPage(); break;
			case 22: $cc = new CustomerCatalog($this->dbconn); echo $cc->searchPage(); break;
			case 23: $ct = new CustomerTypes($this->dbconn); echo $ct->searchPage(); break;
			case 24: $cust = new Customer($this->dbconn); echo $cust->searchPage(); break;
			case 25: $dc = new CustomerDC($this->dbconn); echo $dc->searchPage(); break;
			case 26: $st = new CustomerStoreTypes($this->dbconn); echo $st->searchPage(); break;
			case 27: $st = new CustomerStores($this->dbconn); echo $st->searchPage(); break;
			case 28: $cc = new Consumers($this->dbconn); echo $cc->searchPage(); break;
			case 29: echo $this->insightSubmenu(); break;
			case 30: $dsu = new DashboardSetup($this->dbconn); echo $dsu->searchPage(); break;
			case 31: $rsu = new ReportSetup($this->dbconn); echo $rsu->searchPage(); break;
			case 32: $db = new Dashboards($this->dbconn); echo $db->searchPage(); break;
			case 33: $rpt = new Reports($this->dbconn); echo $rpt->searchPage(); break;
			/* 1000-1999: List Records */
			case 1002: $ent = new Entity($this->dbconn); $ent->listRecords(); break;
			case 1005: $vend = new Vendor($this->dbconn); $vend->listRecords(); break;
			case 1012: $addr = new Addresses($this->dbconn); $addr->listRecords(); break;
			case 1013: $item = new ItemManager($this->dbconn); $item->listRecords(); break;
			case 1019: $bom = new BOM($this->dbconn); $bom->listRecords(); break;
			case 1020: $vc = new VendorCatalog($this->dbconn); $vc->listRecords(); break;
			case 1021: $entres = new EntityResource($this->dbconn); $entres->listRecords(); break;
			case 1023: $ct = new CustomerTypes($this->dbconn); $ct->listRecords(); break;
			case 1024: $cust = new Customer($this->dbconn); $cust->listRecords(); break;
			case 1025: $dc = new CustomerDC($this->dbconn); $dc->listRecords(); break;
			case 1026: $st = new CustomerStoreTypes($this->dbconn); $st->listRecords(); break;
			case 1027: $st = new CustomerStores($this->dbconn); $st->listRecords(); break;
			case 1028: $cc = new Consumers($this->dbconn); $cc->listRecords(); break;
			/* 2000-2999: Display record */
			case 2002: $ent = new Entity($this->dbconn); $ent->display($_SESSION['idarray'][2]); break;
			case 2005: $vend = new Vendor($this->dbconn); $vend->display($_SESSION['idarray'][2]); break;
			case 2012: $addr = new Addresses($this->dbconn); $addr->display($_SESSION['idarray'][2]); break;
			case 2013: $item = new ItemManager($this->dbconn); $item->display($_SESSION['idarray'][2]); break;
			case 2019: $bom = new BOM($this->dbconn); $bom->display($_SESSION['idarray'][2]); break;
			case 2020: $vc = new VendorCatalog($this->dbconn); $vc->display($_SESSION['idarray'][2]); break;
			case 2021: $entres = new EntityResource($this->dbconn); $entres->display($_SESSION['idarray'][2]); break;
			case 2023: $ct = new CustomerTypes($this->dbconn); $ct->display($_SESSION['idarray'][2]); break;
			case 2024: $cust = new Customer($this->dbconn); $cust->display($_SESSION['idarray'][2]); break;
			case 2025: $dc = new CustomerDC($this->dbconn); $dc->display($_SESSION['idarray'][2]); break;
			case 2026: $st = new CustomerStoreTypes($this->dbconn); $st->display($_SESSION['idarray'][2]); break;
			case 2027: $st = new CustomerStores($this->dbconn); $st->display($_SESSION['idarray'][2]); break;
			case 2028: $cc = new Consumers($this->dbconn); $cc->display($_SESSION['idarray'][2]); break;
			/* 3000-3999: New Record */
			case 3007: $purch = new Purchasing($this->dbconn); $purch->newRecord(); break;
			case 3012: $addr = new Addresses($this->dbconn); $addr->newRecord(); break;
			case 3019: $bom = new BOM($this->dbconn); $bom->newRecord(); break;
			case 3021: $entres = new EntityResource($this->dbconn); $entres->newRecord(); break;
			case 3023: $ct = new CustomerTypes($this->dbconn); $ct->newRecord(); break;
			case 3024: $cust = new Customer($this->dbconn); $cust->newRecord(); break;
			case 3025: $dc = new CustomerDC($this->dbconn); $dc->newRecord(); break;
			case 3026: $st = new CustomerStoreTypes($this->dbconn); $st->newRecord(); break;
			case 3027: $st = new CustomerStores($this->dbconn); $st->newRecord(); break;
			case 3028: $cc = new Consumers($this->dbconn); $cc->newRecord(); break;
		}
		
	} // render()
	public function setCurrentScreen($screen) {
		if (!is_numeric($screen) && !ctype_digit($screen)) return;
		if ($screen < 0 || $screen > 3999) return;
		$this->currentScreen = $screen;
		$_SESSION['currentScreen'] = $screen;
		$this->render();
	}
	private function dashboard() {
		// TODO: Only display those modules the user has permissions to.
		$module_list = ['Entities','Core Lookups','Contacts','Items','Vendors','Freight','Purchasing','Production','Customers','Sales','Insights'];
		$html = '';
		foreach ($module_list as $module) {
			$html .= '<DIV id="'.str_replace(' ','',$module).'ModuleIcon" class="DashboardIcon" onClick="selectModule(this);">'.$module."</DIV>\r\n";
		}
		return $html;
	}
	private function coreSubmenu() {
		// TODO: Only display those modules the user has permissions to.
		$module_list = ['Currency','Country','Language','State','UOM Types','UOM','UOM Conversions','Terms','Note Types','Attachment Types','Cancellation Reason Codes',
			'BOM Steps'];
		$html = '';
		foreach ($module_list as $module) {
			$html .= '<DIV id="'.str_replace(' ','',$module).'ModuleIcon" class="DashboardIcon" onClick="selectModule(this);">'.$module."</DIV>\r\n";
		}
		$this->currentScreen = 2;
		return $html;		
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
		$module_list = ['Item Setup','Item Attributes','Item Categories','Item Types','GTIN Master','Inventory Lookup','Bill of Materials','Vendor Catalog','Customer Catalog'];
		$html = '';
		foreach ($module_list as $module) {
			$html .= '<DIV id="'.str_replace(' ','',$module).'ModuleIcon" class="DashboardIcon" onClick="selectModule(this);">'.$module."</DIV>\r\n";
		}
		$this->currentScreen = 4;
		return $html;
	}
	private function gtinSubmenu() {
		// TODO: Only display those modules the user has permissions to.
		$module_list = ['Mfgr ID','Generate GTIN','GTIN Manager'];
		$html = '';
		foreach ($module_list as $module) {
			$html .= '<DIV id="'.str_replace(' ','',$module).'ModuleIcon" class="DashboardIcon" onClick="selectModule(this);">'.$module."</DIV>\r\n";
		}
		$this->currentScreen = 17;
		return $html;
	}
	private function customerSubmenu() {
		$module_list = ['Customer Types','Customer','Customer DC','Customer Store Types','Customer Stores','Consumers'];
		$html = '';
		foreach ($module_list as $module) {
			$html .= '<DIV id="'.str_replace(' ','',$module).'ModuleIcon" class="DashboardIcon" onClick="selectModule(this);">'.$module."</DIV>\r\n";
		}
		$this->currentScreen = 9;
		return $html;
	}
} // class Workspace
?>