<?php
class SalesOrders extends ERPBase {
	private $sales_order_number;
	private $parent;
	private $sales_order_status;
	private $customer_id;
	private $buyer;
	private $seller;
	private $entity_id;
	private $division_id;
	private $department_id;
	private $inventory_entity;
	private $currency_code;
	private $visible;
	private $rev_enabled;
	private $rev_number;
	private $quote_number;
	private $quote_approved_by;
	private $quote_given_date;
	private $quote_expires_date;
	private $customer_purchase_order_number;
	private $customer_department;
	private $customer_product_group;
	private $store_code;
	private $terms;
	private $order_date;
	private $credit_release_date;
	private $ship_window_start;
	private $ship_window_end;
	private $must_route_by;
	private $must_arrive_by;
	private $order_cancelled_date;
	private $wave_number;
	private $wave_date;
	private $inventory_needed_by;
	private $inventory_pulled_complete;
	private $inventory_packed_complete;
	private $fv_vendor_id;
	private $bill_of_lading;
	private $rrc;
	private $load_id;
	private $routing_requested;
	private $pickup_scheduled_for;
	private $inventory_loaded_complete;
	private $bol_date;
	private $order_shipped_date;
	private $invoice_number;
	private $order_invoiced_date;
	private $invoice_paid_complete;
	private $shipping_from;
	private $shipping_to;
	private $remit_to;
	private $column_list_header = 'sales_order_number,parent,sales_order_type,sales_order_status,customer_id,buyer,seller,entity_id,division_id,department_id,
		inventory_entity,currency_code,visible,rev_enabled,rev_number,quote_number,quote_approved_by,quote_given_date,quote_expires_date,
		customer_purchase_order_number,customer_department,customer_product_group,store_code,terms,order_date,credit_release_date,ship_window_start,ship_window_end,
		must_route_by,must_arrive_by,order_cancelled_date,wave_number,wave_date,inventory_needed_by,inventory_pulled_complete,inventory_packed_complete,
		fv_vendor_id,bill_of_lading,rrc,load_id,routing_requested,pickup_scheduled_for,inventory_loaded_complete,bol_date,order_shipped_date,
		invoice_number,order_invoiced_date,invoice_paid_complete,shipping_from,shipping_to,remit_to';
	
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
		$this->entryFields[] = array('sales_header','-quote','Pre-sales','fieldset');
		$this->entryFields[] = array('sales_header','quote_number','Quote #','textbox');
		$this->entryFields[] = array('sales_header','quote_approved_by','Quote approved by','dropdown','v_sec_users',array('user_id','human_name'));
		$this->entryFields[] = array('sales_header','quote_given_date','Quote Given','date');
		$this->entryFields[] = array('sales_header','quote_expires_date','Quote Expires','date');
		$this->entryFields[] = array('sales_header','','','endfieldset');
		$this->entryFields[] = array('sales_header','-ordered','Order confirmation','fieldset');
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
		$this->entryFields[] = array('sales_header','-processing','Processing','fieldset');
		$this->entryFields[] = array('sales_header','wave_number','Wave #','integer');
		$this->entryFields[] = array('sales_header','wave_date','Wave date','date');
		$this->entryFields[] = array('sales_header','inventory_needed_by','Inventory needed by','datetime');
		$this->entryFields[] = array('sales_header','inventory_pulled_complete','Inventory Pulled','datetime');
		$this->entryFields[] = array('sales_header','inventory_packed_complete','Inventory Packed','datetime');
		$this->entryFields[] = array('sales_header','','','endfieldset');
		$this->entryFields[] = array('sales_header','-shipping','Shipping','fieldset');
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
		$this->entryFields[] = array('sales_header','-invoicing','Invoicing','fieldset');
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
		$_SESSION['currentScreen'] = 2044;
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
		echo '<SCRIPT>$("#sales_header-ordered_edit legend").siblings().hide(); 
			$("#sales_header-processing_edit legend").siblings().hide(); 
			$("#sales_header-shipping_edit legend").siblings().hide();
			$("#sales_header-invoicing_edit legend").siblings().hide();
			</SCRIPT>';
		$_SESSION['currentScreen'] = 3044;
	} // newRecord()
	private function insertHeader() {
		$this->resetHeader();
		$this->resetDetail();
		$ordernum = isset($_POST['h1'])?$_POST['h1']:0;
		$parent = isset($_POST['h2'])?$_POST['h2']:0;
		$ordertype = isset($_POST['h3'])?$_POST['h3']:0;
		$orderstatus = isset($_POST['h4'])?$_POST['h4']:'Q';
		$customerid = isset($_POST['h5'])?$_POST['h5']:0;
		$buyer = isset($_POST['h6'])?$_POST['h6']:0;
		$seller = isset($_POST['h7'])?$_POST['h7']:0;
		$entityid = isset($_POST['h8'])?$_POST['h8']:0;
		$divisionid = isset($_POST['h9'])?$_POST['h9']:0;
		$departmentid = isset($_POST['h10'])?$_POST['h10']:0;
		$invent = isset($_POST['h11'])?$_POST['h11']:0;
		$visible = isset($_POST['h12'])?$_POST['h12']:false;
		$termsid = isset($_POST['o5'])?$_POST['o5']:0;
		$rev_enabled = isset($_POST['h13'])?$_POST['h13']:false;
		$rev_number = isset($_POST['h14'])?$_POST['h14']:1;
		$return_date = false;
		if (strlen(trim($orderdate_date))==0) $return_date = true;
		$orderdate = new DateTime($orderdate_date.' '.$orderdate_time);
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
} // class _template
?>