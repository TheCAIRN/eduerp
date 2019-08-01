<?php
class Production extends ERPBase {
	private $prod_id;
	private $entity_id;
	private $division_id;
	private $department_id;
	private $resulting_product_id;
	private $prod_start;
	private $prod_due;
	private $prod_finished;
	private $bom_id;
	private $rev_enabled;
	private $rev_number;
	private $created_by;
	private $creation_date;
	private $last_update_by;
	private $last_update_date;
	private $column_list_header;
	
	private $prod_detail_id;
	private $prod_step_number;
	private $bom_detail_id;
	private $item_consumed_id;
	private $item_generated_id;
	private $step_started;
	private $step_due;
	private $step_finished;
	private $step_cost;
	private $currency_code;
	private $quantity_consumed;
	private $quantity_generated;
	private $detail_rev_enabled;
	private $detail_rev_number;
	private $detail_created_by;
	private $detail_creation_date;
	private $detail_last_update_by;
	private $detail_last_update_date;
	private $column_list_detail;
	private $detail_array();
	
	public function __construct ($link=null) {
		parent::__construct($link);
		$this->supportsNotes = 'prod_header_notes';
		$this->primaryKey = 'prod_id';
		$this->supportsAttachments = false;
		
		$this->resetHeader();
	} // __construct
	public function resetHeader() {
		$this->prod_id = null;
		$this->entity_id = null;
		$this->division_id = null;
		$this->department_id = null;
		$this->resulting_product_id = null;
		$this->prod_start = null;
		$this->prod_due = null;
		$this->prod_finished = null;
		$this->bom_id = null;
		$this->rev_enabled = false;
		$this->rev_number = 1;
		$this->created_by = null;
		$this->creation_date = null;
		$this->last_update_by = null;
		$this->last_update_date = null;
		$this->detail_array = array();
	} // resetHeader()
	public function resetDetail() {
		$this->prod_detail_id = null;
		$this->prod_step_number = 1;
		$this->bom_detail_id = null;
		$this->item_consumed_id = null;
		$this->item_generated_id = null;
		$this->step_started = null;
		$this->step_due = null;
		$this->step_finished = null;
		$this->step_cost = null;
		$this->currency_code = null;
		$this->quantity_consumed = 0.00;
		$this->quantity_generated = 0.00;
		$this->detail_rev_enabled = false;
		$this->detail_rev_number = 1;
		$this->detail_created_by = null;
		$this->detail_creation_date = null;
		$this->detail_last_update_by = null;
		$this->detail_last_update_date = null;
	} // resetDetail()
	public function arrayifyHeader() {
		
	} // arrayifyHeader()
	public function arrayifyDetail() {
		
	} // arrayifyDetail()
	public function listRecords() {
		parent::abstractListRecords('Production');
	} // listRecords()
	public function searchPage() {
		parent::abstractSearchPage('ProductionSearch');
	} // searchPage()
	public function executeSearch($criteria) {
		$q = "SELECT * FROM prod_header ";
		// TODO: Add $criteria
		// TODO: Convert to prepared statements
		$q .= " ORDER BY prod_id";
		$result = $this->dbconn->query($q);
		if ($result!==false) {
			$this->recordSet = array();
			while ($row=$result->fetch_assoc()) {
				$this->recordSet[$row['prod_id']] = array();
			} // while rows
		} // if query succeeded
		$this->listRecords();
		$_SESSION['currentScreen'] = 1008;
		$_SESSION['lastCriteria'] = $criteria;
		if (!isset($_SESSION['searchResults'])) $_SESSION['searchResults'] = array();
		$_SESSION['searchResults']['Production'] = array_keys($this->recordSet);		
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
			FROM prod_header h
			WHERE prod_id=?";
		$stmt = $this->dbconn->prepare($q);
		if ($stmt===false) {
			echo $this->dbconn->error;
			return;
		}
		$stmt->bind_param('i',$prodid);
		$prodid = $id;
		$result = $stmt->execute();
		// TODO: What if another user deletes the record while it's still in my search results?
		if ($result!==false) {
			$stmt->bind_result(
			
			);
			$stmt->fetch();
			if ($readonly) $cls = 'RecordView'; else $cls = 'RecordEdit';
			if ($readonly) $inputtextro = ' readonly="readonly"'; else $inputtextro = '';
			$html .= '<FIELDSET id="ProductionRecord" class="'.$cls.'">';
			$html .= '<LABEL for="prod_id">Production ID:</LABEL><B id="prod_id">'.$id.'</B>';
			$html .= $this->statusSelect($status,$readonly);
			$html .= parent::displayRecordAudit($inputtextro,$crevyn,$crevnumber,$cuser_creation,$cdate_creation,$cuser_modify,$cdate_modify);
			$html .= '</FIELDSET>';
		} // if result
		$stmt->close();			
		echo $html;
		$_SESSION['currentScreen'] = 2008;
		if (!isset($_SESSION['searchResults']) && !isset($_SESSION['searchResults']['Production']))
			$_SESSION['idarray'] = array(0,0,$id,0,0);
		else {
			$idloc = array_search($id,$_SESSION['searchResults']['Production'],false);
			$f = $_SESSION['searchResults']['Production'][0];
			$l = $_SESSION['searchResults']['Production'][] = array_pop($_SESSION['searchResults']['Production']); // https://stackoverflow.com/questions/3687358/whats-the-best-way-to-get-the-last-element-of-an-array-without-deleting-it#comment63556865_3687358
			if ($idloc > 0) $p = $_SESSION['searchResults']['Production'][$idloc-1]; else $p = $f;
			if ($l != $id) $n = $_SESSION['searchResults']['Production'][$idloc+1]; else $n = $l;
			$_SESSION['idarray'] = array($f,$p,$id,$n,$l);
		}		
	} // display()
	public function newRecord() {
		echo parent::abstractNewRecord('Production');
		$_SESSION['currentScreen'] = 3008;
	} // newRecord()
	public function editRecord($id) {
		$this->display($id,'edit');
		$_SESSION['currentScreen'] = 4008;
	}
	private function insertHeader() {
		$this->resetHeader();
		$q = "INSERT INTO prod_header (
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
	private function insertDetail() {
		
	} // insertDetail()
	private function updateHeader() {
	
	} // updateHeader()
	private function updateDetail() {
		
	} // updateDetail()
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