<?php
class BOMSteps extends ERPBase {
	private $bom_step_id;
	private $bom_step_name;
	private $description;
	public function __construct ($link=null) {
		parent::__construct($link);
		$this->supportsNotes = false;
		$this->supportsAttachments = false;
		$this->searchFields[] = array('bom_steps','bom_step_id','ID','integer');
		$this->searchFields[] = array('bom_steps','bom_step_name','Name','textbox');
		$this->searchFields[] = array('bom_steps','description','Description','textbox');
		
		$this->entryFields[] = array('bom_steps','','BOM Steps','fieldset');
		$this->entryFields[] = array('bom_steps','bom_step_id','ID','integerid');
		$this->entryFields[] = array('bom_steps','bom_step_name','Name','textbox');
		$this->entryFields[] = array('bom_steps','description','Description','textbox');
		$this->entryFields[] = array('bom_steps','','','endfieldset');
		$this->resetHeader();
	} // __construct
	public function resetHeader() {
		$this->bom_step_id = -1;
		$this->bom_step_name = '';
		$this->description = '';
	} // resetHeader()
	public function _templateSelect($id=0,$readonly=false) {
		return parent::abstractSelect($id,$readonly,'bom_steps','bom_step_id','bom_step_name','BOM Step');
	} // _templateSelect()
	public function listRecords() {
		parent::abstractListRecords('BOMSteps');
	} // listRecords()
	public function searchPage() {
		parent::abstractSearchPage('BOMStepsSearch');
	} // searchPage()
	public function executeSearch($criteria) {
		$q = "SELECT * FROM bom_steps ";
		// TODO: Add $criteria
		// TODO: Convert to prepared statements
		$q .= " ORDER BY bom_step_id";
		$result = $this->dbconn->query($q);
		if ($result!==false) {
			$this->recordSet = array();
			while ($row=$result->fetch_assoc()) {
				$this->recordSet[$row['bom_step_id']] = array('Name'=>$row['bom_step_name'],'Description'=>$row['description']);
			} // while rows
		} // if query succeeded
		$this->listRecords();
		$_SESSION['currentScreen'] = 1063;
		$_SESSION['lastCriteria'] = $criteria;
		if (!isset($_SESSION['searchResults'])) $_SESSION['searchResults'] = array();
		$_SESSION['searchResults']['BOMSteps'] = array_keys($this->recordSet);		
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
			FROM bom_steps s
			WHERE bom_step_id=?";
		$stmt = $this->dbconn->prepare($q);
		if ($stmt===false) {
			echo $this->dbconn->error;
			return;
		}
		$stmt->bind_param('i',$bsid);
		$bsid = $id;
		$result = $stmt->execute();
		// TODO: What if another user deletes the record while it's still in my search results?
		if ($result!==false) {
			$stmt->bind_result(
				$this->bom_step_id
				,$this->bom_step_name
				,$this->description
				,$crevyn
				,$crevnumber
				,$cuser_creation
				,$cdate_creation
				,$cuser_modify
				,$cdate_modify
			);
			$stmt->fetch();
			echo parent::abstractRecord($mode,'BOMSteps','',array('bom_step_id'=>$this->bom_step_id,'bom_step_name'=>$this->bom_step_name,'description'=>$this->description),null);
			echo parent::displayRecordAudit('readonly="readonly"',$crevyn,$crevnumber,$cuser_creation,$cdate_creation,$cuser_modify,$cdate_modify);
		} // if result
		$stmt->close();			
		echo $html;
		$_SESSION['currentScreen'] = 2063;
		if (!isset($_SESSION['searchResults']) && !isset($_SESSION['searchResults']['BOMSteps']))
			$_SESSION['idarray'] = array(0,0,$id,0,0);
		else {
			$idloc = array_search($id,$_SESSION['searchResults']['BOMSteps'],false);
			$f = $_SESSION['searchResults']['BOMSteps'][0];
			$l = $_SESSION['searchResults']['BOMSteps'][] = array_pop($_SESSION['searchResults']['BOMSteps']); // https://stackoverflow.com/questions/3687358/whats-the-best-way-to-get-the-last-element-of-an-array-without-deleting-it#comment63556865_3687358
			if ($idloc > 0) $p = $_SESSION['searchResults']['BOMSteps'][$idloc-1]; else $p = $f;
			if ($l != $id) $n = $_SESSION['searchResults']['BOMSteps'][$idloc+1]; else $n = $l;
			$_SESSION['idarray'] = array($f,$p,$id,$n,$l);
		}		
	} // display()
	public function newRecord() {
		echo parent::abstractNewRecord('BOMSteps');
		$_SESSION['currentScreen'] = 3063;
	} // newRecord()
	public function editRecord($id) {
		$this->display($id,'edit');
		$_SESSION['currentScreen'] = 4063;
	}
	private function insertHeader() {
		$this->resetHeader();
		$q = "INSERT INTO bom_steps (bom_step_name,description,
			rev_enabled,rev_number,created_by,creation_date,last_update_by,last_update_date) VALUES 
			(?,?,?,?,?,NOW(),?,NOW());";
		$stmt = $this->dbconn->prepare($q);
		if ($stmt===false) {
			echo 'fail|'.$this->dbconn->error;
			return;
		}
		// v.2019: Not supporting revisions in the front end, but the database support still exists
		$rev_enabled='false';
		$rev_number = 1;
		$stmt->bind_param('sssiii',$p1,$p2,$p12,$p13,$p14,$p16);
		$p1 = isset($_POST['bom_step_name'])?$_POST['bom_step_name']:null;
		$p2 = isset($_POST['description'])?$_POST['description']:'';
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