<?php
class GLAccounts extends ERPBase {
	private $gl_account_id;
	private $account_number;
	private $entity_id;
	private $division_id;
	private $department_id;
	private $sub_account_number;
	private $gl_account_string;
	private $gl_account_name;
	private $gl_account_balance;
	private $currency_code;
	private $rev_enabled;
	private $rev_number;
	private $created_by;
	private $creation_date;
	private $last_update_by;
	private $last_update_date;
	private $column_list = 'gl_account_id,account_number,entity_id,division_id,department_id,sub_account_number,gl_account_string,gl_account_name,
		gl_account_balance,currency_code,rev_enabled,rev_number';
	
	public function __construct ($link=null) {
		parent::__construct($link);
		$this->supportsNotes = false;
		$this->supportsAttachments = false;
		$this->searchFields[] = array('acgl_accounts','unified_search','Type in any part of the account number or name and click Search','textbox');
		$this->entryFields[] = array('acgl_accounts','gl_account_id','ID','integerid');
		$this->entryFields[] = array('acgl_accounts','account_number','Acct #','integer');
		$this->entryFields[] = array('acgl_accounts','entity_id','Entity','dropdown','ent_entities',array('entity_id','entity_name'));
		$this->entryFields[] = array('acgl_accounts','division_id','Division','dropdown','ent_division_master',array('division_id','division_name'));
		$this->entryFields[] = array('acgl_accounts','department_id','Department','dropdown','ent_department_master',array('department_id','department_name'));
		$this->entryFields[] = array('acgl_accounts','sub_account_number','Sub Acct #','integer');
		$this->entryFields[] = array('acgl_accounts','gl_account_string','Account','textbox');
		$this->entryFields[] = array('acgl_accounts','gl_account_name','Name','textbox');
		$this->entryFields[] = array('acgl_accounts','gl_account_balance','Balance','decimal',24,5);
		$this->entryFields[] = array('acgl_accounts','currency_code','Currency','dropdown','aa_currency',array('code','code'),'USD');
		//$this->entryFields[] = array('acgl_accounts','visible','Visible','checkbox',null,true); // TODO: Add this to the database.
		$this->entryFields[] = array('acgl_accounts','rev_enabled','Enable Revision Tracking','checkbox','rev_number',false);
		$this->entryFields[] = array('acgl_accounts','rev_number','Revision number','integer');
		$this->resetHeader();
	} // __construct
	public function resetHeader() {
		$this->gl_account_id = -1;
		$this->account_number = 0;
		$this->entity_id = null;
		$this->division_id = null;
		$this->department_id = null;
		$this->sub_account_number = 0;
		$this->gl_account_string = '';
		$this->gl_account_name = '';
		$this->gl_account_balance = 0.00;
		$this->currency_code = '';
		$this->rev_enabled = 'N';
		$this->rev_number = 1;
		$this->created_by = null;
		$this->creation_date = null;
		$this->last_update_by = null;
		$this->last_update_date = null;
	} // resetHeader()
	private function arrayifyHeader() {
		return array('gl_account_id'=>$this->gl_account_id,'account_number'=>$this->account_number,'entity_id'=>$this->entity_id,'division_id'=>$this->division_id,
			'department_id'=>$this->department_id,'sub_account_number'=>$this->sub_account_number,'gl_account_string'=>$this->gl_account_string,
			'gl_account_name'=>$this->gl_account_name,'gl_account_balance'=>$this->gl_account_balance,'currency_code'=>$this->currency_code,'rev_enabled'=>$this->rev_enabled,
			'rev_number'=>$this->rev_number,'created_by'=>$this->created_by,'creation_date'=>$this->creation_date,'last_update_by'=>$this->last_update_by,
			'last_update_date'=>$this->last_update_date);
	} // arrayifyHeader()
	public function glAccountSelect($id=0,$readonly=false) {
		return parent::abstractSelect($id,$readonly,'acgl_accounts','gl_account_id','gl_account_name','GLAccounts');
	} // _templateSelect()
	public function statusSelect($status='',$readonly=false,$include_label=false) {
		$html = '';
		if ($include_label) $html .= '<LABEL for="GLAccountStatus">Status:</LABEL>';
		$html .= '<SELECT id="GLAccountStatus">';
		if ($status=='A' || !$readonly) $html .= '<OPTION value="A"'.($status=='A'?' selected="selected">':'>').'Active</OPTION>';
		if ($status=='B' || !$readonly) $html .= '<OPTION value="B"'.($status=='B'?' selected="selected">':'>').'Bankrupt</OPTION>';
		if ($status=='D' || !$readonly) $html .= '<OPTION value="D"'.($status=='D'?' selected="selected">':'>').'Defunct</OPTION>';
		if ($status=='I' || !$readonly) $html .= '<OPTION value="I"'.($status=='I'?' selected="selected">':'>').'Temporarily Inactive</OPTION>';
		if ($status=='S' || !$readonly) $html .= '<OPTION value="S"'.($status=='S'?' selected="selected">':'>').'Seasonally Inactive</OPTION>';
		$html .= '</SELECT>';
		return $html;
	} // statusSelect()
	public function listRecords() {
		parent::abstractListRecords('GLAccounts');
	} // listRecords()
	public function searchPage() {
		parent::abstractSearchPage('GLAccountsSearch');
	} // searchPage()
	public function executeSearch($criteria) {
		if (!is_null($criteria) && is_array($criteria) && count($criteria)>0)
			if (is_array($criteria[0]) && count($criteria[0])>=2 && $criteria[0][0]=='unified_search') $criteria = $criteria[0][1];
			else $criteria='';
		else $criteria='';
		$q = "SELECT gl_account_id,entity_name,gl_account_string,gl_account_name,gl_account_balance,acgl_accounts.currency_code FROM acgl_accounts 
			JOIN ent_entities ON ent_entities.entity_id=acgl_accounts.entity_id WHERE 
			gl_account_string LIKE ? OR gl_account_name LIKE ? OR gl_account_id=?";
		$q .= " ORDER BY gl_account_id";
		$this->recordSet = array();
		$stmt = $this->dbconn->prepare($q);
		if ($stmt!==false) {
			$stmt->bind_param('ssi',$p1,$p2,$p3);
			if ($criteria=='') $p1 = $p2 = '%';
			else $p1 = $p2 = "%$criteria%";
			if (is_integer($criteria) || ctype_digit($criteria)) $p3 = $criteria; else $p3 = -999;
			$result = $stmt->execute();
			if ($result!==false) {
				$stmt->bind_result($id,$ent,$str,$name,$bal,$cur);
				$stmt->store_result();
				while ($stmt->fetch()) {
					$this->recordSet[$id] = array('Entity'=>$ent,'Account'=>$str,'Name'=>$name,'Balance'=>$bal,'Currency'=>$cur);
				} // while rows
			} // if query succeeded
			$stmt->close();
		}
		$this->listRecords();
		$_SESSION['currentScreen'] = 1037;
		$_SESSION['lastCriteria'] = $criteria;
		if (!isset($_SESSION['searchResults'])) $_SESSION['searchResults'] = array();
		$_SESSION['searchResults']['GLAccounts'] = array_keys($this->recordSet);		
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
		$q = "SELECT {$this->column_list},created_by,creation_date,last_update_by,last_update_date 
			FROM acgl_accounts c 
			WHERE gl_account_id=?";
		$stmt = $this->dbconn->prepare($q);
		if ($stmt===false) {
			echo $this->dbconn->error;
			return;
		}
		$stmt->bind_param('i',$acctid);
		$acctid = $id;
		$result = $stmt->execute();
		// TODO: What if another user deletes the record while it's still in my search results?
		if ($result!==false) {
			$stmt->bind_result(
				$this->gl_account_id,$this->account_number,$this->entity_id,$this->division_id,$this->department_id,$this->sub_account_number,
				$this->gl_account_string,$this->gl_account_name,$this->gl_account_balance,$this->currency_code,$this->rev_enabled,$this->rev_number,
				$this->created_by,$this->creation_date,$this->last_update_by,$this->last_update_date
			);
			$stmt->fetch();
			$this->currentRecord = $id;
			$stmt->close();		
			if ($mode!='update') {
				$hdata = $this->arrayifyHeader();
				// The fieldset must be added here, because GLAccounts will eventually become an embedded class like Addresses or ItemManager.
				echo '<FIELDSET id="GLAccountRecord" class="Record'.ucwords($mode).'">';
				echo parent::abstractRecord($mode,'GLAccounts','',$hdata,null);
				echo '</FIELDSET>';
			}
		} // if result
		else echo 'There was a problem accessing the requested record.';

		$_SESSION['currentScreen'] = 2037;
		if (!isset($_SESSION['searchResults']) && !isset($_SESSION['searchResults']['GLAccounts']))
			$_SESSION['idarray'] = array(0,0,$id,0,0);
		else {
			$idloc = array_search($id,$_SESSION['searchResults']['GLAccounts'],false);
			$f = $_SESSION['searchResults']['GLAccounts'][0];
			$l = $_SESSION['searchResults']['GLAccounts'][] = array_pop($_SESSION['searchResults']['GLAccounts']); // https://stackoverflow.com/questions/3687358/whats-the-best-way-to-get-the-last-element-of-an-array-without-deleting-it#comment63556865_3687358
			if ($idloc > 0) $p = $_SESSION['searchResults']['GLAccounts'][$idloc-1]; else $p = $f;
			if ($l != $id) $n = $_SESSION['searchResults']['GLAccounts'][$idloc+1]; else $n = $l;
			$_SESSION['idarray'] = array($f,$p,$id,$n,$l);
		}		
	} // display()
	public function newRecord() {
		echo parent::abstractNewRecord('GLAccounts');
		$_SESSION['currentScreen'] = 3037;
	} // newRecord()
	private function insertHeader() {
		$this->resetHeader();
		$q = "INSERT INTO acgl_accounts (
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