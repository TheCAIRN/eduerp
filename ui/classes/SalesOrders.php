<?php
class SalesOrders extends ERPBase {
	public function __construct ($link=null) {
		parent::__construct($link);
		$this->supportsNotes = true;
		$this->supportsAttachments = true;
		$this->searchFields[] = array('sales_header','sales_order_number','Sales Order #','integer');
		$this->searchFields[] = array('sales_header','customer_purchase_order_number','PO #','textbox');
		$this->searchFields[] = array('sales_header','bill_of_lading','BOL #','textbox');
		$this->searchFields[] = array('sales_header','wave_number','Wave #','textbox');
		$this->searchFields[] = array('sales_header','invoice_number','Invoice #','integer');
		
		$this->entryFields[] = array('sales_header','','Sales Order','fieldset');
		$this->entryFields[] = array('sales_header','sales_order_number','Sales Order #','integerid');
		$this->entryFields[] = array('sales_header','parent','Parent Order #','integer');
		$this->entryFields[] = array('sales_header','sales_order_type','Order Type','dropdown','sales_order_types',array('sales_order_type','description'));
		$this->entryFields[] = array('sales_header','sales_order_status','Status','function',$this,'statusSelect');
		$this->entryFields[] = array('sales_header','customer_id','Customer','dropdown','cust_master',array('customer_id','customer_name'));
		$this->entryFields[] = array('sales_header','buyer','Buyer','dropdown','v_cust_contacts',array('human_id','contact_name'),'customer_id');
		$this->entryFields[] = array('sales_header','seller','Seller','dropdown','v_sec_users',array('user_id','human_name'));
		$this->entryFields[] = array('sales_header','entity_id','Entity','dropdown','ent_entities',array('entity_id','entity_name'));
		$this->entryFields[] = array('sales_header','division_id','Division','dropdown','ent_division_master',array('division_id','division_name'));
		$this->entryFields[] = array('sales_header','department_id','Department','dropdown','ent_department_master',array('department_id','department_name'));
		$this->entryFields[] = array('sales_header','inventory_entity','Inventory Entity','dropdown','ent_entities',array('entity_id','entity_name'),'INV');
		$this->entryFields[] = array('sales_header','currency_code','Currency','dropdown','aa_currency',array('code','code'));
		$this->entryFields[] = array('sales_header','visible','Visible','checkbox');
		$this->entryFields[] = array('sales_header','rev_enabled','Enable Revision Tracking','checkbox','rev_number');
		$this->entryFields[] = array('sales_header','rev_number','Revision number','integer');
		$this->entryFields[] = array('sales_header','','','endfieldset');
		$this->entryFields[] = array('sales_header','','Pre-sales','fieldset');
		$this->entryFields[] = array('sales_header','quote_number','Quote #','textbox');
		$this->entryFields[] = array('sales_header','quote_approved_by','Quote approved by','dropdown','v_sec_users',array('user_id','human_name'));
		$this->entryFields[] = array('sales_header','quote_given_date','Quote Given','date');
		$this->entryFields[] = array('sales_header','quote_expires_date','Quote Expires','date');
		$this->entryFields[] = array('sales_header','','','endfieldset');
		$this->entryFields[] = array('sales_header','','Order confirmation','fieldset');
		$this->entryFields[] = array('sales_header','customer_purchase_order_number','Customer PO #','textbox');
		$this->entryFields[] = array('sales_header','customer_department','Customer Dept','textbox');
		$this->entryFields[] = array('sales_header','customer_product_group','Product Group','textbox');
		$this->entryFields[] = array('sales_header','store_code','Store','dropdown','cust_stores',array('store_code','store_name'),'customer_id');
		$this->entryFields[] = array('sales_header','terms','Terms','dropdown','aa_terms',array('terms_id','terms_code'));
		$this->entryFields[] = array('sales_header','order_date','Order Date','date');
		$this->entryFields[] = array('sales_header','credit_release_date','Credit Released','date');
		$this->entryFields[] = array('sales_header','ship_window_start','Start Date','date');
		$this->entryFields[] = array('sales_header','ship_window_end','End Date','date');
		$this->entryFields[] = array('sales_header','must_route_by','Must route by','date');
		$this->entryFields[] = array('sales_header','must_arrive_by','Must arrive by','date');
		$this->entryFields[] = array('sales_header','order_cancelled_date','Order Cancelled','date');
		$this->entryFields[] = array('sales_header','','','endfieldset');
		$this->entryFields[] = array('sales_header','','Processing','fieldset');
		$this->entryFields[] = array('sales_header','wave_number','Wave #','integer');
		$this->entryFields[] = array('sales_header','wave_date','Wave date','date');
		$this->entryFields[] = array('sales_header','inventory_needed_by','Inventory needed by','datetime');
		$this->entryFields[] = array('sales_header','inventory_pulled_complete','Inventory Pulled','datetime');
		$this->entryFields[] = array('sales_header','inventory_packed_complete','Inventory Packed','datetime');
		$this->entryFields[] = array('sales_header','','','endfieldset');
		$this->entryFields[] = array('sales_header','','Shipping','fieldset');
		$this->entryFields[] = array('sales_header','fv_vendor_id','Shipper','dropdown','fv_freight_vendors',array('fv_vendor_id','fv_vendor_name'));
		$this->entryFields[] = array('sales_header','bill_of_lading','BOL','textbox');
		$this->entryFields[] = array('sales_header','rrc','Routing request #','textbox');
		$this->entryFields[] = array('sales_header','load_id','Load ID','textbox');
		$this->entryFields[] = array('sales_header','routing_requested','Routing Requested','datetime');
		$this->entryFields[] = array('sales_header','pickup_scheduled_for','Pickup Scheduled For','datetime');
		$this->entryFields[] = array('sales_header','inventory_loaded_complete','Inventory Loaded','datetime');
		$this->entryFields[] = array('sales_header','bol_date','BOL Date','date');
		$this->entryFields[] = array('sales_header','order_shipped_date','Order Shipped','date');
		$this->entryFields[] = array('sales_header','','','endfieldset');
		$this->entryFields[] = array('sales_header','','Invoicing','fieldset');
		$this->entryFields[] = array('sales_header','invoice_number','Invoice #','integer');
		$this->entryFields[] = array('sales_header','order_invoiced_date','Order Invoiced','date');
		$this->entryFields[] = array('sales_header','invoice_paid_complete','Invoice Paid','date');
		$this->entryFields[] = array('sales_header','','','endfieldset');
		$this->entryFields[] = array('sales_header','shipping_from','Ship From','embedded');
		$this->entryFields[] = array('sales_header','shipping_from','Ship From','Address');
		$this->entryFields[] = array('sales_header','','','endembedded');
		$this->entryFields[] = array('sales_header','shipping_to','Ship To','embedded');
		$this->entryFields[] = array('sales_header','shipping_to','Ship To','Address');
		$this->entryFields[] = array('sales_header','','','endembedded');
		$this->entryFields[] = array('sales_header','remit_to','Remit To','embedded');
		$this->entryFields[] = array('sales_header','remit_to','Remit To','Address');
		$this->entryFields[] = array('sales_header','','','endembedded');
		
		$this->entryFields[] = array('sales_detail','','Sales Order Detail','fieldtable');
		$this->entryFields[] = array('sales_detail','sales_order_line','Line #','integer');
		$this->entryFields[] = array('sales_detail','parent_line','Parent','integer');
		$this->entryFields[] = array('sales_detail','customer_line','Customer Line #','textbox');
		$this->entryFields[] = array('sales_detail','','Item','embedded');
		$this->entryFields[] = array('sales_detail','item_id','Item','Item');
		$this->entryFields[] = array('sales_detail','','','endembedded');
		$this->entryFields[] = array('sales_detail','quantity_requested','Quantity','decimal',9,5);
		$this->entryFields[] = array('sales_detail','price','Price','decimal',17,5);
		$this->entryFields[] = array('sales_detail','retail_high','Retail','decimal',17,5);
		$this->entryFields[] = array('sales_detail','rev_enabled','Enable Revision Tracking','checkbox','rev_number');
		$this->entryFields[] = array('sales_detail','rev_number','Revision number','integer');
		$this->entryFields[] = array('sales_detail','','','endfieldtable');
		$this->resetHeader();
	} // __construct
	public function resetHeader() {
	
	} // resetHeader()
	public function _templateSelect($id=0,$readonly=false) {
		return parent::abstractSelect($id,$readonly,'sales_header','sales_order_number','sales_order_number','SalesOrders');
	} // _templateSelect()
	public function statusSelect($status='',$readonly=false,$include_label=false) {
		$html = '';
		if ($include_label) $html .= '<LABEL for="SalesOrdersStatus">Status:</LABEL>';
		$html .= '<SELECT id="salesOrderStatus">';
		if ($status=='Q' || !$readonly) $html .= '<OPTION value="Q"'.($status=='Q'?' selected="selected">':'>').'Quote</OPTION>';
		if ($status=='O' || !$readonly) $html .= '<OPTION value="O"'.($status=='O'?' selected="selected">':'>').'Ordered</OPTION>';
		if ($status=='H' || !$readonly) $html .= '<OPTION value="H"'.($status=='H'?' selected="selected">':'>').'Held</OPTION>';
		if ($status=='P' || !$readonly) $html .= '<OPTION value="P"'.($status=='P'?' selected="selected">':'>').'Processing</OPTION>';
		if ($status=='B' || !$readonly) $html .= '<OPTION value="B"'.($status=='B'?' selected="selected">':'>').'Backordered</OPTION>';
		if ($status=='S' || !$readonly) $html .= '<OPTION value="S"'.($status=='S'?' selected="selected">':'>').'Shipped</OPTION>';
		if ($status=='I' || !$readonly) $html .= '<OPTION value="I"'.($status=='I'?' selected="selected">':'>').'Invoiced</OPTION>';
		if ($status=='C' || !$readonly) $html .= '<OPTION value="C"'.($status=='C'?' selected="selected">':'>').'Cancelled</OPTION>';
		$html .= '</SELECT>';
		return $html;
	} // statusSelect()
	public function listRecords() {
		parent::abstractListRecords('SalesOrders');
	} // listRecords()
	public function searchPage() {
		parent::abstractSearchPage('SalesOrdersSearch');
	} // searchPage()
	public function executeSearch($criteria) {
		$q = "SELECT * FROM sales_header ";
		// TODO: Add $criteria
		// TODO: Convert to prepared statements
		$q .= " ORDER BY sales_order_number";
		$result = $this->dbconn->query($q);
		if ($result!==false) {
			$this->recordSet = array();
			while ($row=$result->fetch_assoc()) {
				$this->recordSet[$row['sales_order_number']] = array();
			} // while rows
		} // if query succeeded
		$this->listRecords();
		$_SESSION['currentScreen'] = 1000;
		$_SESSION['lastCriteria'] = $criteria;
		if (!isset($_SESSION['searchResults'])) $_SESSION['searchResults'] = array();
		$_SESSION['searchResults']['SalesOrders'] = array_keys($this->recordSet);		
	} // executeSearch()
	public function isIDValid($id) {
		// TODO: Validate that the ID is actually a record in the database
		if ($id<1) return false;
		if (is_integer($id)) return true;
		if (ctype_digit($id)) return true;
		return false;
	} // isIDValid()
	public function display($id) {
		if (!$this->isIDValid($id)) return;
		$readonly = true;
		$html = '';
		$q = "SELECT *
			FROM SalesOrders_master c 
			WHERE SalesOrders_id=?";
		$stmt = $this->dbconn->prepare($q);
		if ($stmt===false) {
			echo $this->dbconn->error;
			return;
		}
		$stmt->bind_param('i',$SalesOrdersid);
		$SalesOrdersid = $id;
		$result = $stmt->execute();
		// TODO: What if another user deletes the record while it's still in my search results?
		if ($result!==false) {
			$stmt->bind_result(
			
			);
			$stmt->fetch();
			if ($readonly) $cls = 'RecordView'; else $cls = 'RecordEdit';
			if ($readonly) $inputtextro = ' readonly="readonly"'; else $inputtextro = '';
			$html .= '<FIELDSET id="SalesOrdersRecord" class="'.$cls.'">';
			$html .= '<LABEL for="SalesOrdersid">SalesOrders ID:</LABEL><B id="SalesOrdersid">'.$id.'</B>';
			$html .= $this->statusSelect($status,$readonly);
			$html .= parent::displayRecordAudit($inputtextro,$crevyn,$crevnumber,$cuser_creation,$cdate_creation,$cuser_modify,$cdate_modify);
			$html .= '</FIELDSET>';
		} // if result
		$stmt->close();			
		echo $html;
		$_SESSION['currentScreen'] = 2000;
		if (!isset($_SESSION['searchResults']) && !isset($_SESSION['searchResults']['SalesOrders']))
			$_SESSION['idarray'] = array(0,0,$id,0,0);
		else {
			$idloc = array_search($id,$_SESSION['searchResults']['SalesOrders'],false);
			$f = $_SESSION['searchResults']['SalesOrders'][0];
			$l = $_SESSION['searchResults']['SalesOrders'][] = array_pop($_SESSION['searchResults']['SalesOrders']); // https://stackoverflow.com/questions/3687358/whats-the-best-way-to-get-the-last-element-of-an-array-without-deleting-it#comment63556865_3687358
			if ($idloc > 0) $p = $_SESSION['searchResults']['SalesOrders'][$idloc-1]; else $p = $f;
			if ($l != $id) $n = $_SESSION['searchResults']['SalesOrders'][$idloc+1]; else $n = $l;
			$_SESSION['idarray'] = array($f,$p,$id,$n,$l);
		}		
	} // display()
	public function newRecord() {
		echo parent::abstractNewRecord('SalesOrders');
		$_SESSION['currentScreen'] = 3000;
	} // newRecord()
	private function insertHeader() {
		$this->resetHeader();
		$q = "INSERT INTO SalesOrders_master (
			rev_enabled,rev_number,created_by,creation_date,last_update_by,last_update_date) VALUES 
			(?,?,?,?,?,?,?,?,?,?,?,?,?,?,NOW(),?,NOW());";
		$stmt = $this->dbconn->prepare($q);
		$stmt->bind_param('sssissiisiisiii',$p1,$p2,$p3,$p4,$p5,$p6,$p7,$p8,$p9,$p10,$p11,$p12,$p13,$p14,$p16);

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
	} // insertHeader()
	private function updateHeader() {
	
	} // updateHeader()
	public function insertRecord() {
		$this->insertHeader();
	} // insertRecord()
	public function updateRecord() {
		$this->updateHeader();
	} // updateRecord()
	public function saveRecord() {
	
	} // saveRecord()
} // class _template
?>