<?php
class Customer extends ERPBase {
	public function __construct ($link=null) {
		parent::__construct($link);
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
		$this->entryFields[] = array('cust_master','gl_account_id','G/L Account','dropdown','acgl_accounts',array('gl_account_id','gl_account_name'));
 // TODO: Change gl_account_id from simple dropdown to GLAccount, and treat as an embedded field, so new G/L accounts can be created in place.
		$this->entryFields[] = array('cust_master','default_terms','Default Terms','dropdown','aa_terms',array('terms_id','terms_code'));
		$this->entryFields[] = array('cust_master','status','Status','function',$this,'statusSelect');
		$this->entryFields[] = array('cust_master','rev_enabled','Enable Revision Tracking','checkbox','rev_number');
		$this->entryFields[] = array('cust_master','rev_number','Revision number','integer');
		$this->entryFields[] = array('cust_master','','','endfieldset');
		$this->entryFields[] = array('cust_master','primary_address','Primary Address','embedded');
		$this->entryFields[] = array('cust_master','primary_address','Primary Address','Address');
		$this->entryFields[] = array('cust_master','','','endembedded');
		$this->entryFields[] = array('cust_master','billing_address','Billing Address','embedded');
		$this->entryFields[] = array('cust_master','billing_address','Billing Address','Address');
		$this->entryFields[] = array('cust_master','','','endembedded');
		
		$this->resetHeader();
	} // __construct
	public function resetHeader() {
	
	} // resetHeader()
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
		$q = "SELECT customer_code,customer_name,cust_type_code,parent,customer_group,supplier_code,gl_account_id,default_terms,status, 
			c.rev_enabled,c.rev_number,c.created_by,c.creation_date,c.last_update_by,c.last_update_date,
			a.building_number,a.street,a.attention,a.apartment,a.postal_box,a.line2,a.line3,a.city,a.spc_abbrev,a.postal_code,a.country,a.county,a.maidenhead,a.latitude,a.longitude,a.osm_id,a.last_validated, 
			b.building_number,b.street,b.attention,b.apartment,b.postal_box,b.line2,b.line3,b.city,b.spc_abbrev,b.postal_code,b.country,b.county,b.maidenhead,b.latitude,b.longitude,b.osm_id,b.last_validated
			FROM cust_master c 
			LEFT OUTER JOIN cx_addresses a ON a.address_id=c.primary_address 
			LEFT OUTER JOIN cx_addresses b ON b.address_id=c.billing_address 
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
			$stmt->bind_result($ccode,$cname,$ctype,$parent,$cgroup,$supplier,$gl,$terms,$status,
				$crevyn,$crevnumber,$cuser_creation,$cdate_creation,$cuser_modify,$cdate_modify,
				$anumber,$astreet,$aattn,$aapt,$apobox,$aline2,$aline3,$acity,$aspc,$azip,$acountry,$acounty,$amaidenhead,$alatitude,$alongitude,$aosm,$alastvalidated,
				$bnumber,$bstreet,$battn,$bapt,$bpobox,$bline2,$bline3,$bcity,$bspc,$bzip,$bcountry,$bcounty,$bmaidenhead,$blatitude,$blongitude,$bosm,$blastvalidated
			);
			$stmt->fetch();
			if ($readonly) $cls = 'RecordView'; else $cls = 'RecordEdit';
			if ($readonly) $inputtextro = ' readonly="readonly"'; else $inputtextro = '';
			$html .= '<FIELDSET id="CustomerRecord" class="'.$cls.'">';
			$html .= '<LABEL for="customerid">Customer ID:</LABEL><B id="customerid">'.$id.'</B>';
			$html .= '<LABEL for="customercode">Code:</Label><INPUT type="text" id="customercode" value="'.$ccode.'"'.$inputtextro.' />';
			$html .= '<LABEL for="customername">Name:</LABEL><INPUT type="text" id="customername" value="'.$cname.'"'.$inputtextro.' />';
			$html .= '<LABEL for="customertype">Type:</LABEL><INPUT type="text" id="customertype" value="'.$ctype.'"'.$inputtextro.' />';
			$html .= '<LABEL for="customerparent">Parent:</LABEL><INPUT type="text" id="customerparent" value="'.$parent.'"'.$inputtextro.' />';
			$html .= '<LABEL for="customergroup">Group:</LABEL><INPUT type="text" id="customergroup" value="'.$cgroup.'"'.$inputtextro.' />';
			$html .= '<LABEL for="customersupplier">Supplier #:</LABEL><INPUT type="text" id="customersupplier" value="'.$supplier.'"'.$inputtextro.' />';
			$html .= '<LABEL for="customerglacct">GL Account:</LABEL><INPUT type="text" id="customerglacct" value="'.$gl.'"'.$inputtextro.' />';
			$html .= '<LABEL for="customerterms">Terms:</LABEL><INPUT type="text" id="customerterms" value="'.$terms.'"'.$inputtextro.' />';
			$html .= $this->statusSelect($status,$readonly);
			$html .= parent::displayRecordAudit($inputtextro,$crevyn,$crevnumber,$cuser_creation,$cdate_creation,$cuser_modify,$cdate_modify);
			$html .= '</FIELDSET>';
			$html .= '<FIELDSET id="PrimaryAddressRecord" class="'.$cls.'">';
			$html .= '<LEGEND onClick="$(this).siblings().toggle();">Primary Address</LEGEND>';
			$html .= '<LABEL for="addr_attn">Attn:</LABEL><INPUT type="text" id="addr_attn" value="'.$aattn.'"'.$inputtextro.' /><BR />';
			$html .= '<LABEL for="addr_pobox">PO Box:</LABEL><INPUT type="text" id="addr_pobox" value="'.$apobox.'"'.$inputtextro.' /><BR />';
			$html .= '<INPUT id="addr_number" value="'.$anumber.'"'.$inputtextro.' /><INPUT id="addr_street" value="'.$astreet.'"'.$inputtextro.' /><BR />';
			$html .= '<LABEL for="addr_apt">Apartment/Suite:</LABEL><INPUT type="text" id="addr_apt" value="'.$aapt.'"'.$inputtextro.' /><BR />';
			$html .= '<INPUT id="addr_line2" value="'.$aline2.'"'.$inputtextro.' /><BR />';
			$html .= '<INPUT id="addr_line3" value="'.$aline3.'"'.$inputtextro.' /><BR />';
			$html .= '<INPUT id="addr_city" value="'.$acity.'"'.$inputtextro.' /><INPUT id="addr_spc" value="'.$aspc.'"'.$inputtextro.' /><INPUT id="addr_zip" value="'.$azip.'"'.$inputtextro.
				' /><INPUT id="addr_country" value="'.$acountry.'"'.$inputtextro.' /><BR />';
			$html .= '<LABEL for="addr_county">County:</LABEL><INPUT type="text" id="addr_county" value="'.$acounty.'"'.$inputtextro.' /><BR />';
			$html .= '<LABEL for="addr_latitude">Lat/Long/Grid:</LABEL><INPUT id="addr_latitude" value="'.$alatitude.'"'.$inputtextro.' /><INPUT id="addr_longitude" value="'.$alongitude.
				'"'.$inputtextro.' /><INPUT	id="addr_maidenhead" value="'.$amaidenhead.'"'.$inputtextro.' /><BR />';
			$html .= '<LABEL for="addr_osm">Open Street Map ID:</LABEL><INPUT id="addr_osm" value="'.$aosm.'"'.$inputtextro.' /><LABEL for="addr_lastval">Last validated:</LABEL>'.
				'<INPUT type="date" id="addr_lastval" value="'.$alastvalidated.'" /><BR />';
			$html .= '</FIELDSET>';
			$html .= '<FIELDSET id="BillingAddressRecord" class="'.$cls.'">';
			$html .= '<LEGEND onClick="$(this).siblings().toggle();">Billing Address</LEGEND>';
			$html .= '<LABEL for="addr_attn">Attn:</LABEL><INPUT type="text" id="addr_attn" value="'.$battn.'"'.$inputtextro.' /><BR />';
			$html .= '<LABEL for="addr_pobox">PO Box:</LABEL><INPUT type="text" id="addr_pobox" value="'.$bpobox.'"'.$inputtextro.' /><BR />';
			$html .= '<INPUT id="addr_number" value="'.$bnumber.'"'.$inputtextro.' /><INPUT id="addr_street" value="'.$bstreet.'"'.$inputtextro.' /><BR />';
			$html .= '<LABEL for="addr_apt">Apartment/Suite:</LABEL><INPUT type="text" id="addr_apt" value="'.$bapt.'"'.$inputtextro.' /><BR />';
			$html .= '<INPUT id="addr_line2" value="'.$bline2.'"'.$inputtextro.' /><BR />';
			$html .= '<INPUT id="addr_line3" value="'.$bline3.'"'.$inputtextro.' /><BR />';
			$html .= '<INPUT id="addr_city" value="'.$bcity.'"'.$inputtextro.' /><INPUT id="addr_spc" value="'.$bspc.'"'.$inputtextro.' /><INPUT id="addr_zip" value="'.$bzip.'"'.$inputtextro.
				' /><INPUT id="addr_country" value="'.$bcountry.'"'.$inputtextro.' /><BR />';
			$html .= '<LABEL for="addr_county">County:</LABEL><INPUT type="text" id="addr_county" value="'.$bcounty.'"'.$inputtextro.' /><BR />';
			$html .= '<LABEL for="addr_latitude">Lat/Long/Grid:</LABEL><INPUT id="addr_latitude" value="'.$blatitude.'"'.$inputtextro.' /><INPUT id="addr_longitude" value="'.$blongitude.
				'"'.$inputtextro.' /><INPUT	id="addr_maidenhead" value="'.$bmaidenhead.'"'.$inputtextro.' /><BR />';
			$html .= '<LABEL for="addr_osm">Open Street Map ID:</LABEL><INPUT id="addr_osm" value="'.$bosm.'"'.$inputtextro.' /><LABEL for="addr_lastval">Last validated:</LABEL>'.
				'<INPUT type="date" id="addr_lastval" value="'.$blastvalidated.'" /><BR />';
			$html .= '</FIELDSET>';
			$html .= '<SCRIPT>$("#PrimaryAddressRecord legend").siblings().hide(); $("#BillingAddressRecord legend").siblings().hide(); </SCRIPT>';
		} // if result
		$stmt->close();			
		echo $html;
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
	private function insertHeader() {
		$this->resetHeader();
		$custid = isset($_POST['customer_id'])?$_POST['customer_id']:-1;
		$custcode = isset($_POST['customer_code'])?$_POST['customer_code']:'';
		$custname = isset($_POST['customer_name'])?$_POST['customer_name']:'';
		$custtype = isset($_POST['cust_type_code'])?$_POST['cust_type_code']:null;
		$parent = isset($_POST['parent'])?$_POST['parent']:null;
		$custgroup = isset($_POST['customer_group'])?$_POST['customer_group']:'';
		$supplier = isset($_POST['supplier_code'])?$_POST['supplier_code']:'';
		$glacct = isset($_POST['gl_account_id'])?$_POST['gl_account_id']:null;
		$terms = isset($_POST['default_terms'])?$_POST['default_terms']:null;
		$status = isset($_POST['status'])?$_POST['status']:'A';
		$rev_enabled = isset($_POST['rev_enabled'])?$_POST['rev_enabled']:false;
		$rev_number = isset($_POST['rev_number'])?$_POST['rev_number']:1;
		$primary_addr = isset($_POST['primary_address'])?$_POST['primary_address']:null;
		$billing_addr = isset($_POST['billing_address'])?$_POST['billing_address']:null;
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