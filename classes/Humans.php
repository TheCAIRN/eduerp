<?php
class Humans extends ERPBase {
	private $menucode = 11;
	private $human_id;
	private $title;
	private $given_name;
	private $middle;
	private $middle_2;
	private $family_name;
	private $suffix;
	private $alias;
	private $degrees;
	private $sex;
	private $birthdate;
	private $home_address;
	private $mother;
	private $father;
	private $spouse;
	private $unique_id;
	
	private $phone_id;
	private $phone_type_id;
	private $phone_number;
	
	private $email_id;
	private $email_user;
	private $email_domain;
	private $last_validated;
	private $bounce_count;
	
	private $column_list_human = "human_id,title,given_name,middle,middle_2,family_name,suffix,alias,degrees,sex,birthdate,home_address,mother,father,spouse,unique_id";
	private $column_list_phone = "phone_id,phone_type_id,phone_number";
	private $column_list_email = "email_id,email_user,email_domain,last_validated,bounce_count";
	
	public function __construct ($link=null) {
		parent::__construct($link);
		$this->supportsNotes = false;
		$this->supportsAttachments = false;
		$this->searchFields[] = array('cx_humans','unified_search','Type in any part of the address and click Search','textbox');
		$this->entryFields[] = array('cx_humans','human_id','ID','integerid');
		$this->entryFields[] = array('cx_humans','title','Title','textbox');
		$this->entryFields[] = array('cx_humans','given_name','Given Name','textbox');
		$this->entryFields[] = array('cx_humans','middle','Middle Name 1','textbox');
		$this->entryFields[] = array('cx_humans','middle_2','Middle Name 2','textbox');
		$this->entryFields[] = array('cx_humans','family_name','Family Name','textbox');
		$this->entryFields[] = array('cx_humans','suffix','Suffix','textbox');
		$this->entryFields[] = array('cx_humans','alias','Alias','textbox');
		$this->entryFields[] = array('cx_humans','degrees','Degrees','textbox');
		$this->entryFields[] = array('cx_humans','sex','Sex','dropdown',array(array('F','Female'),array('M','Male'),array('O','Other')));
		$this->entryFields[] = array('cx_humans','birthdate','Birth Date','date');
		$this->entryFields[] = array('cx_humans','home_address','Home Address','embedded');
		$this->entryFields[] = array('cx_humans','home_address','Home Address','Address');
		$this->entryFields[] = array('cx_humans','','','endembedded');
		$this->entryFields[] = array('cx_humans','mother','Mother','embedded');
		$this->entryFields[] = array('cx_humans','mother','Mother','Human');
		$this->entryFields[] = array('cx_humans','','','endembedded');
		$this->entryFields[] = array('cx_humans','father','Father','embedded');
		$this->entryFields[] = array('cx_humans','father','Father','Human');
		$this->entryFields[] = array('cx_humans','','','endembedded');
		$this->entryFields[] = array('cx_humans','spouse','Spouse','embedded');
		$this->entryFields[] = array('cx_humans','spouse','Spouse','Human');
		$this->entryFields[] = array('cx_humans','','','endembedded');
		$this->entryFields[] = array('cx_humans','unique_id','Unique ID','textbox'); // TODO: Change to GUID generator
		
		$this->resetHeader();
	} // __construct
	public function resetHeader() {
		$this->human_id = -1;
		$this->title = '';
		$this->given_name = '';
		$this->middle = '';
		$this->middle_2 = '';
		$this->family_name = '';
		$this->suffix = '';
		$this->alias = '';
		$this->degrees = '';
		$this->sex = '';
		$this->birthdate = null;
		$this->home_address = null;
		$this->mother = '';
		$this->father = '';
		$this->spouse = '';
		$this->unique_id = '';
		
		$this->phone_id = -1;
		$this->phone_type_id = 1;
		$this->phone_number = '';
		
		$this->email_id = -1;
		$this->email_user = '';
		$this->email_domain = '';
		$this->last_validated = null;
		$this->bounce_count = 0;
	} // resetHeader()
	private function arrayifyHeader() {
	
	} // arrayifyHeader()
	public function humanSelect($id=0,$readonly=false) {
		return parent::abstractSelect($id,$readonly,'cx_humans','human_id',"given_name+' '+family_name",'Humans');
	} // HumansSelect()
	public function phoneTypeSelect($id=0,$readonly=false) {
		return parent::abstractSelect($id,$readonly,'cx_phone_types','phone_type_id','phone_type_description','PhoneTypes');
	} // HumansSelect()
	public function statusSelect($status='',$readonly=false,$include_label=false) {
		$html = '';
		if ($include_label) $html .= '<LABEL for="HumansStatus">Status:</LABEL>';
		$html .= '<SELECT id="customerStatus">';
		if ($status=='A' || !$readonly) $html .= '<OPTION value="A"'.($status=='A'?' selected="selected">':'>').'Active</OPTION>';
		if ($status=='B' || !$readonly) $html .= '<OPTION value="B"'.($status=='B'?' selected="selected">':'>').'Bankrupt</OPTION>';
		if ($status=='D' || !$readonly) $html .= '<OPTION value="D"'.($status=='D'?' selected="selected">':'>').'Defunct</OPTION>';
		if ($status=='I' || !$readonly) $html .= '<OPTION value="I"'.($status=='I'?' selected="selected">':'>').'Temporarily Inactive</OPTION>';
		if ($status=='S' || !$readonly) $html .= '<OPTION value="S"'.($status=='S'?' selected="selected">':'>').'Seasonally Inactive</OPTION>';
		$html .= '</SELECT>';
		return $html;
	} // statusSelect()
	/*
	 * Human fields are linked from many different tables within the ERP system.  As a result, many other modules need to have access to 
	 * look up, select, and add human records.  The embed method provides that capability without changing $_SESSION['currentScreen'] or
	 * requiring the user to open a new tab.
	 *
	 * $id = The HTML id attribute of the fieldset.
	 * $mode = ['search' | 'lookup' | 'new' | 'save' | 'display']
	 * $data = An array of human fields, or other data as appropriate to the mode.
	 */
	public function embed($id='human',$mode='search',$data=null) {
		if ($mode=='search') {
			return $this->embed_search($id,$data);
		} elseif ($mode=='lookup') {
			return $this->embed_lookup($id,$data);
		} elseif ($mode=='display') {
			return $this->embed_display($id,$data);
		} elseif ($mode=='new') {
			return $this->embed_new($id,$data);
		} elseif ($mode=='save') {
			return $this->embed_save($id,$data);
		} else {
			$this->mb->addError('JQ Embedded Human does not understand mode, "'.$mode.'".');
		}
	} // embed()
	private function embed_search($id='human',$data=null) {
		$html = "<INPUT type=\"text\" id=\"$id\" placeholder=\"Type in any part of the name and click Search\" size=\"50\" />
			<BUTTON onClick=\"embeddedHumanSearch('$id');\">Search</BUTTON>
			<BUTTON onClick=\"embeddedHumanList('$id');\">List</BUTTON>
			<BUTTON onClick=\"embeddedHumanNew('$id');\">New</BUTTON>";
		return $html;		
	} // embed_search()
	private function embed_lookup($id='human',$data=null) {
		
	} // embed_lookup()
	private function embed_display($id='human',$data=null,$readonly=true) {
		
	} // embed_display()
	private function embed_new($id='human',$data=null) {
		
	} // embed_new()
	private function embed_save($id='human',$data=null) {
		
	} // embed_save()
	public function listRecords() {
		parent::abstractListRecords('Humans');
	} // listRecords()
	public function searchPage() {
		parent::abstractSearchPage('HumansSearch');
	} // searchPage()
	public function executeSearch($criteria) {
		if (!is_null($criteria) && is_array($criteria) && count($criteria)>0)
			if (is_array($criteria[0]) && count($criteria[0])>=2 && $criteria[0][0]=='unified_search') $criteria = $criteria[0][1];
			else $criteria='';
		else $criteria='';
		$q = "SELECT * FROM cx_humans ";
		// TODO: Add $criteria
		// TODO: Convert to prepared statements
		$q .= " ORDER BY Humans_id";
		$result = $this->dbconn->query($q);
		if ($result!==false) {
			$this->recordSet = array();
			while ($row=$result->fetch_assoc()) {
				$this->recordSet[$row['Humans_id']] = array();
			} // while rows
		} // if query succeeded
		$this->listRecords();
		$_SESSION['currentScreen'] = 1000+$this->menucode;
		$_SESSION['lastCriteria'] = $criteria;
		if (!isset($_SESSION['searchResults'])) $_SESSION['searchResults'] = array();
		$_SESSION['searchResults']['Humans'] = array_keys($this->recordSet);		
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
			FROM cx_humans c 
			WHERE human_id=?";
		$stmt = $this->dbconn->prepare($q);
		if ($stmt===false) {
			echo $this->dbconn->error;
			return;
		}
		$stmt->bind_param('i',$Humansid);
		$Humansid = $id;
		$result = $stmt->execute();
		// TODO: What if another user deletes the record while it's still in my search results?
		if ($result!==false) {
			$stmt->bind_result(
			
			);
			$stmt->fetch();
			$this->currentRecord = $id;
			$stmt->close();		
			if ($mode!='update') {
				$hdata = $this->arrayifyHeader();
				echo parent::abstractRecord($mode,'Humans','',$hdata,null);
			}
		} // if result
		else echo 'There was a problem accessing the requested record.';

		$_SESSION['currentScreen'] = 2000+$this->menucode;
		if (!isset($_SESSION['searchResults']) && !isset($_SESSION['searchResults']['Humans']))
			$_SESSION['idarray'] = array(0,0,$id,0,0);
		else {
			$idloc = array_search($id,$_SESSION['searchResults']['Humans'],false);
			$f = $_SESSION['searchResults']['Humans'][0];
			$l = $_SESSION['searchResults']['Humans'][] = array_pop($_SESSION['searchResults']['Humans']); // https://stackoverflow.com/questions/3687358/whats-the-best-way-to-get-the-last-element-of-an-array-without-deleting-it#comment63556865_3687358
			if ($idloc > 0) $p = $_SESSION['searchResults']['Humans'][$idloc-1]; else $p = $f;
			if ($l != $id) $n = $_SESSION['searchResults']['Humans'][$idloc+1]; else $n = $l;
			$_SESSION['idarray'] = array($f,$p,$id,$n,$l);
		}		
	} // display()
	public function newRecord() {
		echo '<FIELDSET class="RecordEdit" id="Humans_edit">';
		echo '<LEGEND onClick="$(this).siblings().toggle();">Person</LEGEND>';
		echo parent::abstractNewRecord('Humans');
		echo '</FIELDSET>';
		$_SESSION['currentScreen'] = 3000+$this->menucode;
	} // newRecord()
	public function editRecord($id=null) {
		$this->display($id,'edit');	
		$_SESSION['currentScreen'] = 4000+$this->menucode;
	} // editRecord()
	private function insertHeader() {
		$this->resetHeader();
		$q = "INSERT INTO cx_humans ({$this->column_list_human}) VALUES 
			(null,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?);";
		$stmt = $this->dbconn->prepare($q);
		$stmt->bind_param('ssssssssssiiiis',$p2,$p3,$p4,$p5,$p6,$p7,$p8,$p9,$p10,$p11,$p12,$p13,$p14,$p15,$p16);

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
	public function insertPhone() {
		
	} // insertPhone()
	public function updatePhone() {
		
	} // updatePhone()
	public function insertEmail() {
		
	} // insertEmail()
	public function updateEmail() {
		
	} // updateEmail()
	public function insertRecord() {
		$this->insertHeader();
	} // insertRecord()
	public function updateRecord() {
		$this->updateHeader();
	} // updateRecord()
	public function saveRecord() {
	
	} // saveRecord()
} // class Humans
?>
