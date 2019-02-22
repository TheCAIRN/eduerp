<?php
class Purchasing extends ERPBase {
	public function __construct($link=null) {
		parent::__construct($link);
		$this->searchFields[] = array('ent_entities',array('entity_id','entity_name'),'Entity','dropdown');
		$this->searchFields[] = array('pur_vendors',array('vendor_id','vendor_name'),'Vendor','dropdown');
		$this->searchFields[] = array('pur_header','purchase_order_number','Order #','integer');
		$this->searchFields[] = array('pur_header','order_date','Order Date','datetime');
		$this->searchFields[] = array('item_master',array('product_id','product_code'),'Product ID','dropdown');
		$this->searchFields[] = array('pur_vendor_catalog',array('vendor_catalog_id'.'vendor_item_number'),'Vendor SKU','dropdown');
		$this->searchFields[] = array('pur_detail','pur_detail_id','Order detail #','integer');
		
		$this->entryFields[] = array('pur_header','','Purchase Order','fieldset');
		$this->entryFields[] = array('pur_header','purchase_order_number','Order #','integerid');
		$this->entryFields[] = array('pur_header','vendor_id','Vendor','dropdown','pur_vendors',array('vendor_id','vendor_name'));
		$this->entryFields[] = array('pur_header','order_date','Order Date','datetime');
		$this->entryFields[] = array('pur_header','purchase_order_reference','Reference','textbox');
		$this->entryFields[] = array('pur_header','entity_id','Entity','dropdown','ent_entities',array('entity_id','entity_name'));
		$this->entryFields[] = array('pur_header','division_id','Division','dropdown','ent_division_master',array('division_id','division_name'));
		$this->entryFields[] = array('pur_header','department_id','Department','dropdown','ent_department_master',array('department_id','department_name'));
		$this->entryFields[] = array('pur_header','terms','Terms','dropdown','aa_terms',array('terms_id','terms_code'));
		$this->entryFields[] = array('pur_header','rev_enabled','Enable Revision Tracking','checkbox','rev_number');
		$this->entryFields[] = array('pur_header','rev_number','Revision number','integer');
		$this->entryFields[] = array('pur_header','','','endfieldset');
		$this->entryFields[] = array('pur_detail','','Purchase Order Detail','fieldtable');
		$this->entryFields[] = array('pur_detail','pur_detail_id','Order Detail #','integerid');
		$this->entryFields[] = array('pur_detail','po_line','Order Line #','integer');
		$this->entryFields[] = array('pur_detail','parent_line','Parent Line #','integer');
		$this->entryFields[] = array('pur_detail','item_id','Item','dropdown','item_master',array('product_id','product_code'));
		$this->entryFields[] = array('pur_detail','quantity','Quantity','integer');
		$this->entryFields[] = array('pur_detail','quantity_uom','Quantity UOM','dropdown','aa_uom',array('uom_code','uom_description'));
		$this->entryFields[] = array('pur_detail','price','Price','decimal',17,5);
		$this->entryFields[] = array('pur_detail','gl_account_id','G/L Account','dropdown','acgl_accounts',array('gl_account_id','gl_account_name'));
		$this->entryFields[] = array('pur_detail','rev_enabled','Enable Revision Tracking','checkbox','rev_number');
		$this->entryFields[] = array('pur_detail','rev_number','Revision number','integer');
		$this->entryFields[] = array('pur_detail','','','endfieldtable');
	}
	public function listRecords() {
		parent::abstractListRecords('Purchasing');
	} // function listRecords()
	public function searchPage() {
		parent::abstractSearchPage('PurchasingSearch');
	} // function searchPage()
	public function executeSearch($criteria) {
		
	} // function executeSearch()
	public function isIDValid($id) {
		// TODO: Validate that the ID is actually a record in the database
		if ($id<1) return false;
		if (is_integer($id)) return true;
		if (ctype_digit($id)) return true;
		return false;
	} // function isIDValid()
	public function display($id) {
		
	} // function display()
	public function newRecord() {
		echo parent::abstractNewRecord('Purchasing');
	} // function newRecord()
	public function saveRecord() {
		
	} // function saveRecord()
} // class Purchasing
?>