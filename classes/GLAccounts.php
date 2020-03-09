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
		echo '<FIELDSET id="GLAccountRecord" class="RecordEdit">';
		echo parent::abstractNewRecord('GLAccounts');
		echo '</FIELDSET>';
		$_SESSION['currentScreen'] = 3037;
	} // newRecord()
	public function editRecord($id=null ){
		$this->display($id,'edit');
		$_SESSION['currentScreen'] = 4037;
	} // editRecord()
	private function insertHeader() {
		$q = "INSERT INTO acgl_accounts (account_number,entity_id,division_id,department_id,sub_account_number,gl_account_string,gl_account_name,
			gl_account_balance,currency_code,	rev_enabled,rev_number,created_by,creation_date,last_update_by,last_update_date) VALUES 
			(?,?,?,?,?,?,?,?,?,?,?,?,NOW(),?,NOW());";
		$stmt = $this->dbconn->prepare($q);
		$stmt->bind_param('iiiiissdssiii',$p1,$p2,$p3,$p4,$p5,$p6,$p7,$p8,$p9,$p10,$p11,$p12,$p14);
		$p1 = $this->account_number;
		$p2 = $this->entity_id;
		$p3 = $this->division_id;
		$p4 = $this->department_id;
		$p5 = $this->sub_account_number;
		$p6 = $this->gl_account_string;
		$p7 = $this->gl_account_name;
		$p8 = $this->gl_account_balance;
		$p9 = $this->currency_code;
		$p10 = ($this->rev_enabled=='true')?'Y':'N';
		if ($this->rev_number<1) $this->rev_number = 1;
		$p11 = $this->rev_number;
		$p12 = $_SESSION['dbuserid'];
		$p14 = $_SESSION['dbuserid'];
		$result = $stmt->execute();
		if ($result!==false) {
			return 'inserted|'.$this->dbconn->insert_id;
		} else {
			return 'fail|'.$this->dbconn->error;
			$this->mb->addError($this->dbconn->error);
		}
		$stmt->close();
	} // insertHeader()
	private function updateHeader() {
	
	} // updateHeader()
	public function insertRecord() {
		// The insert and update functions look a little different in this class, because we need to account for autocreation, which most other classes don't.
		$this->resetHeader();
		if (isset($_POST['account_number'])) $this->account_number = $_POST['account_number'];
		if (isset($_POST['entity_id'])) $this->entity_id = $_POST['entity_id'];
		if (isset($_POST['division_id'])) $this->division_id = $_POST['division_id'];
		if (isset($_POST['department_id'])) $this->department_id = $_POST['department_id'];
		if (isset($_POST['sub_account_number'])) $this->sub_account_number = $_POST['sub_account_number'];
		if (isset($_POST['gl_account_string'])) $this->gl_account_string = $_POST['gl_account_string'];
		else $this->gl_account_string = sprintf('%d.%d',$this->account_number,$this->sub_account_number); // TODO: Change format to Options setting.
		if (isset($_POST['gl_account_name'])) $this->gl_account_name = $_POST['gl_account_name'];
		if (isset($_POST['gl_account_balance'])) $this->gl_account_balance = $_POST['gl_account_balance'];
		if (isset($_POST['currency_code'])) $this->currency_code = $_POST['currency_code'];
		$this->rev_enabled = isset($_POST['h14'])?$_POST['h14']:false;
		$this->rev_number = isset($_POST['h15'])?$_POST['h15']:1;
		echo $this->insertHeader();
	} // insertRecord()
	public function updateRecord() {
		$this->resetHeader();
		$this->updateHeader();
	} // updateRecord()
	public function saveRecord() {
	
	} // saveRecord()
	public function autoCustomer($prefix,$name) {
		$this->resetHeader();
		$this->account_number = $prefix;
		$ent = $this->dbconn->query("select entity_id from ent_entities where entity_type='HQ' limit 1;");
		if ($ent!==false) {
			$row = $ent->fetch_row();
			$this->entity_id = $row[0];
			$ent->close();
		} else return 'No entity';
		//$this->division_id = $division; // default to null
		//$this->department_id = $department; // default to null
		$this->gl_account_name = $name;
		if (isset($_SESSION['Options']) && isset($_SESSION['Options']['DEFAULT_CURRENCY_CODE']))
			$this->currency_code = $_SESSION['Options']['DEFAULT_CURRENCY_CODE'];
		else return 'Invalid currency code';
		// Get next sub_account_number
		$q1 = 'SELECT MAX(sub_account_number) as max_sub FROM acgl_accounts WHERE account_number=?';
		$s1 = $this->dbconn->prepare($q1);
		if ($s1!==false) {
			$s1->bind_param('i',$an);
			$an = $prefix;
			$result = $s1->execute();
			if ($result!==false) {
				$s1->bind_result($max_sub);
				$s1->fetch();
				$this->sub_account_number = $max_sub + 1;
				$glfmt = explode('.',Options::GetOptionValue($this->dbconn,'GL_ACCOUNT_FORMAT'));
				if (count($glfmt)<2) $glfmt = array('#####','#####'); // TODO: Once parsing and validation are fixed, this line must be removed.
				$glstra = '%0'.strlen($glfmt[0]).'d';
				// TODO: Improve parsing and validation
				$glstrb = '%0'.strlen($glfmt[1]).'d';
				$this->gl_account_string = sprintf("$glstra.$glstrb",$this->account_number,$this->sub_account_number);
				$s1->close();
				$r2 = explode('|',$this->insertHeader(0));
				if ($r2[0]=='inserted') return $r2[1];
				else {echo $r2[1]; return $r2[1];}
			} else {
				$s1->close();
				return 'Result is false';
			}
			$s1->close();
		} else return 'Could not get max_sub';
	} // autoCustomer
} // class _template
?>
