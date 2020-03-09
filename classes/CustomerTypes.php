<?php
class CustomerTypes extends ERPBase {
	private $cust_type_code;
	private $description;
	public function __construct ($link=null) {
		parent::__construct($link);
		$this->supportsNotes = false;
		$this->supportsAttachments = false;
		$this->searchFields[] = array('cust_types','unified_search','Type in part of the type code or description and click Search','textbox');
		$this->entryFields[] = array('cust_types','','Customer Types','fieldset');
		$this->entryFields[] = array('cust_types','cust_type_code','Type Code','textbox');
		$this->entryFields[] = array('cust_types','description','Description','textbox');
		$this->entryFields[] = array('cust_types','','','endfieldset');
		$this->resetHeader();
	} // __construct
	public function resetHeader() {
		$this->cust_type_code = '';
		$this->description = '';
	} // resetHeader()
	public function CustomerTypesSelect($id=0,$readonly=false) {
		return parent::abstractSelect($id,$readonly,'cust_types','cust_type_code','description','CustomerTypes');
	} // CustomerTypesSelect()
	public function listRecords() {
		parent::abstractListRecords('CustomerTypes');
	} // listRecords()
	public function searchPage() {
		parent::abstractSearchPage('CustomerTypesSearch');
	} // searchPage()
	public function executeSearch($criteria) {
		if (!is_null($criteria) && is_array($criteria) && count($criteria)>0)
			if (is_array($criteria[0]) && count($criteria[0])>=2 && $criteria[0][0]=='unified_search') $criteria = $criteria[0][1];
			else $criteria='';
		else $criteria='';
		$q = "SELECT cust_type_code,description FROM cust_types ";
		$q .= "WHERE cust_type_code LIKE ? OR description LIKE ?";
		$q .= " ORDER BY cust_type_code";
		$stmt = $this->dbconn->prepare($q);
		if ($stmt!==false) {
			$stmt->bind_param('ss',$p1,$p2);
			if ($criteria=='') $p1 = $p2 = '%';
			else $p1 = $p2 = "%$criteria%";
			$result = $stmt->execute();
			if ($result!==false) {
				$stmt->bind_result($code,$text);
				$stmt->store_result();
				$this->recordSet = array();
				while ($stmt->fetch()) {
					$this->recordSet[$code] = array('description'=>$text);
				} // while
			} // if $result
			$stmt->close();
		}
		$this->listRecords();
		$_SESSION['currentScreen'] = 1023;
		$_SESSION['lastCriteria'] = $criteria;
		if (!isset($_SESSION['searchResults'])) $_SESSION['searchResults'] = array();
		$_SESSION['searchResults']['cust_types'] = array_keys($this->recordSet);		
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
		$q = "SELECT cust_type_code,description
			FROM cust_types c 
			WHERE cust_type_code=?";
		$stmt = $this->dbconn->prepare($q);
		if ($stmt===false) {
			echo $this->dbconn->error;
			return;
		}
		$stmt->bind_param('s',$cust_typesid);
		$cust_typesid = $id;
		$result = $stmt->execute();
		// TODO: What if another user deletes the record while it's still in my search results?
		if ($result!==false) {
			$stmt->bind_result(
				$this->cust_type_code,$this->description
			);
			$stmt->fetch();
			$this->currentRecord = $id;
			$stmt->close();		
			if ($mode!='update') {
				$hdata = $this->arrayifyHeader();
				echo parent::abstractRecord($mode,'CustomerTypes','',$hdata,null);
			}
		} // if result
		else echo 'There was a problem accessing the requested record.';

		$_SESSION['currentScreen'] = 2023;
		if (!isset($_SESSION['searchResults']) && !isset($_SESSION['searchResults']['cust_types']))
			$_SESSION['idarray'] = array(0,0,$id,0,0);
		else {
			$idloc = array_search($id,$_SESSION['searchResults']['cust_types'],false);
			$f = $_SESSION['searchResults']['cust_types'][0];
			$l = $_SESSION['searchResults']['cust_types'][] = array_pop($_SESSION['searchResults']['cust_types']); // https://stackoverflow.com/questions/3687358/whats-the-best-way-to-get-the-last-element-of-an-array-without-deleting-it#comment63556865_3687358
			if ($idloc > 0) $p = $_SESSION['searchResults']['cust_types'][$idloc-1]; else $p = $f;
			if ($l != $id) $n = $_SESSION['searchResults']['cust_types'][$idloc+1]; else $n = $l;
			$_SESSION['idarray'] = array($f,$p,$id,$n,$l);
		}		
	} // display()
	public function newRecord() {
		echo parent::abstractNewRecord('cust_types');
		$_SESSION['currentScreen'] = 3023;
	} // newRecord()
	public function editRecord($id=null) {
		$this->display($id,'edit');
		$_SESSION['currentScreen'] = 4023;
	} // editRecord()
	private function insertHeader() {
		$this->resetHeader();
		if (!isset($_POST['cust_type_code']) || !isset($_POST['description'])) {
			echo 'fail|Both code and description are required.';
			return;
		}
		$q = "INSERT INTO cust_types (
			cust_type_code,description) VALUES (?,?) ON DUPLICATE KEY UPDATE description=?;";
		$stmt = $this->dbconn->prepare($q);
		$stmt->bind_param('sss',$p1,$p2,$p3);
		$p1 = isset($_POST['cust_type_code'])?$_POST['cust_type_code']:'';
		$p2 = $p3 = isset($_POST['description'])?$_POST['description']:'';
		$result = $stmt->execute();
		if ($result!==false) {
			if ($this->dbconn->affected_rows==1) echo 'inserted|'.$p1;
			elseif ($this->dbconn->affected_rows==2) echo 'updated|'.$p1;
			else echo 'success|'.$p1;
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
} // class CustomerTypes
?>
