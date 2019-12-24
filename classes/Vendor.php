<?php
class Vendor extends ERPBase {
	private $vendor_id;
	private $vendor_name;
	private $url;
	private $primary_address;
	private $billing_address;
	private $payment_address;
	private $gl_account_id;
	private $default_terms;
	private $status;
	private $rev_enabled;
	private $rev_number;
	private $created_by;
	private $creation_date;
	private $last_update_by;
	private $last_update_date;
	private $column_list = 'vendor_id,vendor_name,url,primary_address,billing_address,payment_address,gl_account_id,default_terms,status,rev_enabled,rev_number';
	public function __construct($link=null) {
		parent::__construct($link);
		$this->searchFields[] = array('pur_vendors','vendor_id','ID','integer');
		$this->searchFields[] = array('pur_vendors','vendor_name','Name','textbox');
		$this->searchFields[] = array('ent_entities','status','Active Flag','dropdown',array(array('A','Active'),array('B','Bankrupt'),array('D','Defunct'),
			array('I','Temporarily Inactive'),array('S','Seasonally Inactive')));
		// TO DO: Search by address
		
		$this->entryFields[] = array('pur_vendors','','Vendor','fieldset');
		$this->entryFields[] = array('pur_vendors','vendor_id','ID','integerid');
		$this->entryFields[] = array('pur_vendors','vendor_name','Name','textbox');
		$this->entryFields[] = array('pur_vendors','url','URL','textbox');
		$this->entryFields[] = array('pur_vendors','gl_account_id','G/L Account','dropdown','acgl_accounts',array('gl_account_id','gl_account_name'));
 // TODO: Change gl_account_id from simple dropdown to GLAccount, and treat as an embedded field, so new G/L accounts can be created in place.
		$this->entryFields[] = array('pur_vendors','default_terms','Default Terms','dropdown','aa_terms',array('terms_id','terms_code'));
		$this->entryFields[] = array('pur_vendors','status','Status','function',$this,'statusSelect');
		$this->entryFields[] = array('pur_vendors','rev_enabled','Enable Revision Tracking','checkbox','rev_number');
		$this->entryFields[] = array('pur_vendors','rev_number','Revision number','integer');
		$this->entryFields[] = array('pur_vendors','','','endfieldset');
		$this->entryFields[] = array('pur_vendors','primary_address','Primary Address','embedded');
		$this->entryFields[] = array('pur_vendors','primary_address','Primary Address','Address');
		$this->entryFields[] = array('pur_vendors','','','endembedded');
		$this->entryFields[] = array('pur_vendors','billing_address','Billing Address','embedded');
		$this->entryFields[] = array('pur_vendors','billing_address','Billing Address','Address');
		$this->entryFields[] = array('pur_vendors','','','endembedded');
		$this->entryFields[] = array('pur_vendors','payment_address','Payment Address','embedded');
		$this->entryFields[] = array('pur_vendors','payment_address','Payment Address','Address');
		$this->entryFields[] = array('pur_vendors','','','endembedded');
		
		$this->resetHeader();		
	} // function __construct
	public function resetHeader() {
		$this->vendor_id = -1;
		$this->vendor_name = '';
		$this->url = '';
		$this->primary_address = null;
		$this->billing_address = null;
		$this->payment_address = null;
		$this->gl_account_id = null;
		$this->default_terms = null;
		$this->status = 'A';
		$this->rev_enabled = false;
		$this->rev_number = 1;
		$this->created_by = null;
		$this->creation_date = null;
		$this->last_update_by = null;
		$this->last_update_date = null;
	}
	public function vendorSelect($id=0,$readonly=false) {
		/*
		$html = '<LABEL for="vendorSelect">Vendor:</LABEL><SELECT id="vendorSelect">';
		$q = 'SELECT vendor_id,vendor_name FROM pur_vendors ORDER BY vendor_name;';
		$result = $this->dbconn->query($q);
		if ($result!==false) while ($row = $result->fetch_assoc()) {
			if ($row['vendor_id']==$id || !$readonly) 
				$html .= '<OPTION value="'.$row['vendor_id'].'"'.($id==$row['vendor_id']?' selected="selected">':'>').$row['vendor_name'].'</OPTION>';
		} else {
			$html .= '<OPTION>'.$this->dbconn->error.'</OPTION>';
		}
		$html .= '</SELECT>';
		return $html;
		*/
		return parent::abstractSelect($id,$readonly,'pur_vendors','vendor_id','vendor_name','vendor');
	} // function vendorSelect
	public function statusSelect($status='',$readonly=false,$include_label=false) {
		$html = '';
		if ($include_label) $html .= '<LABEL for="vendorStatus">Status:</LABEL>';
		$html .= '<SELECT id="vendorStatus">';
		if ($status=='A' || !$readonly) $html .= '<OPTION value="A"'.($status=='A'?' selected="selected">':'>').'Active</OPTION>';
		if ($status=='B' || !$readonly) $html .= '<OPTION value="B"'.($status=='B'?' selected="selected">':'>').'Bankrupt</OPTION>';
		if ($status=='D' || !$readonly) $html .= '<OPTION value="D"'.($status=='D'?' selected="selected">':'>').'Defunct</OPTION>';
		if ($status=='I' || !$readonly) $html .= '<OPTION value="I"'.($status=='I'?' selected="selected">':'>').'Temporarily Inactive</OPTION>';
		if ($status=='S' || !$readonly) $html .= '<OPTION value="S"'.($status=='S'?' selected="selected">':'>').'Seasonally Inactive</OPTION>';
		$html .= '</SELECT>';
		return $html;
	} // function statusSelect
	public function listRecords() {
		parent::abstractListRecords('Vendor');
	} // function listRecords()
	public function searchPage() {
		parent::abstractSearchPage('VendorSearch');
	} // function searchPage()
	public function executeSearch($criteria) {
		$q = "SELECT vendor_id,vendor_name,gl_account_id,default_terms,status,city,spc_abbrev,country 
			FROM pur_vendors v
			LEFT OUTER JOIN cx_addresses a ON a.address_id=v.primary_address";
		// TODO: Add $criteria
		// TODO: Convert to prepared statements
		$q .= " ORDER BY vendor_id;";
		$result = $this->dbconn->query($q);
		if ($result!==false) {
			$this->recordSet = array();
			while ($row=$result->fetch_assoc()) {
				$this->recordSet[$row['vendor_id']] = array('name'=>$row['vendor_name'],'status'=>$row['status'],'city'=>$row['city'],
					'spc'=>$row['spc_abbrev'],'country'=>$row['country'],'gl_account'=>$row['gl_account_id'],'terms'=>$row['default_terms']);
			} // while rows
		} // if query succeeded
		$this->listRecords();
		$_SESSION['currentScreen'] = 1005;
		$_SESSION['lastCriteria'] = $criteria;
		if (!isset($_SESSION['searchResults'])) $_SESSION['searchResults'] = array();
		$_SESSION['searchResults']['Vendor'] = array_keys($this->recordSet);		
	} // function executeSearch
	public function isIDValid($id) {
		// TODO: Validate that the ID is actually a record in the database
		if ($id<1) return false;
		if (is_integer($id)) return true;
		if (ctype_digit($id)) return true;
		return false;
	}
	public function display($id,$mode='view') {
		if (!($this->isIDValid($id))) return;
		$readonly = true;
		$html = '';
		$q = "SELECT vendor_name,gl_account_id,default_terms,status,
			v.rev_enabled,v.rev_number,v.created_by,v.creation_date,v.last_update_by,v.last_update_date,
			a.building_number,a.street,a.attention,a.apartment,a.postal_box,a.line2,a.line3,a.city,a.spc_abbrev,a.postal_code,a.country,a.county,a.maidenhead,a.latitude,a.longitude,a.osm_id,a.last_validated, 
			b.building_number,b.street,b.attention,b.apartment,b.postal_box,b.line2,b.line3,b.city,b.spc_abbrev,b.postal_code,b.country,b.county,b.maidenhead,b.latitude,b.longitude,b.osm_id,b.last_validated,
			p.building_number,p.street,p.attention,p.apartment,p.postal_box,p.line2,p.line3,p.city,p.spc_abbrev,p.postal_code,p.country,p.county,p.maidenhead,p.latitude,p.longitude,p.osm_id,p.last_validated 
			FROM pur_vendors v
			LEFT OUTER JOIN cx_addresses a ON a.address_id=v.primary_address 
			LEFT OUTER JOIN cx_addresses b ON b.address_id=v.billing_address
			LEFT OUTER JOIN cx_addresses p ON p.address_id=v.payment_address
			WHERE vendor_id=?";
		$stmt = $this->dbconn->prepare($q);
		if ($stmt===false) {
			echo $this->dbconn->error;
			return;
		}
		$stmt->bind_param('i',$vendorid);
		$vendorid = $id;
		$result = $stmt->execute();
		if ($result!==false) {
			$stmt->bind_result($vname,$vglacct,$vterms,$vstatus,
				$vrevyn,$vrevnumber,$vuser_creation,$vdate_creation,$vuser_modify,$vdate_modify,
				$anumber,$astreet,$aattn,$aapt,$apobox,$aline2,$aline3,$acity,$aspc,$azip,$acountry,$acounty,$amaidenhead,$alatitude,$alongitude,$aosm,$alastvalidated,
				$bnumber,$bstreet,$battn,$bapt,$bpobox,$bline2,$bline3,$bcity,$bspc,$bzip,$bcountry,$bcounty,$bmaidenhead,$blatitude,$blongitude,$bosm,$blastvalidated,
				$pnumber,$pstreet,$pattn,$papt,$ppobox,$pline2,$pline3,$pcity,$pspc,$pzip,$pcountry,$pcounty,$pmaidenhead,$platitude,$plongitude,$posm,$plastvalidated);
			$stmt->fetch();
			if ($readonly) $cls = 'RecordView'; else $cls = 'RecordEdit';
			if ($readonly) $inputtextro = ' readonly="readonly"'; else $inputtextro = '';
			$html .= '<FIELDSET id="VendorRecord" class="'.$cls.'">';
			$html .= '<LABEL for="vendorid">Vendor ID:</LABEL><B id="vendorid">'.$id.'</B>';
			$html .= '<LABEL for="vendorname">Name:</LABEL><INPUT type="text" id="vendorname" value="'.$vname.'"'.$inputtextro.' />';
			$html .= '<LABEL for="vendorglacct">GL Account:</LABEL><INPUT type="text" id="vendorglacct" value="'.$vglacct.'"'.$inputtextro.' />';
			$html .= '<LABEL for="vendorterms">Terms:</LABEL><INPUT type="text" id="vendorterms" value="'.$vterms.'"'.$inputtextro.' />';
			$html .= $this->statusSelect($vstatus,$readonly,true);
			$html .= parent::displayRecordAudit($inputtextro,$vrevyn,$vrevnumber,$vuser_creation,$vdate_creation,$vuser_modify,$vdate_modify);
			/*
			$html .= '<DIV id="RecordAudit">';
			$html .= '<LABEL for="revenabled">Revision Enabled:</LABEL><INPUT type="checkbox" id="revenabled" '.$inputtextro.' '.($vrevyn=='Y'?'checked="checked" />':'/>');
			$html .= '<LABEL for="revnumber">Revision Number:</LABEL><INPUT type="number" id="revnumber" value="'.$vrevnumber.'"'.$inputtextro.' />';
			$html .= '<LABEL for="createdby">Created By:</LABEL><INPUT type="text" id="createdby" value="'.$vuser_creation.'" readonly="readonly" />';	// These 4 fields can only ever be modified by the system
			$html .= '<LABEL for="createdon">Created On:</LABEL><INPUT type="date" id="createdon" value="'.$vdate_creation.'" readonly="readonly" />';
			$html .= '<LABEL for="modifiedby">Modified By:</LABEL><INPUT type="text" id="modifiedby" value="'.$vuser_modify.'" readonly="readonly" />';
			$html .= '<LABEL for="modifiedon">Modified On:</LABEL><INPUT type="date" id="modifiedon" value="'.$vdate_modify.'" readonly="readonly" />';			
			$html .= '</DIV>';
			*/
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
			$html .= '<FIELDSET id="PaymentAddressRecord" class="'.$cls.'">';
			$html .= '<LEGEND onClick="$(this).siblings().toggle();">Payment Address</LEGEND>';
			$html .= '<LABEL for="addr_attn">Attn:</LABEL><INPUT type="text" id="addr_attn" value="'.$pattn.'"'.$inputtextro.' /><BR />';
			$html .= '<LABEL for="addr_pobox">PO Box:</LABEL><INPUT type="text" id="addr_pobox" value="'.$ppobox.'"'.$inputtextro.' /><BR />';
			$html .= '<INPUT id="addr_number" value="'.$pnumber.'"'.$inputtextro.' /><INPUT id="addr_street" value="'.$pstreet.'"'.$inputtextro.' /><BR />';
			$html .= '<LABEL for="addr_apt">Apartment/Suite:</LABEL><INPUT type="text" id="addr_apt" value="'.$papt.'"'.$inputtextro.' /><BR />';
			$html .= '<INPUT id="addr_line2" value="'.$pline2.'"'.$inputtextro.' /><BR />';
			$html .= '<INPUT id="addr_line3" value="'.$pline3.'"'.$inputtextro.' /><BR />';
			$html .= '<INPUT id="addr_city" value="'.$pcity.'"'.$inputtextro.' /><INPUT id="addr_spc" value="'.$pspc.'"'.$inputtextro.' /><INPUT id="addr_zip" value="'.$pzip.'"'.$inputtextro.
				' /><INPUT id="addr_country" value="'.$pcountry.'"'.$inputtextro.' /><BR />';
			$html .= '<LABEL for="addr_county">County:</LABEL><INPUT type="text" id="addr_county" value="'.$pcounty.'"'.$inputtextro.' /><BR />';
			$html .= '<LABEL for="addr_latitude">Lat/Long/Grid:</LABEL><INPUT id="addr_latitude" value="'.$platitude.'"'.$inputtextro.' /><INPUT id="addr_longitude" value="'.$plongitude.
				'"'.$inputtextro.' /><INPUT	id="addr_maidenhead" value="'.$pmaidenhead.'"'.$inputtextro.' /><BR />';
			$html .= '<LABEL for="addr_osm">Open Street Map ID:</LABEL><INPUT id="addr_osm" value="'.$posm.'"'.$inputtextro.' /><LABEL for="addr_lastval">Last validated:</LABEL>'.
				'<INPUT type="date" id="addr_lastval" value="'.$plastvalidated.'" /><BR />';
			$html .= '</FIELDSET>';
			$html .= '<SCRIPT>$("#PrimaryAddressRecord legend").siblings().hide(); $("#BillingAddressRecord legend").siblings().hide(); $("#PaymentAddressRecord legend").siblings().hide(); </SCRIPT>';
		}
		$stmt->close();			
		echo $html;
		$_SESSION['currentScreen'] = 2005;
		if (!isset($_SESSION['searchResults']) && !isset($_SESSION['searchResults']['Vendor']))
			$_SESSION['idarray'] = array(0,0,$id,0,0);
		else {
			$idloc = array_search($id,$_SESSION['searchResults']['Vendor'],false);
			$f = $_SESSION['searchResults']['Vendor'][0];
			$l = $_SESSION['searchResults']['Vendor'][] = array_pop($_SESSION['searchResults']['Vendor']); // https://stackoverflow.com/questions/3687358/whats-the-best-way-to-get-the-last-element-of-an-array-without-deleting-it#comment63556865_3687358
			if ($idloc > 0) $p = $_SESSION['searchResults']['Vendor'][$idloc-1]; else $p = $f;
			if ($l != $id) $n = $_SESSION['searchResults']['Vendor'][$idloc+1]; else $n = $l;
			$_SESSION['idarray'] = array($f,$p,$id,$n,$l);
		}
	} // function display
	public function newRecord() {
		echo parent::abstractNewRecord('Vendor');
		$_SESSION['currentScreen'] = 3005;
	} // newRecord()
	public function editRecord($id) {
		$this->display($id,'edit');
		$_SESSION['currentScreen'] = 4005;
	}
	private function insertHeader() {
		
	} // insertHeader()
	private function updateHeader() {
		
	} // updateHeader()
	public function insertRecord() {
		if (isset($_POST['level']) && $_POST['level']=='header') $this->insertHeader(false);
	}
	public function updateRecord() {
		if (isset($_POST['level']) && $_POST['level']=='header') $this->updateHeader();
	}
	public function saveRecord() {
	
	} // saveRecord()	
} // class Vendor
?>