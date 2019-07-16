<?php
class Attachments extends ERPBase {
	private $location;
	public function __construct ($link=null) {
		parent::__construct($link);
		$this->supportsNotes = false;
		$this->supportsAttachments = false;
		$this->location = Options::GetOptionValue($link,'ATTACHMENT_LOCATION');
		if (substr($this->location,-1)=='/') $this->location = substr($this->location,0,strlen($this->location)-1);
		$this->resetHeader();
	} // __construct
	public function resetHeader() {
	
	} // resetHeader()
	public function AttachmentsSelect($id=0,$readonly=false) {
		return parent::abstractSelect($id,$readonly,'zzzz_master','zzzz_id','zzzz_name','zzzz');
	} // AttachmentsSelect()
	public function statusSelect($status='',$readonly=false,$include_label=false) {
		$html = '';
		if ($include_label) $html .= '<LABEL for="zzzzStatus">Status:</LABEL>';
		$html .= '<SELECT id="customerStatus">';
		if ($status=='A' || !$readonly) $html .= '<OPTION value="A"'.($status=='A'?' selected="selected">':'>').'Active</OPTION>';
		if ($status=='B' || !$readonly) $html .= '<OPTION value="B"'.($status=='B'?' selected="selected">':'>').'Bankrupt</OPTION>';
		if ($status=='D' || !$readonly) $html .= '<OPTION value="D"'.($status=='D'?' selected="selected">':'>').'Defunct</OPTION>';
		if ($status=='I' || !$readonly) $html .= '<OPTION value="I"'.($status=='I'?' selected="selected">':'>').'Temporarily Inactive</OPTION>';
		if ($status=='S' || !$readonly) $html .= '<OPTION value="S"'.($status=='S'?' selected="selected">':'>').'Seasonally Inactive</OPTION>';
		$html .= '</SELECT>';
		return $html;
	} // statusSelect()
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
		if (count($_FILES)==0) {
			echo 'fail|No files uploaded';
		}
		$q1 = 'SELECT attachment_id FROM aa_attachments WHERE file_name=?;'; // TODO: Add hash check to table
		$stmt1 = $this->dbconn->prepare($q1);
		$stmt1->bind_param('s',$p1);
		$q2 = 'INSERT INTO aa_attachments (file_name,attachment_type_id,uri,description) VALUES (?,?,?,?);';
		$stmt2 = $this->dbconn->prepare($q2);
		$stmt2->bind_param('siss',$i1,$i2,$i3,$i4);
		$table_name = $_POST['tablename'];
		$key_name = $_POST['primaryKey'];
		$q3 = 'INSERT INTO '.$table_name.' (attachment_id,'.$key_name.') VALUES (?,?);';
		$stmt3 = $this->dbconn->prepare($q3);
		if ($stmt3===false) {
			echo 'fail|'.$this->dbconn->error;
			return;
		}
		$stmt3->bind_param('ii',$k1,$k2);
		foreach ($_FILES as $file) {
			// Step 1: Does the file already exist in the attachments table?
			$hash = md5(file_get_contents($file['tmp_name']));
			$p1 = $file['name'];
			$stmt1->execute();
			$stmt1->store_result();
			$stmt1->bind_result($att_id);
			$stmt1->fetch();
			if (is_integer($att_id) && $att_id>0) {
				unlink($file['tmp_name']);
			} else {
				$success = move_uploaded_file($file['tmp_name'],$this->location.'/'.basename($file['name']));
				if (!$success) {
					echo 'fail|Could not move '.$file['name'];
					continue;
				}
				$i1 = $this->location.'/'.basename($file['name']);
				$i2 = $_POST['attachmentType'];
				// TODO: Bug = URI is not capturing the correct path
				if (strpos($this->location,'./')===0) $uri = 'http'.(isset($_SERVER['HTTPS'])?'s':'').'://'.$_SERVER['SERVER_NAME'].substr($this->location,1).'/'.basename($file['name']);
				elseif (strpos($this->location,'/')===0) $uri = 'http'.(isset($_SERVER['HTTPS'])?'s':'').'://'.$_SERVER['SERVER_NAME'].$this->location.'/'.basename($file['name']);
				else $uri = 'http'.(isset($_SERVER['HTTPS'])?'s':'').'://'.$_SERVER['SERVER_NAME'].'/'.$this->location.'/'.basename($file['name']);
				$i3 = $uri;
				$i4 = $_POST['description'];
				$result = $stmt2->execute();
				$stmt2->store_result();
				if ($result!==false) {
					$att_id = $this->dbconn->insert_id;
				} else {
					echo 'fail|Error inserting attachments: '.$this->dbconn->error;
					continue;
				}
			}
			// Step 2: Link the file to the requested record.
			$k1 = $att_id;
			$k2 = $_POST['currentRecord'];
			$result = $stmt3->execute();
			$stmt3->store_result();
			if ($result===false) {
				echo 'fail|Unable to link attachment with record: '.$this->dbconn->error;
			}
		} // foreach $FILES	
		echo 'inserted|success';
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
	public function removeRecord() {
		
	} // removeRecord()
} // class Attachments
?>