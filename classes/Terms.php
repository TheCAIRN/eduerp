<?php
class Terms extends ERPBase {
    private $terms_id;
    private $terms_code;
    private $terms_name;
    private $terms_type;
    private $terms_basis;
    private $discount_percent_1;
    private $discount_percent_2;
    private $discount_percent_3;
    private $discount_days_1;
    private $discount_days_2;
    private $discount_days_3;
    private $tier1_type;
    private $tier2_type;
    private $tier3_type;
    private $status;
    private $column_list = 'terms_id,terms_code,terms_name,terms_type,terms_basis,discount_percent_1,discount_percent_2,discount_percent_3,discount_days_1,discount_days_2,discount_days_3,tier1_type,tier2_type,tier3_type,status';
	public function __construct ($link=null) {
		parent::__construct($link);
		$this->supportsNotes = false;
		$this->supportsAttachments = false;
		$this->searchFields[] = array('aa_terms','unified_search','Type in any part of the terms code or description and click Search','textbox');
		
		//$this->entryFields[] = array('aa_terms','','Terms','fieldset');
		$this->entryFields[] = array('aa_terms','terms_id','ID','integerid');
		$this->entryFields[] = array('aa_terms','terms_code','Code','textbox');
		$this->entryFields[] = array('aa_terms','terms_name','Description','textbox');
		$this->entryFields[] = array('aa_terms','terms_type','Type','integer');
		$this->entryFields[] = array('aa_terms','terms_basis','Basis','integer');
		$this->entryFields[] = array('aa_terms','tier1_type','Tier Type 1','function',$this,'tierTypeSelect','1');
		$this->entryFields[] = array('aa_terms','discount_percent_1','Discount % 1','decimal',9,4);
		$this->entryFields[] = array('aa_terms','discount_days_1','Discount days 1','integer');
		$this->entryFields[] = array('aa_terms','tier2_type','Tier Type 2','function',$this,'tierTypeSelect','2');
		$this->entryFields[] = array('aa_terms','discount_percent_2','Discount % 2','decimal',9,4);
		$this->entryFields[] = array('aa_terms','discount_days_2','Discount days 2','integer');
		$this->entryFields[] = array('aa_terms','tier3_type','Tier Type 3','function',$this,'tierTypeSelect','3');
		$this->entryFields[] = array('aa_terms','discount_percent_3','Discount % 3','decimal',9,4);
		$this->entryFields[] = array('aa_terms','discount_days_3','Discount days 3','integer');
		$this->entryFields[] = array('aa_terms','status','Status','function',$this,'statusSelect');
		//$this->entryFields[] = array('aa_terms','','','endfieldset');	
		
		$this->resetHeader();
	} // __construct
	public function resetHeader() {
        $this->terms_id = -1;
        $this->terms_code = '';
        $this->terms_name = '';
        $this->terms_type = '';
        $this->terms_basis = '';
        $this->discount_percent_1 = 0.00;
        $this->discount_percent_2 = 0.00;
        $this->discount_percent_3 = 0.00;
        $this->discount_days_1 = 0;
        $this->discount_days_2 = 0;
        $this->discount_days_3 = 0;
        $this->tier1_type = '';
        $this->tier2_type = '';
        $this->tier3_type = '';
        $this->status = 'A';
	} // resetHeader()
	public function arrayify() {
        return array('terms_id'=>$this->terms_id,'terms_code'=>$this->terms_code,'terms_name'=>$this->terms_name,'terms_type'=>$this->terms_type,'terms_basis'=>$this->terms_basis,
           'tier1_type'=>$this->tier1_type, 'discount_percent_1'=>$this->discount_percent_1,'discount_days_1'=>$this->discount_days_1,
           'tier2_type'=>$this->tier2_type,'discount_percent_2'=>$this->discount_percent_2,'discount_days_2'=>$this->discount_days_2,
           'tier3_type'=>$this->tier3_type,'discount_percent_3'=>$this->discount_percent_3,'discount_days_3'=>$this->discount_days_3,'status'=>$this->status);
	} // arrayify
	public function termsSelect($id=0,$readonly=false) {
		return parent::abstractSelect($id,$readonly,'aa_terms','terms_id','terms_name','Terms');
	} // _templateSelect()
	public function tierTypeSelect($status='X',$readonly=false,$include_label=false,$param=null) {
		$html = '';
		$id = "tier{$param}_type";
		if ($include_label) $html .= "<LABEL for=\"$id\">Tier:</LABEL>";
		$html .= "<SELECT id=\"$id\">";
		if ($status=='A' || !$readonly) $html .= '<OPTION value="A"'.($status=='A'?' selected="selected">':'>').'Due days from invoice date</OPTION>';
		if ($status=='B' || !$readonly) $html .= '<OPTION value="B"'.($status=='B'?' selected="selected">':'>').'Due days from beginning of month</OPTION>';
		if ($status=='E' || !$readonly) $html .= '<OPTION value="E"'.($status=='E'?' selected="selected">':'>').'Due days before end of month</OPTION>';
		if ($status=='N' || !$readonly) $html .= '<OPTION value="N"'.($status=='N'?' selected="selected">':'>').'Due days after the terms basis</OPTION>';
		if ($status=='P' || !$readonly) $html .= '<OPTION value="P"'.($status=='P'?' selected="selected">':'>').'Due days after the end of month</OPTION>';
		if ($status=='X' || !$readonly) $html .= '<OPTION value="X"'.($status=='X'?' selected="selected">':'>').'Tier not used</OPTION>';
		$html .= '</SELECT>';
		return $html;
	} // statusSelect()
	public function statusSelect($status='',$readonly=false,$include_label=false) {
		$html = '';
		if ($include_label) $html .= '<LABEL for="termsStatus">Status:</LABEL>';
		$html .= '<SELECT id="termsStatus">';
		if ($status=='A' || !$readonly) $html .= '<OPTION value="A"'.($status=='A'?' selected="selected">':'>').'Active</OPTION>';
		if ($status=='I' || !$readonly) $html .= '<OPTION value="P"'.($status=='I'?' selected="selected">':'>').'Inactive</OPTION>';
		$html .= '</SELECT>';
		return $html;
	} // statusSelect()
	public function listRecords() {
		parent::abstractListRecords('Terms');
	} // listRecords()
	public function searchPage() {
		parent::abstractSearchPage('TermsSearch');
	} // searchPage()
	public function executeSearch($criteria) {
		$q = "SELECT * FROM aa_terms ";
		// TODO: Add $criteria
		// TODO: Convert to prepared statements
		$q .= " ORDER BY terms_id";
		$result = $this->dbconn->query($q);
		if ($result!==false) {
			$this->recordSet = array();
			while ($row=$result->fetch_assoc()) {
				$this->recordSet[$row['terms_id']] = array('code'=>$row['terms_code'],'description'=>$row['terms_name']);
				
			} // while rows
		} // if query succeeded
		$this->listRecords();
		$_SESSION['currentScreen'] = 1059;
		$_SESSION['lastCriteria'] = $criteria;
		if (!isset($_SESSION['searchResults'])) $_SESSION['searchResults'] = array();
		$_SESSION['searchResults']['terms'] = array_keys($this->recordSet);		
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
			FROM aa_terms  
			WHERE terms_id=?";
		$stmt = $this->dbconn->prepare($q);
		if ($stmt===false) {
			echo $this->dbconn->error;
			return;
		}
		$stmt->bind_param('i',$termsid);
		$termsid = $id;
		$result = $stmt->execute();
		// TODO: What if another user deletes the record while it's still in my search results?
		if ($result!==false) {
			$stmt->bind_result(
                $this->terms_id,
                $this->terms_code,
                $this->terms_name,
                $this->terms_type,
                $this->terms_basis,
                $this->discount_percent_1,
                $this->discount_percent_2,
                $this->discount_percent_3,
                $this->discount_days_1,
                $this->discount_days_2,
                $this->discount_days_3,
                $this->tier1_type,
                $this->tier2_type,
                $this->tier3_type,
                $this->status
			);
			$stmt->fetch();
			$stmt->store_result();
		} // if result
		$stmt->close();			
		if ($mode!='update') {
			$hdata = $this->arrayify();
			echo '<FIELDSET id="TermsRecord" class="Record'.ucwords($mode).'">';
			echo parent::abstractRecord($mode,'Terms','',$hdata,null);
			echo '</FIELDSET>';
		}
		//echo $html;
		$_SESSION['currentScreen'] = 2059;
		if (!isset($_SESSION['searchResults']) && !isset($_SESSION['searchResults']['Terms']))
			$_SESSION['idarray'] = array(0,0,$id,0,0);
		elseif (count($_SESSION['searchResults'])==1) {
			$_SESSION['idarray'] = array($id,$id,$id,$id,$id);
		} else {
			$idloc = array_search($id,$_SESSION['searchResults']['Terms'],false);
			$f = $_SESSION['searchResults']['Terms'][0];
			$l = $_SESSION['searchResults']['Terms'][] = array_pop($_SESSION['searchResults']['Terms']); // https://stackoverflow.com/questions/3687358/whats-the-best-way-to-get-the-last-element-of-an-array-without-deleting-it#comment63556865_3687358
			if ($idloc > 0) $p = $_SESSION['searchResults']['Terms'][$idloc-1]; else $p = $f;
			if ($l != $id) $n = $_SESSION['searchResults']['Terms'][$idloc+1]; else $n = $l;
			$_SESSION['idarray'] = array($f,$p,$id,$n,$l);
		}		
	} // display()
	public function newRecord() {
		echo parent::abstractNewRecord('terms');
		$_SESSION['currentScreen'] = 3059;
	} // newRecord()
	public function editRecord($id) {
		$this->display($id,'edit');
		$_SESSION['currentScreen'] = 4059;
	}
	private function insertHeader() {
		$this->resetHeader();
		$data = $_POST['data'];
		$this->terms_id = isset($data['terms_id'])?$data['terms_id']:-1;
		$this->terms_code = isset($data['terms_code'])?$data['terms_code']:'';
		$this->terms_name = isset($data['terms_name'])?$data['terms_name']:'';
		$this->terms_basis = isset($data['terms_basis'])?$data['terms_basis']:null;
		$this->terms_type = isset($data['terms_type'])?$data['terms_type']:null;
		$this->tier1_type = isset($data['tier1_type'])?$data['tier1_type']:null;
		$this->discount_percent_1 = isset($data['discount_percent_1'])?$data['discount_percent_1']:0.00;
		$this->discount_days_1 = isset($data['discount_days_1'])?$data['discount_days_1']:0;
		$this->tier2_type = isset($data['tier2_type'])?$data['tier2_type']:null;
		$this->discount_percent_2 = isset($data['discount_percent_2'])?$data['discount_percent_2']:0.00;
		$this->discount_days_2 = isset($data['discount_days_2'])?$data['discount_days_2']:0;
		$this->tier3_type = isset($data['tier3_type'])?$data['tier3_type']:null;
		$this->discount_percent_3 = isset($data['discount_percent_3'])?$data['discount_percent_3']:0.00;
		$this->discount_days_3 = isset($data['discount_days_3'])?$data['discount_days_3']:0;
		$this->status = isset($data['status'])?$data['status']:'A';
		$q = "INSERT INTO aa_terms (
			terms_code,terms_name,terms_type,terms_basis,discount_percent_1,discount_percent_2,discount_percent_3,discount_days_1,discount_days_2,discount_days_3,tier1_type,tier2_type,tier3_type,status) VALUES 
			(?,?,?,?,?,?,?,?,?,?,?,?,?,?);";
		$stmt = $this->dbconn->prepare($q);
		$stmt->bind_param('ssiidddiiissss',$p1,$p2,$p3,$p4,$p5,$p6,$p7,$p8,$p9,$p10,$p11,$p12,$p13,$p14);
		$p1 = $this->terms_code;
		$p2 = $this->terms_name;
		$p3 = $this->terms_basis;
		$p4 = $this->terms_type;
		$p5 = $this->discount_percent_1;
		$p6 = $this->discount_percent_2;
		$p7 = $this->discount_percent_3;
		$p8 = $this->discount_days_1;
		$p9 = $this->discount_days_2;
		$p10 = $this->discount_days_3;
		$p11 = $this->tier1_type;
		$p12 = $this->tier2_type;
		$p13 = $this->tier3_type;
		$p14 = $this->status;
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
		$rtn = '';
		$this->resetHeader();
		$now = new DateTime();
		$data = $_POST['data'];
		$id = $data['terms_id'];
		if ((!is_integer($id) && !ctype_digit($id)) || $id<1) {
			return 'fail|Invalid terms id for updating';
		}
		$this->display($id,'update'); // Display already has the logic for loading the record.  TODO: Refactor into separate function.
		if (is_null($this->terms_id) || $this->terms_id <= 0) {
			return 'fail|Invalid terms id for updating';
		}
		$update = array();
		if (isset($data['terms_code']) && $data['terms_code']!=$this->terms_code) $update['terms_code'] = array('s',$data['terms_code']);
		if (isset($data['terms_name']) && $data['terms_name']!=$this->terms_name) $update['terms_name'] = array('s',$data['terms_name']);
		if (isset($data['terms_basis']) && $data['terms_basis']!=$this->terms_basis) $update['terms_basis'] = array('i',$data['terms_basis']);
		if (isset($data['terms_type']) && $data['terms_type']!=$this->terms_type) $update['terms_type'] = array('i',$data['terms_type']);
		if (isset($data['tier1_type']) && $data['tier1_type']!=$this->tier1_type) $update['tier1_type'] = array('s',$data['tier1_type']);
		if (isset($data['discount_percent_1']) && $data['discount_percent_1']!=$this->discount_percent_1) $update['discount_percent_1'] = array('d',$data['discount_percent_1']);
		if (isset($data['discount_days_1']) && $data['discount_days_1']!=$this->discount_days_1) $update['discount_days_1'] = array('i',$data['discount_days_1']);
		if (isset($data['tier2_type']) && $data['tier2_type']!=$this->tier2_type) $update['tier2_type'] = array('s',$data['tier2_type']);
		if (isset($data['discount_percent_2']) && $data['discount_percent_2']!=$this->discount_percent_2) $update['discount_percent_2'] = array('d',$data['discount_percent_2']);
		if (isset($data['discount_days_2']) && $data['discount_days_2']!=$this->discount_days_2) $update['discount_days_2'] = array('i',$data['discount_days_2']);
		if (isset($data['tier3_type']) && $data['tier3_type']!=$this->tier3_type) $update['tier3_type'] = array('s',$data['tier3_type']);
		if (isset($data['discount_percent_3']) && $data['discount_percent_3']!=$this->discount_percent_3) $update['discount_percent_3'] = array('d',$data['discount_percent_3']);
		if (isset($data['discount_days_3']) && $data['discount_days_3']!=$this->discount_days_3) $update['discount_days_3'] = array('i',$data['discount_days_3']);
		if (isset($data['status']) && $data['status']!=$this->status) $update['status'] = array('s',$data['status']);
		// Create UPDATE String
		
		if (count($update)<=0) { 
			return 'fail|Nothing to update';
		}
		$q = 'UPDATE aa_terms SET ';
		$ctr = 0;
		$bp_types = '';
		$bp_values = array_fill(0,count($update),null);
		foreach ($update as $field=>$data) {
			if ($ctr > 0) $q .= ',';
			$q .= "$field=?";
			$bp_types .= $data[0];
			$bp_values[$ctr] = $data[1];
			$ctr++;
		}
		$q .= ' WHERE terms_id=?';
		$ctr++;
		$bp_types .= 'i';
		$bp_values[$ctr] = $this->terms_id;
		$stmt = $this->dbconn->prepare($q);
		/* The internet has a lot of material about different ways to pass a variable number of arguments to bind_param.
		   I feel that using Reflection is the best tool for the job.
		   Reference: https://www.php.net/manual/en/mysqli-stmt.bind-param.php#107154
		*/
		$bp_method = new ReflectionMethod('mysqli_stmt','bind_param');
		$bp_refs = array();
		foreach ($bp_values as $key=>$value) {
			$bp_refs[$key] = &$bp_values[$key];
		}
		array_unshift($bp_values,$bp_types);
		$bp_method->invokeArgs($stmt,$bp_values);
		$stmt->execute();
		if ($stmt->affected_rows > 0) {
			$rtn .= 'updated';
		} else {
			if ($this->dbconn->error) {
				$rtn .= 'fail|'.$this->dbconn->error;
				$this->mb->addError($this->dbconn->error);
			} else $rtn .= 'fail|No rows updated';
		}
		$stmt->close();
		return $rtn;	
	} // updateHeader()
	public function insertRecord() {
		// TODO: Switch to returns in insertHeader, and echo from here.
		$this->insertHeader();
	} // insertRecord()
	public function updateRecord() {
		echo $this->updateHeader();
	} // updateRecord()
	public function saveRecord() {
	
	} // saveRecord()
} // class _template
?>
