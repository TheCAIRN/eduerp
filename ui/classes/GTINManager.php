<?php
class GTINManager extends ERPBase {
	private $entity_id;
	private $division_id;
	private $department_id;
	private $item_type_code;
	private $manufacturer_id;
	private $description;
	private $first_used;
	private $last_used;
	private $last_gtin;
	private $last_carton;
	private $last_bol;
	private $visible;
	private $rev_enabled;
	private $rev_number;
	private $created_by;
	private $creation_date;
	private $last_update_by;
	private $last_update_date;
	
	public function __construct ($link=null) {
		parent::__construct($link);
		$this->supportsNotes = false;
		$this->supportsAttachments = false;
		
		$this->resetHeader();
	} // __construct
	public function resetHeader() {
	
	} // resetHeader()
	public function calculateGTIN14CheckDigit($data) {
		if (strlen($data)==12) $data = '0'.$data;
		if (strlen($data)!=11 && strlen($data)!=13) {
			echo 'fail|Invalid GTIN length';
			return 'X';
		}
		if (!ctype_digit($data)) {
			echo 'fail|GTIN must be numeric only';
			return 'X';
		}
		$sum = 0;
		for ($i=0;$i<strlen($data);$i+=2)
			$sum += 3*substr($data,$i,1);
		for ($i=1;$i<strlen($data);$i+=2)
			$sum += 1*substr($data,$i,1);
		$check = 10 - ($sum % 10);
		if ($check==10) $check = 0;
		return $check;
	} // calculateGTIN14CheckDigit()
	public function calculateBOL17CheckDigit($data) {
		
	} // calculateBOL17CheckDigit()
	public function calculateSSCC18CheckDigit($data) {
		
	} // calculateSSCC18CheckDigit()
	public function assignOneGTIN($entity,$division,$department,$itemtype) {
		$this->resetHeader();
		if (is_integer($entity) || ctype_digit($entity)) $this->entity_id = $entity;
		if (is_integer($division) || ctype_digit($division)) $this->division_id = $division;
		if (is_integer($department) || ctype_digit($department)) $this->department_id = $department;
		$q = 'SELECT manufacturer_id,description,first_used,last_used,last_gtin,last_carton,last_bol FROM item_gtin_master WHERE
			visible=1 AND entity_id=? AND division_id=? AND department_id=? AND item_type_code=?';
		$stmt = $this->dbconn->prepare($q);
		$stmt->bind_param('iiis',$p1,$p2,$p3,$p4);
		$p1 = $entity;
		$p2 = $division;
		$p3 = $department;
		$p4 = $itemtype;
		$result = $stmt->execute();
		$stmt->store_result();
		if ($result!==false && $stmt->num_rows > 0) {
			$stmt->bind_result($this->manufacturer_id,$this->description,$this->first_used,$this->last_used,$this->last_gtin,$this->last_carton,$this->last_bol);
			$stmt->fetch();
			$this->entity_id = $entity;
			$this->division_id = $division;
			$this->department_id = $department;
			$this->item_type_code = $itemtype;
			$newgtin = '00'.$this->manufacturer_id;
			$digitsneeded = 13-strlen($newgtin);
			$newgtin .= sprintf('%0'.$digitsneeded.'d',($this->last_gtin)+1);
			if (strlen($newgtin)!=13) {
				// TODO: Look for unused numbers
				// TODO: Go to next resolution
				echo 'fail|All numbers have been used.';
				return;
			}
			$newgtin .= $this->calculateGTIN14CheckDigit($newgtin);
			$stmt->close();
			// TODO: Verify the chosen number isn't already used.
			// Update all entries using the same manufacturer_id
			$updq = 'UPDATE item_gtin_master SET last_gtin = last_gtin + 1,last_used=NOW() WHERE 
				visible=1 AND manufacturer_id=?';
			$stmt = $this->dbconn->prepare($updq);
			$stmt->bind_param('s',$p1);
			$p1 = $this->manufacturer_id;
			$result = $stmt->execute();
			echo 'success|'.$newgtin;
		} else {
			echo 'fail|No GTIN resolution. Try using a different entity or item type.';
			return;
		}
	} // assignOneGTIN()
	public function _templateSelect($id=0,$readonly=false) {
		return parent::abstractSelect($id,$readonly,'zzzz_master','zzzz_id','zzzz_name','zzzz');
	} // _templateSelect()
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
		$_SESSION['currentScreen'] = 1000;
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
		$_SESSION['currentScreen'] = 2000;
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
	public function newRecord() {
		echo parent::abstractNewRecord('zzzz');
		$_SESSION['currentScreen'] = 3000;
	} // newRecord()
	private function insertHeader() {
		$this->resetHeader();
		$q = "INSERT INTO zzzz_master (
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
} // class GTINManager
?>