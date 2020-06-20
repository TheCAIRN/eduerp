<?php
class Consumers extends ERPBase {
	private $menucode = 28;
	private $customer_id;
	private $consumer_id;
	private $billing_address;
	private $shipping_address;
	private $last_update_date;
	private $column_list = "customer_id,consumer_id,billing_address,shipping_address,last_update_date";
	public function __construct ($link=null) {
		parent::__construct($link);
		$this->supportsNotes = false;
		$this->supportsAttachments = false;
		$this->searchFields[] = array('cust_consumers','unified_search','Type in any part of the address and click Search','textbox');
		// For embeddable classes, don't use fieldset.
		$this->entryFields[] = array('cust_consumers','customer_id','Customer','dropdown','cust_master',array('customer_id','customer_name'));
		$this->entryFields[] = array('cust_consumers','consumer_id','Consumer','embedded');
		$this->entryFields[] = array('cust_consumers','consumer_id','Consumer','Human');
		$this->entryFields[] = array('cust_consumers','','','endembedded');
		$this->entryFields[] = array('cust_consumers','billing_address','Billing Address','embedded');
		$this->entryFields[] = array('cust_consumers','billing_address','Billing Address','Address');
		$this->entryFields[] = array('cust_consumers','','','endembedded');
		$this->entryFields[] = array('cust_consumers','shipping_address','Shipping Address','embedded');
		$this->entryFields[] = array('cust_consumers','shipping_address','Shipping Address','Address');
		$this->entryFields[] = array('cust_consumers','','','endembedded');
		$this->resetHeader();
	} // __construct
	public function resetHeader() {
		$this->customer_id = null;
		$this->consumer_id = null;
		$this->billing_address = null;
		$this->shipping_address = null;
		$this->last_update_date = null;
	} // resetHeader()
	private function arrayifyHeader() {
		return array('customer_id'=>$this->customer_id,'consumer_id'=>$this->consumer_id,'billing_address'=>$this->billing_address,
			'shipping_address'=>$this->shipping_address,'last_update_date'=>$this->last_update_date);
	} // arrayifyHeader()
	public function listRecords() {
		parent::abstractListRecords('cust_consumers');
	} // listRecords()
	public function searchPage() {
		parent::abstractSearchPage('ConsumersSearch');
	} // searchPage()
	public function executeSearch($criteria) {
		if (!is_null($criteria) && is_array($criteria) && count($criteria)>0)
			if (is_array($criteria[0]) && count($criteria[0])>=2 && $criteria[0][0]=='unified_search') $criteria = $criteria[0][1];
			else $criteria='';
		else $criteria='';
		$q = "SELECT * FROM cust_consumers ";
		// TODO: Add $criteria
		// TODO: Convert to prepared statements
		$q .= " ORDER BY consumer_id";
		$result = $this->dbconn->query($q);
		if ($result!==false) {
			$this->recordSet = array();
			while ($row=$result->fetch_assoc()) {
				$this->recordSet[$row['cust_consumers_id']] = array();
			} // while rows
		} // if query succeeded
		$this->listRecords();
		$_SESSION['currentScreen'] = 1000+$this->menucode;
		$_SESSION['lastCriteria'] = $criteria;
		if (!isset($_SESSION['searchResults'])) $_SESSION['searchResults'] = array();
		$_SESSION['searchResults']['cust_consumers'] = array_keys($this->recordSet);		
	} // executeSearch()
	public function isIDValid($id) {
		// TODO: Validate that the ID is actually a record in the database
		if ($id<1) return false;
		if (is_integer($id)) return true;
		if (ctype_digit($id)) return true;
		return false;
	} // isIDValid()
	public function display($id,$mode='view') {
		if (!$this->isIDValid($id)) return;
		$readonly = true;
		$html = '';
		$q = "SELECT *
			FROM cust_consumers c 
			WHERE cust_consumers_id=?";
		$stmt = $this->dbconn->prepare($q);
		if ($stmt===false) {
			echo $this->dbconn->error;
			return;
		}
		$stmt->bind_param('i',$cust_consumersid);
		$cust_consumersid = $id;
		$result = $stmt->execute();
		// TODO: What if another user deletes the record while it's still in my search results?
		if ($result!==false) {
			$stmt->bind_result(
			
			);
			$stmt->fetch();
			$this->currentRecord = $id;
			$stmt->close();		
			if ($mode!='update') {
				$hdata = $this->arrayifyHeader();
				echo parent::abstractRecord($mode,'Consumer','',$hdata,null);
			}
		} // if result
		else echo 'There was a problem accessing the requested record.';

		$_SESSION['currentScreen'] = 2000+$this->menucode;
		if (!isset($_SESSION['searchResults']) && !isset($_SESSION['searchResults']['cust_consumers']))
			$_SESSION['idarray'] = array(0,0,$id,0,0);
		else {
			$idloc = array_search($id,$_SESSION['searchResults']['cust_consumers'],false);
			$f = $_SESSION['searchResults']['cust_consumers'][0];
			$l = $_SESSION['searchResults']['cust_consumers'][] = array_pop($_SESSION['searchResults']['cust_consumers']); // https://stackoverflow.com/questions/3687358/whats-the-best-way-to-get-the-last-element-of-an-array-without-deleting-it#comment63556865_3687358
			if ($idloc > 0) $p = $_SESSION['searchResults']['cust_consumers'][$idloc-1]; else $p = $f;
			if ($l != $id) $n = $_SESSION['searchResults']['cust_consumers'][$idloc+1]; else $n = $l;
			$_SESSION['idarray'] = array($f,$p,$id,$n,$l);
		}		
	} // display()
	public function newRecord() {
		echo parent::abstractNewRecord('cust_consumers');
		$_SESSION['currentScreen'] = 3000+$this->menucode;
	} // newRecord()
	public function editRecord($id=null) {
		$this->display($id,'edit');	
		$_SESSION['currentScreen'] = 4000+$this->menucode;
	} // editRecord()
	private function insertHeader() {
		if (!isset($_POST['customer_id']) || !isset($_POST['consumer_id'])) {
			echo 'fail|Customer and Consumer IDs are both required.';
			return;
		}
		$this->resetHeader();
		$q = "INSERT INTO cust_consumers ({$this->column_list}) VALUES 
			(?,?,?,?,NOW());";
		$stmt = $this->dbconn->prepare($q);
		$stmt->bind_param('iiii',$p1,$p2,$p3,$p4);
		$p1 = $_POST['customer_id'];
		$p2 = $_POST['consumer_id'];
		$p3 = isset($_POST['billing_address'])?$_POST['billing_address']:null;
		$p4 = isset($_POST['shipping_address'])?$_POST['shipping_address']:null;
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
} // class Consumer
?>
