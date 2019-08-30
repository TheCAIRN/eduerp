<?php
class COA extends ERPBase {
	public function __construct ($link=null) {
		parent::__construct($link);
		$this->supportsNotes = false;
		$this->supportsAttachments = false;
		$this->searchFields[] = array('ac_coa','unified_search','Type in any part of the account number or title and click Search','textbox');
		$this->resetHeader();
	} // __construct
	public function resetHeader() {
	
	} // resetHeader()
	public function COASelect($id=0,$readonly=false) {
		return parent::abstractSelect($id,$readonly,'ac_coa','account_number','account_title','COA');
	} // COASelect()
	public function accountTypeSelect($actype='',$readonly=false,$include_label=false) {
		$html = '';
		if ($include_label) $html .= '<LABEL for="COAStatus">Status:</LABEL>';
		$html .= '<SELECT id="customerStatus">';
		if ($actype=='A' || !$readonly) $html .= '<OPTION value="A"'.($actype=='A'?' selected="selected">':'>').'Asset</OPTION>';
		if ($actype=='L' || !$readonly) $html .= '<OPTION value="L"'.($actype=='L'?' selected="selected">':'>').'Liability</OPTION>';
		if ($actype=='Q' || !$readonly) $html .= '<OPTION value="Q"'.($actype=='Q'?' selected="selected">':'>').'Equity</OPTION>';
		if ($actype=='R' || !$readonly) $html .= '<OPTION value="R"'.($actype=='R'?' selected="selected">':'>').'Revenue</OPTION>';
		if ($actype=='E' || !$readonly) $html .= '<OPTION value="E"'.($actype=='E'?' selected="selected">':'>').'Expense</OPTION>';
		$html .= '</SELECT>';
		return $html;
	} // statusSelect()
	public function listRecords() {
		parent::abstractListRecords('COA');
	} // listRecords()
	public function searchPage() {
		parent::abstractSearchPage('COASearch');
	} // searchPage()
	public function executeSearch($criteria) {
		$q = "SELECT account_number,account_type,account_title FROM ac_coa ";
		// TODO: Add $criteria
		// TODO: Convert to prepared statements
		$q .= " ORDER BY account_number";
		$result = $this->dbconn->query($q);
		if ($result!==false) {
			$this->recordSet = array();
			while ($row=$result->fetch_assoc()) {
				$actype = $row['account_type'];
				if ($actype=='A') $actype = 'Asset';
				if ($actype=='L') $actype = 'Liability';
				if ($actype=='Q') $actype = 'Equity';
				if ($actype=='R') $actype = 'Revenue';
				if ($actype=='E') $actype = 'Expense';
				$this->recordSet[$row['account_number']] = array('Type'=>$actype,'Title'=>$row['account_title']);
			} // while rows
		} // if query succeeded
		$this->listRecords();
		$_SESSION['currentScreen'] = 1035;
		$_SESSION['lastCriteria'] = $criteria;
		if (!isset($_SESSION['searchResults'])) $_SESSION['searchResults'] = array();
		$_SESSION['searchResults']['COA'] = array_keys($this->recordSet);		
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
			FROM ac_coa c 
			WHERE account_number=?";
		$stmt = $this->dbconn->prepare($q);
		if ($stmt===false) {
			echo $this->dbconn->error;
			return;
		}
		$stmt->bind_param('i',$COAid);
		$COAid = $id;
		$result = $stmt->execute();
		// TODO: What if another user deletes the record while it's still in my search results?
		if ($result!==false) {
			$stmt->bind_result(
			
			);
			$stmt->fetch();
			if ($readonly) $cls = 'RecordView'; else $cls = 'RecordEdit';
			if ($readonly) $inputtextro = ' readonly="readonly"'; else $inputtextro = '';
			$html .= '<FIELDSET id="COARecord" class="'.$cls.'">';
			$html .= '<LABEL for="AcctNumber">Acct #:</LABEL><B id="AcctNumber">'.$id.'</B>';
			$html .= $this->statusSelect($status,$readonly);
			$html .= parent::displayRecordAudit($inputtextro,$crevyn,$crevnumber,$cuser_creation,$cdate_creation,$cuser_modify,$cdate_modify);
			$html .= '</FIELDSET>';
		} // if result
		$stmt->close();			
		echo $html;
		$_SESSION['currentScreen'] = 2035;
		if (!isset($_SESSION['searchResults']) && !isset($_SESSION['searchResults']['COA']))
			$_SESSION['idarray'] = array(0,0,$id,0,0);
		else {
			$idloc = array_search($id,$_SESSION['searchResults']['COA'],false);
			$f = $_SESSION['searchResults']['COA'][0];
			$l = $_SESSION['searchResults']['COA'][] = array_pop($_SESSION['searchResults']['COA']); // https://stackoverflow.com/questions/3687358/whats-the-best-way-to-get-the-last-element-of-an-array-without-deleting-it#comment63556865_3687358
			if ($idloc > 0) $p = $_SESSION['searchResults']['COA'][$idloc-1]; else $p = $f;
			if ($l != $id) $n = $_SESSION['searchResults']['COA'][$idloc+1]; else $n = $l;
			$_SESSION['idarray'] = array($f,$p,$id,$n,$l);
		}		
	} // display()
	public function newRecord() {
		echo parent::abstractNewRecord('COA');
		$_SESSION['currentScreen'] = 3035;
	} // newRecord()
	private function insertHeader() {
		$this->resetHeader();
		$q = "INSERT INTO ac_coa (
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
} // class COA
?>