<?php
class Dashboards extends ERPBase {
	public function __construct ($link=null) {
		parent::__construct($link);
		$this->supportsNotes = false;
		$this->supportsAttachments = false;
		
		$this->resetHeader();
	} // __construct
	public function resetHeader() {
	
	} // resetHeader()
	public function DashboardsSelect($id=0,$readonly=false) {
		return parent::abstractSelect($id,$readonly,'zzzz_master','zzzz_id','zzzz_name','zzzz');
	} // DashboardsSelect()
	public function listRecords() {
		parent::abstractListRecords('zzzz');
	} // listRecords()
	public function searchPage() {
		parent::abstractSearchPage('zzzzSearch');
	} // searchPage()
	public function executeSearch($criteria) {
		$q = "SELECT * FROM zzzz_master ";
		// TODO: Add $criteria
		// TODO: Convert to prepared statements
		$q .= " ORDER BY zzzz_id";
		$result = $this->dbconn->query($q);
		if ($result!==false) {
			$this->recordSet = array();
			while ($row=$result->fetch_assoc()) {
				$this->recordSet[$row['zzzz_id']] = array();
			} // while rows
		} // if query succeeded
		$this->listRecords();
		$_SESSION['currentScreen'] = 1032;
		$_SESSION['lastCriteria'] = $criteria;
		if (!isset($_SESSION['searchResults'])) $_SESSION['searchResults'] = array();
		$_SESSION['searchResults']['zzzz'] = array_keys($this->recordSet);		
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
			FROM zzzz_master c 
			WHERE zzzz_id=?";
		$stmt = $this->dbconn->prepare($q);
		if ($stmt===false) {
			echo $this->dbconn->error;
			return;
		}
		$stmt->bind_param('i',$zzzzid);
		$zzzzid = $id;
		$result = $stmt->execute();
		// TODO: What if another user deletes the record while it's still in my search results?
		if ($result!==false) {
			$stmt->bind_result(
			
			);
			$stmt->fetch();
			if ($readonly) $cls = 'RecordView'; else $cls = 'RecordEdit';
			if ($readonly) $inputtextro = ' readonly="readonly"'; else $inputtextro = '';
			$html .= '<FIELDSET id="zzzzRecord" class="'.$cls.'">';
			$html .= '<LABEL for="zzzzid">zzzz ID:</LABEL><B id="zzzzid">'.$id.'</B>';
			$html .= $this->statusSelect($status,$readonly);
			$html .= parent::displayRecordAudit($inputtextro,$crevyn,$crevnumber,$cuser_creation,$cdate_creation,$cuser_modify,$cdate_modify);
			$html .= '</FIELDSET>';
		} // if result
		$stmt->close();			
		echo $html;
		$_SESSION['currentScreen'] = 2032;
		if (!isset($_SESSION['searchResults']) && !isset($_SESSION['searchResults']['zzzz']))
			$_SESSION['idarray'] = array(0,0,$id,0,0);
		else {
			$idloc = array_search($id,$_SESSION['searchResults']['zzzz'],false);
			$f = $_SESSION['searchResults']['zzzz'][0];
			$l = $_SESSION['searchResults']['zzzz'][] = array_pop($_SESSION['searchResults']['zzzz']); // https://stackoverflow.com/questions/3687358/whats-the-best-way-to-get-the-last-element-of-an-array-without-deleting-it#comment63556865_3687358
			if ($idloc > 0) $p = $_SESSION['searchResults']['zzzz'][$idloc-1]; else $p = $f;
			if ($l != $id) $n = $_SESSION['searchResults']['zzzz'][$idloc+1]; else $n = $l;
			$_SESSION['idarray'] = array($f,$p,$id,$n,$l);
		}		
	} // display()
} // class Dashboards
?>