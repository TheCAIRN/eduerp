<?php
class Customer extends ERPBase {
	private $customer_id;
	private $customer_code;
	private $customer_name;
	private $cust_type_code;
	private $parent;
	private $customer_group;
	private $supplier_code;
	private $gl_account_id;
	private $default_terms;
	private $status;
	private $rev_enabled;
	private $rev_number;
	private $created_by;
	private $creation_date;
	private $last_update_by;
	private $last_update_date;
	private $primary_address;
	private $billing_address;
	private $auto_gl;
	private $auto_gl_prefix;
	private $column_list = 'customer_id,customer_code,customer_name,cust_type_code,parent,customer_group,supplier_code,primary_address,billing_address,gl_account_id,default_terms,
		status,rev_enabled,rev_number';
	public function __construct ($link=null) {
		parent::__construct($link);
		$this->auto_gl = false;
		$this->auto_gl_prefix = 0;
		if (isset($_SESSION['Options']) && isset($_SESSION['Options']['AUTOCREATE_GENERAL_LEDGER']) && strtoupper($_SESSION['Options']['AUTOCREATE_GENERAL_LEDGER'])=='TRUE') {
			$this->auto_gl = true;
			$this->auto_gl_prefix = Options::GetOptionValue($link,'GL_CUSTOMER_PREFIX');
		} // auto_gl
		$this->supportsNotes = true;
		$this->supportsAttachments = true;
		$this->searchFields[] = array('cust_master','customer_id','ID','integer');
		$this->searchFields[] = array('cust_master','customer_code','Code','textbox');
		$this->searchFields[] = array('cust_master','customer_name','Name','textbox');
		$this->searchFields[] = array('cust_types',array('cust_type_code','description'),'Type','dropdown');
		$this->searchFields[] = array('cust_master','primary_address','Primary Address','Address');
		$this->searchFields[] = array('cust_master','billing_address','Billing Address','Address');
		
		$this->entryFields[] = array('cust_master','','Customer','fieldset');
		$this->entryFields[] = array('cust_master','customer_id','ID','integerid');
		$this->entryFields[] = array('cust_master','customer_code','Code','textbox');
		$this->entryFields[] = array('cust_master','customer_name','Name','textbox');
		$this->entryFields[] = array('cust_master','cust_type_code','Type','dropdown','cust_types',array('cust_type_code','description'));
		$this->entryFields[] = array('cust_master','parent','Parent','dropdown','cust_master',array('customer_id','customer_name'));
		$this->entryFields[] = array('cust_master','customer_group','Group','textbox');
		$this->entryFields[] = array('cust_master','supplier_code','Supplier #','textbox');
		$this->entryFields[] = array('cust_master','default_terms','Default Terms','dropdown','aa_terms',array('terms_id','terms_code'));
		$this->entryFields[] = array('cust_master','status','Status','function',$this,'statusSelect');
		$this->entryFields[] = array('cust_master','rev_enabled','Enable Revision Tracking','checkbox','rev_number');
		$this->entryFields[] = array('cust_master','rev_number','Revision number','integer');
		$this->entryFields[] = array('cust_master','','','endfieldset');
		//if (!$this->auto_gl) $this->entryFields[] = array('cust_master','gl_account_id','G/L Account','dropdown','acgl_accounts',array('gl_account_id','gl_account_name'));
 // TODO: Change gl_account_id from simple dropdown to GLAccount, and treat as an embedded field, so new G/L accounts can be created in place.
		if (!$this->auto_gl) {
			$this->entryFields[] = array('cust_master','gl_account_id','G/L Account','embedded');
			$this->entryFields[] = array('cust_master','gl_account_id','G/L Account','GLAccount');
			$this->entryFields[] = array('cust_master','','','endembedded');
		}
		$this->entryFields[] = array('cust_master','primary_address','Primary Address','embedded');
		$this->entryFields[] = array('cust_master','primary_address','Primary Address','Address');
		$this->entryFields[] = array('cust_master','','','endembedded');
		$this->entryFields[] = array('cust_master','billing_address','Billing Address','embedded');
		$this->entryFields[] = array('cust_master','billing_address','Billing Address','Address');
		$this->entryFields[] = array('cust_master','','','endembedded');
		
		$this->resetHeader();
	} // __construct
	public function resetHeader() {
		$this->customer_id= null;
		$this->customer_code= null;
		$this->customer_name= null;
		$this->cust_type_code= null;
		$this->parent= null;
		$this->customer_group= null;
		$this->supplier_code= null;
		$this->gl_account_id= null;
		$this->default_terms= null;
		$this->status= null;
		$this->rev_enabled= null;
		$this->rev_number= null;
		$this->created_by= null;
		$this->creation_date= null;
		$this->last_update_by= null;
		$this->last_update_date= null;
		$this->primary_address= null;
		$this->billing_address= null;
	} // resetHeader()
	private function arrayifyHeader() {
		return array('customer_id'=>$this->customer_id,'customer_code'=>$this->customer_code,'customer_name'=>$this->customer_name,'cust_type_code'=>$this->cust_type_code,
			'parent'=>$this->parent,'customer_group'=>$this->customer_group,'supplier_code'=>$this->supplier_code,'primary_address'=>$this->primary_address,'billing_address'=>$this->billing_address,
			'gl_account_id'=>$this->gl_account_id,'default_terms'=>$this->default_terms,'status'=>$this->status,'rev_enabled'=>$this->rev_enabled,'rev_number'=>$this->rev_number,
			'created_by'=>$this->created_by,'creation_date'=>$this->creation_date,'last_update_by'=>$this->last_update_by,'last_update_date'=>$this->last_update_date);
	} // arrayifyHeader()
	public function customerSelect($id=0,$readonly=false) {
		return parent::abstractSelect($id,$readonly,'cust_master','customer_id','customer_name','customer');
	} // customerSelect()
	public function statusSelect($status='',$readonly=false,$include_label=false) {
		$html = '';
		if ($include_label) $html .= '<LABEL for="customerStatus">Status:</LABEL>';
		$html .= '<SELECT id="customerStatus">';
		if ($status=='A' || !$readonly) $html .= '<OPTION value="A"'.($status=='A'?' selected="selected">':'>').'Active</OPTION>';
		if ($status=='B' || !$readonly) $html .= '<OPTION value="B"'.($status=='B'?' selected="selected">':'>').'Bankrupt</OPTION>';
		if ($status=='D' || !$readonly) $html .= '<OPTION value="D"'.($status=='D'?' selected="selected">':'>').'Defunct</OPTION>';
		if ($status=='I' || !$readonly) $html .= '<OPTION value="I"'.($status=='I'?' selected="selected">':'>').'Temporarily Inactive</OPTION>';
		if ($status=='S' || !$readonly) $html .= '<OPTION value="S"'.($status=='S'?' selected="selected">':'>').'Seasonally Inactive</OPTION>';
		$html .= '</SELECT>';
		return $html;
	} // statusSelect()
	public function listRecords() {
		parent::abstractListRecords('Customer');
	} // listRecords()
	public function searchPage() {
		parent::abstractSearchPage('CustomerSearch');
	} // searchPage()
	public function executeSearch($criteria) {
		$q = "SELECT customer_id,customer_code,customer_name,cust_type_code,parent,customer_group,supplier_code,status,city,spc_abbrev,country FROM cust_master c ".
			"LEFT OUTER JOIN cx_addresses a ON a.address_id=c.primary_address ";
		// TODO: Add $criteria
		// TODO: Convert to prepared statements
		$q .= " ORDER BY customer_id";
		$result = $this->dbconn->query($q);
		if ($result!==false) {
			$this->recordSet = array();
			while ($row=$result->fetch_assoc()) {
				$this->recordSet[$row['customer_id']] = array('code'=>$row['customer_code'],'name'=>$row['customer_name'],'type'=>$row['cust_type_code'],
					'parent'=>$row['parent'],'status'=>$row['status'],'city'=>$row['city'],
					'spc'=>$row['spc_abbrev'],'country'=>$row['country']);
			} // while rows
		} // if query succeeded
		$this->listRecords();
		$_SESSION['currentScreen'] = 1024;
		$_SESSION['lastCriteria'] = $criteria;
		if (!isset($_SESSION['searchResults'])) $_SESSION['searchResults'] = array();
		$_SESSION['searchResults']['Customer'] = array_keys($this->recordSet);		
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
		$q = "SELECT {$this->column_list},c.created_by,c.creation_date,c.last_update_by,c.last_update_date,
			FROM cust_master c 
			WHERE customer_id=?";
		$stmt = $this->dbconn->prepare($q);
		if ($stmt===false) {
			echo $this->dbconn->error;
			return;
		}
		$stmt->bind_param('i',$custid);
		$custid = $id;
		$result = $stmt->execute();
		// TODO: What if another user deletes the record while it's still in my search results?
		if ($result!==false) {
			$stmt->bind_result($this->customer_id,$this->customer_code,$this->customer_name,$this->cust_type_code,$this->parent,$this->customer_group,
				$this->supplier_code,$this->primary_address,$this->billing_address,$this->gl_account_id,$this->default_terms,$this->status,$this->rev_enabled,$this->rev_number
			);
			$stmt->fetch();
			$this->currentRecord = $id;
			$stmt->close();		
			if ($mode!='update') {
				$hdata = $this->arrayifyHeader();
				echo parent::abstractRecord($mode,'Customer','',$hdata,null);
			}
		} // if result
		else echo 'There was a problem accessing the requested record.';

		$_SESSION['currentScreen'] = 2024;
		if (!isset($_SESSION['searchResults']) && !isset($_SESSION['searchResults']['Customer']))
			$_SESSION['idarray'] = array(0,0,$id,0,0);
		else {
			$idloc = array_search($id,$_SESSION['searchResults']['Customer'],false);
			$f = $_SESSION['searchResults']['Customer'][0];
			$l = $_SESSION['searchResults']['Customer'][] = array_pop($_SESSION['searchResults']['Customer']); // https://stackoverflow.com/questions/3687358/whats-the-best-way-to-get-the-last-element-of-an-array-without-deleting-it#comment63556865_3687358
			if ($idloc > 0) $p = $_SESSION['searchResults']['Customer'][$idloc-1]; else $p = $f;
			if ($l != $id) $n = $_SESSION['searchResults']['Customer'][$idloc+1]; else $n = $l;
			$_SESSION['idarray'] = array($f,$p,$id,$n,$l);
		}		
	} // display()
	public function newRecord() {
		echo parent::abstractNewRecord('Customer');
		$_SESSION['currentScreen'] = 3024;
	} // newRecord()
	public function editRecord($id=null) {
		$this->display($id,'edit');
		$_SESSION['currentScreen'] = 4024;
	}
	private function insertHeader() {
		$this->resetHeader();
		$custid = isset($_POST['customer_id'])?$_POST['customer_id']:-1;
		$custcode = isset($_POST['customer_code'])?$_POST['customer_code']:'';
		$custname = isset($_POST['customer_name'])?$_POST['customer_name']:'';
		$custtype = isset($_POST['cust_type_code'])?$_POST['cust_type_code']:null;
		$parent = isset($_POST['parent'])?$_POST['parent']:null;
		$custgroup = isset($_POST['customer_group'])?$_POST['customer_group']:'';
		$supplier = isset($_POST['supplier_code'])?$_POST['supplier_code']:'';
		$terms = isset($_POST['default_terms'])?$_POST['default_terms']:null;
		$status = isset($_POST['status'])?$_POST['status']:'A';
		$rev_enabled = isset($_POST['rev_enabled'])?$_POST['rev_enabled']:false;
		$rev_number = isset($_POST['rev_number'])?$_POST['rev_number']:1;
		$primary_addr = isset($_POST['primary_address'])?$_POST['primary_address']:null;
		$billing_addr = isset($_POST['billing_address'])?$_POST['billing_address']:null;
		if ($this->auto_gl) {
			$gl = new GLAccounts($this->dbconn);
			$glacct = $gl->autoCustomer($this->auto_gl_prefix,$custname);
			if (!$glacct) $glacct = null; // TODO: Report the error
		} else {
			$glacct = isset($_POST['gl_account_id'])?$_POST['gl_account_id']:null;
		}
		$q = "INSERT INTO cust_master (customer_code,customer_name,cust_type_code,parent,customer_group,supplier_code,
			gl_account_id,default_terms,status,primary_address,billing_address,
			rev_enabled,rev_number,created_by,creation_date,last_update_by,last_update_date) VALUES 
			(?,?,?,?,?,?,?,?,?,?,?,?,?,?,NOW(),?,NOW());";
		$stmt = $this->dbconn->prepare($q);
		$stmt->bind_param('sssissiisiisiii',$p1,$p2,$p3,$p4,$p5,$p6,$p7,$p8,$p9,$p10,$p11,$p12,$p13,$p14,$p16);
		if ($custcode=='') {
			$this->mb->addError("A short text customer code is required when creating a new customer.");
			$stmt->close();
			return;
		}
		$p1 = $custcode;
		if ($custname=='') {
			$this->mb->addError("Please provide a customer name.");
			$stmt->close();
			return;			
		}
		$p2 = $custname;
		if (is_null($custtype)) {
			$this->mb->addError("Customer type was not selected.");
			$stmt->close();
			return;
		}
		$p3 = $custtype;
		if ($parent=='') $parent = null;
		$p4 = $parent;
		$p5 = $custgroup;
		$p6 = $supplier;
		$p7 = $glacct;
		$p8 = $terms;
		if ($status=='') $status='A';
		$p9 = $status;
		if (is_null($primary_addr)) {
			$this->mb->addError("Primary address was not selected.");
			$stmt->close();
			return;			
		}
		$p10 = $primary_addr;
		if (is_null($billing_addr)) {
			$this->mb->addError("Billing address was not selected.");
			$stmt->close();
			return;
		}
		$p11 = $billing_addr;
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
		$now = new DateTime();
		$data=$_POST['data'];
		$id = $data['customer_id'];
		if ((!is_integer($id) && !ctype_digit($id)) || $id<1) {
			echo 'fail|Invalid address id for updating';
			return;
		}
		$this->customer_id = $id;
		$this->display($id,'update'); // Display already has the logic for loading the record.  TODO: Refactor into separate function.
		$update = array();
		// TODO: compare fields
		if (isset($data['customer_code']) && $data['customer_code']!=$this->customer_code) $update['customer_code'] = array('s',$data['customer_code']);
		if (isset($data['customer_name']) && $data['customer_name']!=$this->customer_name) $update['customer_name'] = array('s',$data['customer_name']);
		if (isset($data['cust_type_code']) && $data['cust_type_code']!=$this->cust_type_code) $update['cust_type_code'] = array('s',$data['cust_type_code']);
		if (isset($data['parent']) && $data['parent']!=$this->parent) $update['parent'] = array('i',$data['parent']);
		if (isset($data['customer_group']) && $data['customer_group']!=$this->customer_group) $update['customer_group'] = array('s',$data['customer_group']);
		if (isset($data['supplier_code']) && $data['supplier_code']!=$this->supplier_code) $update['supplier_code'] = array('s',$data['supplier_code']);
		if (isset($data['gl_account_id']) && $data['gl_account_id']!=$this->gl_account_id) $update['gl_account_id'] = array('i',$data['gl_account_id']);
		if (isset($data['default_terms']) && $data['default_terms']!=$this->default_terms) $update['default_terms'] = array('i',$data['default_terms']);
		if (isset($data['status']) && $data['status']!=$this->status) $update['status'] = array('s',$data['status']);
		if (isset($data['primary_address']) && $data['primary_address']!=$this->primary_address) $update['primary_address'] = array('i',$data['primary_address']);
		if (isset($data['billing_address']) && $data['billing_address']!=$this->billing_address) $update['billing_address'] = array('i',$data['billing_address']);
		$rev_enabled = isset($data['rev_enabled'])?$data['rev_enabled']:false;
		$rev_enabled = ($rev_enabled=='true')?'Y':'N';
		$rev_number = isset($data['rev_number'])?$data['rev_number']:1;
		if (isset($data['rev_enabled']) && $rev_enabled!=$this->rev_enabled) $update['rev_enabled'] = array('s',$rev_enabled);
		if (isset($data['rev_number']) && $rev_number!=$this->rev_number) $update['rev_number'] = array('i',$rev_number);
	
		$update['last_update_by'] = array('i',$_SESSION['dbuserid']);
		$update['last_update_date'] = array('s',$now->format('Y-m-d H:i:s'));
		// Create UPDATE String
		
		if (count($update)==2) {
			echo 'fail|Nothing to update';
			return;
		}
		$q = 'UPDATE cust_master SET ';
		$ctr = 0;
		$bp_types = '';
		$bp_values = array_fill(0,count($update),null);
		foreach ($update as $field=>$data) {
			if ($ctr > 0) $q .= ',';
			$q .= "$field=?";
			$bp_types .= $data[0];
			$bp_values[$ctr] = $data[1];
			$ctr++;
		}
		$q .= ' WHERE customer_id=?';
		$ctr++;
		$bp_types .= 'i';
		$bp_values[$ctr] = $this->id;
		$stmt = $this->dbconn->prepare($q);
		if ($stmt===false) {
			echo 'fail|'.$this->dbconn->error;
			return;
		}
		/* The internet has a lot of material about different ways to pass a variable number of arguments to bind_param.
		   I feel that using Reflection is the best tool for the job.
		   Reference: https://www.php.net/manual/en/mysqli-stmt.bind-param.php#107154
		*/
		$bp_method = new ReflectionMethod($stmt,'bind_param');
		$bp_refs = array();
		foreach ($bp_values as $key=>$value) {
			$bp_refs[$key] = &$bp_values[$key];
		}
		array_unshift($bp_values,$bp_types);
		$bp_method->invokeArgs($stmt,$bp_values);
		$stmt->execute();
		if ($stmt->affected_rows > 0) {
			echo 'updated|'.$id;
		} else {
			if ($this->dbconn->error) {
				echo 'fail|'.$this->dbconn->error;
				$this->mb->addError($this->dbconn->error);
			} else echo 'fail|No rows updated';
		}
		$stmt->close();		
	} // updateHeader()
	public function insertRecord() {
		$this->insertHeader();
	} // insertRecord()
	public function updateRecord() {
		$this->updateHeader();
	} // updateRecord()
	public function saveRecord() {
	
	} // saveRecord()
} // class Customer
?>
