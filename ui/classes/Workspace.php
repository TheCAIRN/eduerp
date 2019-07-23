<?php
class Workspace {
	private $currentScreen;
	private $dbconn;
	private $mb;
	public function __construct($link=null) {
		if (isset($_SESSION) && isset($_SESSION['currentScreen'])) $this->currentScreen = $_SESSION['currentScreen'];
		else $this->currentScreen = 0;
		$this->dbconn = $link;
		$this->mb = new MessageBar();
	} // construct()
	public function render() {
		switch ($this->currentScreen) {
			case 0: echo $this->dashboard(); break;
			case 1: $ent = new Entity($this->dbconn); echo $ent->searchPage(); break;
			case 2: echo $this->coreSubmenu(); break;
			case 3: echo $this->contactSubmenu(); break;
			case 4: echo $this->itemSubmenu(); break;
			case 5: $vend = new Vendor($this->dbconn); echo $vend->searchPage(); break;
			case 6: echo $this->freightSubmenu(); break;
			case 7: $purch = new Purchasing($this->dbconn); echo $purch->searchPage(); break;
			case 8: $this->notInstalled("Production"); break; // $prod = new Production($this->dbconn); echo $prod->searchPage(); break;
			case 9: echo $this->customerSubMenu(); break; 
			case 10: echo $this->salesSubmenu(); break;
			case 11: $this->notInstalled("People"); /* TO DO People */ break;
			case 12: $addr = new Addresses($this->dbconn); echo $addr->searchPage(); break;
			case 13: $item = new ItemManager($this->dbconn); echo $item->searchPage(); break;
			case 14: $this->notInstalled("Item Attributes"); break; // $item = new ItemAttributes($this->dbconn); echo $item->searchPage(); break;
			case 15: $this->notInstalled("Item Categories"); break; //$item = new ItemCategories($this->dbconn); echo $item->searchPage(); break;
			case 16: $this->notInstalled("Item Types"); break; $item = new ItemTypes($this->dbconn); echo $item->searchPage(); break;
			case 17: echo $this->gtinSubmenu(); break;
			case 18: $this->notInstalled("Inventory Manager"); break; //$item = new InventoryManager($this->dbconn); echo $item->searchPage(); break;
			case 19: $bom = new BOM($this->dbconn); echo $bom->searchPage(); break;
			case 20: $vc = new VendorCatalog($this->dbconn); echo $vc->searchPage(); break;
			case 21: $entres = new EntityResource($this->dbconn); echo $entres->searchPage(); break;
			case 22: $this->notInstalled("Customer Catalog"); break; //$cc = new CustomerCatalog($this->dbconn); echo $cc->searchPage(); break;
			case 23: $this->notInstalled("Customer Types"); break; //$ct = new CustomerTypes($this->dbconn); echo $ct->searchPage(); break;
			case 24: $cust = new Customer($this->dbconn); echo $cust->searchPage(); break;
			case 25: $this->notInstalled("Customer DC"); break; //$dc = new CustomerDC($this->dbconn); echo $dc->searchPage(); break;
			case 26: $this->notInstalled("Customer Store Types"); break; //$st = new CustomerStoreTypes($this->dbconn); echo $st->searchPage(); break;
			case 27: $this->notInstalled("Customer Stores"); break; //$st = new CustomerStores($this->dbconn); echo $st->searchPage(); break;
			case 28: $this->notInstalled("Consumers"); break; //$cc = new Consumers($this->dbconn); echo $cc->searchPage(); break;
			case 29: echo $this->insightSubmenu(); break;
			case 30: $this->notInstalled("Dashboard Setup"); break; //$dsu = new DashboardSetup($this->dbconn); echo $dsu->searchPage(); break;
			case 31: $this->notInstalled("Report Setup"); break; //$rsu = new ReportSetup($this->dbconn); echo $rsu->searchPage(); break;
			case 32: $this->notInstalled("Dashboards"); break; //$db = new Dashboards($this->dbconn); echo $db->searchPage(); break;
			case 33: $this->notInstalled("Reports"); break; //$rpt = new Reports($this->dbconn); echo $rpt->searchPage(); break;
			case 34: echo $this->accountingSubmenu(); break;
			case 35: $this->notInstalled("Chart of Accounts"); break; // Chart of Accounts
			case 36: $this->notInstalled("GL Periods"); break; // GL Periods
			case 37: $this->notInstalled("GL Accounts"); break; // GL Accounts
			case 38: $this->notInstalled("GL Balances"); break; // GL Balances
			case 39: $this->notInstalled("GL Journal"); break; // GL Journal
			case 40: $this->notInstalled("Freight Vendor Types"); break; // Freight Vendor Types
			case 41: $this->notInstalled("Freight Vendors"); break; // Freight Vendors
			case 42: $this->notInstalled("Inbound Freight"); break;
			case 43: $this->notInstalled("Outbound Freight"); break;
			case 44: $so = new SalesOrders($this->dbconn); echo $so->searchPage(); break;
			case 45: $this->notInstalled("Sales Payments"); break;
			case 46: $this->notInstalled("Sales Order Types"); break;
			case 47: echo $this->adminSubmenu(); break;
			case 48: $this->notInstalled("System Options"); break;
			case 49: $this->notInstalled("User Accounts"); break;
			case 50: $this->notInstalled("Security Groups"); break;
			case 51: $this->notInstalled("Permissions"); break;
			case 52: $this->notInstalled("Currency"); break;
			case 53: $this->notInstalled("Country"); break;
			case 54: $this->notInstalled("Language"); break;
			case 55: $this->notInstalled("State"); break;
			case 56: $this->notInstalled("UOM Types"); break;
			case 57: $this->notInstalled("UOM"); break;
			case 58: $this->notInstalled("UOM Conversions"); break;
			case 59: $this->notInstalled("Terms"); break;
			case 60: $this->notInstalled("Note Types"); break;
			case 61: $this->notInstalled("Attachment Types"); break;
			case 62: $this->notInstalled("Cancellation Reason Codes"); break;
			case 63: $boms = new BOMSteps($this->dbconn); echo $boms->searchPage(); break;
			/* 1000-1999: List Records */
			case 1002: $ent = new Entity($this->dbconn); $ent->listRecords(); break;
			case 1005: $vend = new Vendor($this->dbconn); $vend->listRecords(); break;
			case 1007: $pur = new Purchasing($this->dbconn); $pur->listRecords(); break;
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
			case 1044: $so = new SalesOrders($this->dbconn); $so->listRecords(); break;
			case 1063: $boms = new BOMSteps($this->dbconn); $boms->listRecords(); break;
			/* 2000-2999: Display record */
			case 2002: $ent = new Entity($this->dbconn); $ent->display($_SESSION['idarray'][2]); break;
			case 2005: $vend = new Vendor($this->dbconn); $vend->display($_SESSION['idarray'][2]); break;
			case 2007: $pur = new Purchasing($this->dbconn); $pur->display($_SESSION['idarray'][2]); break;
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
			case 2044: $so = new SalesOrders($this->dbconn); $so->display($_SESSION['idarray'][2]); break;
			case 2063: $boms = new BOMSteps($this->dbconn); $boms->display($_SESSION['idarray'][2]); break;
			/* 3000-3999: New Record */
			//case 3002: $ent = new Entity($this->dbconn); $ent->newRecord(); break;
			case 3005: $vend = new Vendor($this->dbconn); $vend->newRecord(); break;
			case 3007: $purch = new Purchasing($this->dbconn); $purch->newRecord(); break;
			case 3012: $addr = new Addresses($this->dbconn); $addr->newRecord(); break;
			case 3013: $item = new ItemManager($this->dbconn); $item->newRecord(); break;
			case 3019: $bom = new BOM($this->dbconn); $bom->newRecord(); break;
			case 3020: $vc = new VendorCatalog($this->dbconn); $vc->newRecord(); break;
			case 3021: $entres = new EntityResource($this->dbconn); $entres->newRecord(); break;
			case 3023: $ct = new CustomerTypes($this->dbconn); $ct->newRecord(); break;
			case 3024: $cust = new Customer($this->dbconn); $cust->newRecord(); break;
			case 3025: $dc = new CustomerDC($this->dbconn); $dc->newRecord(); break;
			case 3026: $st = new CustomerStoreTypes($this->dbconn); $st->newRecord(); break;
			case 3027: $st = new CustomerStores($this->dbconn); $st->newRecord(); break;
			case 3028: $cc = new Consumers($this->dbconn); $cc->newRecord(); break;
			case 3044: $so = new SalesOrders($this->dbconn); $so->newRecord(); break;
			case 3063: $boms = new BOMSteps($this->dbconn); $boms->newRecord(); break;
			/* 4000-4999: Edit Record */
			//case 4002: $ent = new Entity($this->dbconn); $ent->editRecord($_SESSION['idarray'][2]); break;
			case 4005: $vend = new Vendor($this->dbconn); $vend->editRecord($_SESSION['idarray'][2]); break;
			case 4007: $purch = new Purchasing($this->dbconn); $purch->editRecord($_SESSION['idarray'][2]); break;
			case 4012: $addr = new Addresses($this->dbconn); $addr->editRecord($_SESSION['idarray'][2]); break;
			case 4013: $item = new ItemManager($this->dbconn); $item->editRecord($_SESSION['idarray'][2]); break;
			case 4019: $bom = new BOM($this->dbconn); $bom->editRecord($_SESSION['idarray'][2]); break;
			case 4020: $vc = new VendorCatalog($this->dbconn); $vc->editRecord($_SESSION['idarray'][2]); break;
			case 4021: $entres = new EntityResource($this->dbconn); $entres->editRecord($_SESSION['idarray'][2]); break;
			case 4023: $ct = new CustomerTypes($this->dbconn); $ct->editRecord($_SESSION['idarray'][2]); break;
			case 4024: $cust = new Customer($this->dbconn); $cust->editRecord($_SESSION['idarray'][2]); break;
			case 4025: $dc = new CustomerDC($this->dbconn); $dc->editRecord($_SESSION['idarray'][2]); break;
			case 4026: $st = new CustomerStoreTypes($this->dbconn); $st->editRecord($_SESSION['idarray'][2]); break;
			case 4027: $st = new CustomerStores($this->dbconn); $st->editRecord($_SESSION['idarray'][2]); break;
			case 4028: $cc = new Consumers($this->dbconn); $cc->editRecord($_SESSION['idarray'][2]); break;	
			case 4044: $so = new SalesOrders($this->dbconn); $so->editRecord($_SESSION['idarray'][2]); break;
			case 4063: $boms = new BOMSteps($this->dbconn); $boms->editRecord($_SESSION['idarray'][2]); break;
		}
		
	} // render()
	private function notInstalled($module) {
		$this->mb->addWarning("The $module module has not been installed on this system.");
	} // notInstalled()
	public function setCurrentScreen($screen) {
		if (!is_numeric($screen) && !ctype_digit($screen)) return;
		if ($screen < 0 || $screen > 3999) return;
		$this->currentScreen = $screen;
		$_SESSION['currentScreen'] = $screen;
		$this->render();
	} // setCurrentScreen()
	private function dashboard() {
		// TODO: Only display those modules the user has permissions to.
		$module_list = ['Admin','Entities','Core Lookups','Contacts','Items','Vendors','Freight',
			'Purchasing','Production','Customers','Sales','Insights','Accounting'];
		$html = '';
		foreach ($module_list as $module) {
			$html .= '<DIV id="'.str_replace(' ','',$module).'ModuleIcon" class="DashboardIcon" onClick="selectModule(this);">'.$module."</DIV>\r\n";
		}
		return $html;
	} // dashboard()
	private function adminSubmenu() {
		// TODO: Only display those modules the user has permissions to.
		$module_list = ['System Options','User Accounts','Security Groups','Permissions'];
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
	private function freightSubmenu() {
		$module_list = ['Freight Vendor Types','Freight Vendors','Inbound Freight','Outbound Freight'];
		$html = '';
		foreach ($module_list as $module) {
			$html .= '<DIV id="'.str_replace(' ','',$module).'ModuleIcon" class="DashboardIcon" onClick="selectModule(this);">'.$module."</DIV>\r\n";
		}
		$this->currentScreen = 6;
		return $html;
	}
	private function customerSubmenu() {
		// TODO: Only display those modules the user has permissions to.
		$module_list = ['Customer Types','Customer','Customer DC','Customer Store Types','Customer Stores','Consumers'];
		$html = '';
		foreach ($module_list as $module) {
			$html .= '<DIV id="'.str_replace(' ','',$module).'ModuleIcon" class="DashboardIcon" onClick="selectModule(this);">'.$module."</DIV>\r\n";
		}
		$this->currentScreen = 9;
		return $html;
	}
	private function salesSubmenu() {
		$module_list = ['Sales Order Types','Sales Orders','Sales Payments'];
		$html = '';
		foreach ($module_list as $module) {
			$html .= '<DIV id="'.str_replace(' ','',$module).'ModuleIcon" class="DashboardIcon" onClick="selectModule(this);">'.$module."</DIV>\r\n";
		}
		$this->currentScreen = 10;
		return $html;
	}
	private function insightSubmenu() {
		// TODO: Only display those modules the user has permissions to.
		$module_list = ['Dashboard Setup','Report Setup','Dashboards','Reports'];
		$html = '';
		foreach ($module_list as $module) {
			$html .= '<DIV id="'.str_replace(' ','',$module).'ModuleIcon" class="DashboardIcon" onClick="selectModule(this);">'.$module."</DIV>\r\n";
		}
		$this->currentScreen = 17;
		return $html;
	}
	private function accountingSubmenu() {
		// TODO: Only display those modules the user has permissions to.
		$module_list = ['Chart of Accounts','GL Periods','GL Accounts','GL Balances','GL Journal'];
		$html = '';
		foreach ($module_list as $module) {
			$html .= '<DIV id="'.str_replace(' ','',$module).'ModuleIcon" class="DashboardIcon" onClick="selectModule(this);">'.$module."</DIV>\r\n";
		}
		$this->currentScreen = 17;
		return $html;
	}
} // class Workspace
?>