<?php
class Purchasing extends ERPBase {
	private $purchase_order_number;
	private $vendor_id;
	private $order_date;
	private $purchase_order_reference;
	private $entity_id;
	private $division_id;
	private $department_id;
	private $terms_id;
	private $rev_enabled;
	private $rev_number;
	
	private $pur_detail_id;
	private $po_line;
	private $parent_line;
	private $item_id;
	private $quantity;
	private $quantity_uom;
	private $price;
	private $gl_account_id;
	private $detail_rev_enabled;
	private $detail_rev_number;
	private $detail_array;
	public function __construct($link=null) {
		parent::__construct($link);
		$this->searchFields[] = array('ent_entities',array('entity_id','entity_name'),'Entity','dropdown');
		$this->searchFields[] = array('pur_vendors',array('vendor_id','vendor_name'),'Vendor','dropdown');
		$this->searchFields[] = array('pur_header','purchase_order_number','Order #','integer');
		$this->searchFields[] = array('pur_header','order_date','Order Date','datetime');
		$this->searchFields[] = array('item_master',array('product_id','product_code'),'Product ID','dropdown');
		$this->searchFields[] = array('pur_vendor_catalog',array('vendor_catalog_id','vendor_item_number'),'Vendor SKU','dropdown');
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
	public function resetHeader() {
		$this->purchase_order_number = 0;
		$this->vendor_id = 0;
		$this->order_date = null;
		$this->purchase_order_reference = '';
		$this->entity_id = 0;
		$this->division_id = 0;
		$this->department_id = 0;
		$this->terms_id = 0;
		$this->rev_enabled = false;
		$this->rev_number = 1;
		$this->detail_array = array();
	}
	public function resetDetail() {
		$this->pur_detail_id = 0;
		$this->po_line = 0;
		$this->parent_line = 0;
		$this->item_id = 0;
		$this->quantity = 0;
		$this->quantity_uom = '';
		$this->price = 0.00;
		$this->gl_account_id = 0;
		$this->detail_rev_enabled = false;
		$this->detail_rev_number = 1;
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
		$_SESSION['currentScreen'] = 3007;
	} // function newRecord()
	private function insertHeader() {
		$this->resetHeader();
		$this->resetDetail();
		$vendorid = isset($_POST['vendorid'])?$_POST['vendorid']:0;
		$orderdate_date = isset($_POST['orderdate_date'])?$_POST['orderdate_date']:'';
		$orderdate_time = isset($_POST['orderdate_time'])?$_POST['orderdate_time']:'';
		$orderreference = isset($_POST['orderreference'])?$_POST['orderreference']:'';
		$entityid = isset($_POST['entityid'])?$_POST['entityid']:0;
		$divisionid = isset($_POST['divisionid'])?$_POST['divisionid']:0;
		$departmentid = isset($_POST['departmentid'])?$_POST['departmentid']:0;
		$termsid = isset($_POST['termsid'])?$_POST['termsid']:0;
		$rev_enabled = isset($_POST['rev_enabled'])?$_POST['rev_enabled']:false;
		$rev_number = isset($_POST['rev_number'])?$_POST['rev_number']:1;
		$return_date = false;
		if (strlen(trim($orderdate_date))==0) $return_date = true;
		$orderdate = new DateTime($orderdate_date.' '.$orderdate_time);
		$q = "INSERT INTO pur_header (vendor_id,order_date,purchase_order_reference,entity_id,division_id,department_id,terms,rev_enabled,rev_number,created_by,".
			"creation_date,last_update_by,last_update_date) VALUES (?,?,?,?,?,?,?,?,?,?,NOW(),?,NOW());";
		$stmt = $this->dbconn->prepare($q);
		$stmt->bind_param('issiiiisiii',$p1,$p2,$p3,$p4,$p5,$p6,$p7,$p8,$p9,$p10,$p12);
		if ($vendorid < 1) {
			$this->mb->addError("Vendor ID was not selected.");
			$stmt->close();
			return;
		}
		$p1 = $vendorid;
		if (is_null($orderdate)) {
			$this->mb->addError("The order date is not formatted correctly.");
			$stmt->close();
			return;
		}
		$p2 = $orderdate->format("Y-m-d H:i:s");
		$p3 = $orderreference;
		if ($entityid==0) {
			$this->mb->addError("The entity cannot be blank for purchase orders.");
			$stmt->close();
			return;
		}
		$p4 = $entityid;
		$p5 = ($divisionid==0)?null:$divisionid;
		$p6 = ($departmentid==0)?null:$departmentid;
		$p7 = ($termsid==0)?null:$termsid;
		$p8 = ($rev_enabled=='true')?'Y':'N';
		if ($rev_number < 1) $rev_number = 1;
		$p9 = $rev_number;
		$p10 = $_SESSION['dbuserid'];
		$p12 = $_SESSION['dbuserid'];
		$result = $stmt->execute();
		if ($result!==false) {
			echo 'inserted|'.$this->dbconn->insert_id.($return_date?'|'.$p2:'');
		} else {
			echo 'fail|'.$this->dbconn->error;
			$this->mb->addError($this->dbconn->error);
		}
		$stmt->close();
	}
	private function insertDetail() {
		$this->resetDetail();
		$orderkey = isset($_POST['orderkey'])?$_POST['orderkey']:0;
		$orderlinenum = isset($_POST['orderlinenum'])?$_POST['orderlinenum']:0;
		$parentlinenum = isset($_POST['parentlinenum'])?$_POST['parentlinenum']:0;
		$itemid = isset($_POST['itemid'])?$_POST['itemid']:'';
		$quantity = isset($_POST['quantity'])?$_POST['quantity']:0;
		$quantity_uom = isset($_POST['quantity_uom'])?$_POST['quantity_uom']:'EA';
		$price = isset($_POST['price'])?$_POST['price']:0.00;
		$gl_account_id = isset($_POST['gl_account_id'])?$_POST['gl_account_id']:null;
		$rev_enabled = isset($_POST['rev_enabled'])?$_POST['rev_enabled']:false;
		$rev_number = isset($_POST['rev_number'])?$_POST['rev_number']:1;
		$entityid = isset($_POST['entityid'])?$_POST['entityid']:0;
		$divisionid = isset($_POST['divisionid'])?$_POST['divisionid']:0;
		$departmentid = isset($_POST['departmentid'])?$_POST['departmentid']:0;
		
		/* The entity, division, and department are for future use, where one entity may be purchasing materials for another. */
		$q = "INSERT INTO pur_detail (purchase_order_number,po_line,parent_line,entity_id,division_id,department_id, item_id,quantity,quantity_uom,price,gl_account_id,
			rev_enabled,rev_number,created_by,creation_date,last_update_by,last_update_date) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,NOW(),?,NOW());";
		$stmt = $this->dbconn->prepare($q);
		$stmt->bind_param('iiiiiisdsdisiii',$p1,$p2,$p3,$p4,$p5,$p6,$p7,$p8,$p9,$p10,$p11,$p12,$p13,$p14,$p16);
		if ($orderkey==0) {
			$this->mb->addError("Details cannot be inserted when the purchase order number is zero.");
			$stmt->close();
			return;
		}
		$p1 = $orderkey;
		/* Question: Should orderlinenum automatically be updated to the next sequence if it's 0 or a number which already exists? */
		$p2 = $orderlinenum;
		/* Question: Should parentline be set to null if it references a line which no longer exists? */
		$p3 = $parentlinenum;
		if ($entityid==0) {
			$this->mb->addError("The entity cannot be blank for purchase orders.");
			$stmt->close();
			return;
		}
		$p4 = $entityid;
		$p5 = ($divisionid==0)?null:$divisionid;
		$p6 = ($departmentid==0)?null:$departmentid;
		$p7 = $itemid;
		/* Note: Orders with a quantity of 0 may be used as placeholders from a quote, where the remaining balance may be ordered at a future time. */
		$p8 = $quantity;
		$p9 = $quantity_uom;
		$p10 = $price;
		$p11 = $gl_account_id;
		$p12 = ($rev_enabled=='true')?'Y':'N';
		if ($rev_number<1) $rev_number = 1;
		$p13 = $rev_number;
		$p14 = $_SESSION['dbuserid'];
		$p16 = $_SESSION['dbuserid'];
		$result = $stmt->execute();
		if ($result!==false) {
			echo 'inserted|'.$this->dbconn->insert_id;
		} else {
			echo 'fail|'.$this->dbconn->error;
			$this->mb->addError($this->dbconn->error);
		}
		$stmt->close();
	}
	private function updateHeader() {
		$this->resetHeader();
		$this->resetDetail();
		
	}
	private function updateDetail() {
		$this->resetDetail();
		
	}
	public function insertRecord() {
		// Assumes values are stored in $_POST
		if (isset($_POST['level']) && $_POST['level']=='header') $this->insertHeader();
		if (isset($_POST['level']) && $_POST['level']=='detail') $this->insertDetail();
	}
	public function updateRecord() {
		// Assumes values are stored in $_POST
		if (isset($_POST['level']) && $_POST['level']=='header') $this->updateHeader();
		if (isset($_POST['level']) && $_POST['level']=='detail') $this->updateDetail();
	}
	public function saveRecord() {
		
	} // function saveRecord()
} // class Purchasing
?>