<?php
class SystemOptions extends ERPBase {
	private $option_id;
	private $option_code;
	private $option_group;
	private $description;
	private $option_value;
	private $creation_date;
	private $last_update_by;
	private $last_update_date;
	private $column_list = 'option_id,option_code,option_group,description,option_value,creation_date,last_update_by,last_update_date';
	public function __construct ($link=null) {
		parent::__construct($link);
		$this->supportsNotes = false;
		$this->supportsAttachments = false;
		$this->searchFields[] = array('aa_options','unified_search','Type in any part of the option code or description and click Search','textbox');
		$this->entryFields[] = array('aa_options','option_id','ID','integerid');
		$this->entryFields[] = array('aa_options','option_code','Code','textbox');
		$this->entryFields[] = array('aa_options','option_group','Group','dropdown',array(array(null,'Not in a group'),array('SESSION','Load to session')));
		$this->entryFields[] = array('aa_options','description','Description','textarea');
		$this->entryFields[] = array('aa_options','option_value','Value','textbox');
		$this->entryFields[] = array('aa_options','creation_date','Creation date','datetime');
		$this->entryFields[] = array('aa_options','last_update_by','Last modified','dropdown','v_sec_users',array('user_id','human_name'));
		$this->entryFields[] = array('aa_options','last_update_date','Modified date','datetime');

		$this->resetHeader();
	} // __construct
	public function resetHeader() {
		$this->option_id = -1;
		$this->option_code = '';
		$this->option_group = null;
		$this->description = '';
		$this->option_value = null;
		$this->creation_date = null;
		$this->last_update_by = 1;
		$this->last_update_date = null;
	} // resetHeader()
	private function arrayify() {
		return array('option_id'=>$this->option_id,'option_code'=>$this->option_code,'option_group'=>$this->option_group,'description'=>$this->description,
			'option_value'=>$this->option_value,'creation_date'=>$this->creation_date,'last_update_by'=>$this->last_update_by,'last_update_date'=>$this->last_update_date);
	}
	public function SystemOptionsSelect($id=0,$readonly=false) {
		return parent::abstractSelect($id,$readonly,'aa_options','option_id','option_code','SystemOptions');
	} // _templateSelect()
	public function listRecords() {
		parent::abstractListRecords('SystemOptions');
	} // listRecords()
	public function searchPage() {
		parent::abstractSearchPage('SystemOptionsSearch');
	} // searchPage()
	public function executeSearch($criteria) {
		$q = "SELECT option_id,option_code,option_value FROM aa_options ";
		// TODO: Add $criteria
		// TODO: Convert to prepared statements
		$q .= " ORDER BY option_id";
		$result = $this->dbconn->query($q);
		if ($result!==false) {
			$this->recordSet = array();
			while ($row=$result->fetch_assoc()) {
				$this->recordSet[$row['option_id']] = array('Code'=>$row['option_code'],'Value'=>$row['option_value']);
			} // while rows
		} // if query succeeded
		$this->listRecords();
		$_SESSION['currentScreen'] = 1048;
		$_SESSION['lastCriteria'] = $criteria;
		if (!isset($_SESSION['searchResults'])) $_SESSION['searchResults'] = array();
		$_SESSION['searchResults']['SystemOptions'] = array_keys($this->recordSet);		
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
		$q = "SELECT {$this->column_list}
			FROM aa_options  
			WHERE option_id=?";
		$stmt = $this->dbconn->prepare($q);
		if ($stmt===false) {
			echo $this->dbconn->error;
			return;
		}
		$stmt->bind_param('i',$option_id);
		$option_id = $id;
		$result = $stmt->execute();
		// TODO: What if another user deletes the record while it's still in my search results?
		if ($result!==false) {
			$stmt->bind_result(
				$this->option_id
				,$this->option_code
				,$this->option_group
				,$this->description
				,$this->option_value
				,$this->creation_date
				,$this->last_update_by
				,$this->last_update_date
			);
			$stmt->fetch();
			$stmt->store_result();
		} // if result
		$stmt->close();
		if ($mode!='update') {
			$hdata = $this->arrayify();
			echo '<FIELDSET id="SystemOptionsRecord" class="Record'.ucwords($mode).'">';
			echo parent::abstractRecord($mode,'SystemOptions','',$hdata,null);
			echo '</FIELDSET>';
		}
		//echo $html;
		$_SESSION['currentScreen'] = 2048;
		if (!isset($_SESSION['searchResults']) && !isset($_SESSION['searchResults']['SystemOptions']))
			$_SESSION['idarray'] = array(0,0,$id,0,0);
		else {
			$idloc = array_search($id,$_SESSION['searchResults']['SystemOptions'],false);
			$f = $_SESSION['searchResults']['SystemOptions'][0];
			$l = $_SESSION['searchResults']['SystemOptions'][] = array_pop($_SESSION['searchResults']['SystemOptions']); // https://stackoverflow.com/questions/3687358/whats-the-best-way-to-get-the-last-element-of-an-array-without-deleting-it#comment63556865_3687358
			if ($idloc > 0) $p = $_SESSION['searchResults']['SystemOptions'][$idloc-1]; else $p = $f;
			if ($l != $id) $n = $_SESSION['searchResults']['SystemOptions'][$idloc+1]; else $n = $l;
			$_SESSION['idarray'] = array($f,$p,$id,$n,$l);
		}		
	} // display()
	public function newRecord() {
		echo 'fail|New options cannot be created outside of a system update.';
		//echo parent::abstractNewRecord('SystemOptions');
		//$_SESSION['currentScreen'] = 3048;
	} // newRecord()
	public function editRecord($id) {
		$this->display($id,'edit');
		$_SESSION['currentScreen'] = 4048;
	} // editRecord()
	private function insertHeader() {
		echo 'fail|New options cannot be created outside of a system update.';
/*		$this->resetHeader();
		$q = "INSERT INTO aa_options (
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
*/	} // insertHeader()
	private function updateHeader() {
		$data = array();
		if (isset($_POST['data'])) $data = $_POST['data'];
		$this->resetHeader();
		$now = new DateTime();
		$id = $data['option_id'];
		$this->display($id,'update'); // Display already has the logic for loading the record.  TODO: Refactor into separate function.
		if (is_null($this->option_id) || $this->option_id <= 0) {
			return 'fail|Invalid option id for updating';
		}		
		if (isset($data['option_value']) && $data['option_value']!=$this->option_value) {
			// Only value is updatable in the System Options screen.
			$q = 'UPDATE aa_options SET option_value=?,last_update_by=?,last_update_date=NOW() WHERE option_id=?;';
			$stmt = $this->dbconn->prepare($q);
			if ($stmt!==false) {
				$stmt->bind_param('sii',$p1,$p2,$p3);
				$p1 = $data['option_value'];
				$p2 = $_SESSION['dbuserid'];
				$p3 = $id;
				$result = $stmt->execute();
				if ($stmt->affected_rows > 0) {
					echo 'updated';
				} else {
					if ($this->dbconn->error) {
						echo 'fail|'.$this->dbconn->error;
						$this->mb->addError($this->dbconn->error);
					} else echo 'fail|No rows updated';
				}
				$stmt->close();					
			} // if $stmt !== false
		} // if option_value was changed
		else echo 'fail|Only the value may be changed from this screen';
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