<?php
class Production extends ERPBase {
	private $prod_id;
	private $entity_id;
	private $division_id;
	private $department_id;
	private $resulting_product_id;
	private $maximum_quantity;
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
	private $planned_consumed;
	private $planned_generated;
	private $quantity_consumed;
	private $quantity_generated;
	private $detail_rev_enabled;
	private $detail_rev_number;
	private $detail_created_by;
	private $detail_creation_date;
	private $detail_last_update_by;
	private $detail_last_update_date;
	private $column_list_detail;
	private $detail_array = array();
	private $step_counter;
	private $step_timer;
	
	public function __construct ($link=null) {
		parent::__construct($link);
		$this->supportsNotes = 'prod_header_notes';
		$this->primaryKey = 'prod_id';
		$this->supportsAttachments = false;
		$this->searchFields[] = array('prod_header','unified_search','Type in any item related info and click Search','textbox');
		$this->searchFields[] = array('prod_header','included_finished','Include completed production?','checkbox');
		
		$this->entryFields[] = array('prod_header','','Production Header','fieldset');
		$this->entryFields[] = array('prod_header','prod_id','ID','integerid');
		$this->entryFields[] = array('prod_header','entity_id','Entity','dropdown','ent_entities',array('entity_id','entity_name'));
		$this->entryFields[] = array('prod_header','division_id','Division','dropdown','ent_division_master',array('division_id','division_name'));
		$this->entryFields[] = array('prod_header','department_id','Department','dropdown','ent_department_master',array('department_id','department_name'));
		$this->entryFields[] = array('prod_header','resulting_product_id','Resulting Product','embedded');
		$this->entryFields[] = array('prod_header','resulting_product_id','Resulting Product','Item');
		$this->entryFields[] = array('prod_header','','','endembedded');
		$this->entryFields[] = array('prod_header','maximum_quantity','Max Qty','decimal',24,5);
		$this->entryFields[] = array('prod_header','prod_start','Start date','datetime');
		$this->entryFields[] = array('prod_header','prod_due','Due date','datetime');
		$this->entryFields[] = array('prod_header','prod_finished','Finished date','datetime');
		$this->entryFields[] = array('prod_header','bom_id','BOM','dropdown','bom_header',array('bom_id','description'));
		$this->entryFields[] = array('prod_header','rev_enabled','Enable Revision Tracking','checkbox','rev_number');
		$this->entryFields[] = array('prod_header','rev_number','Revision number','integer');
		$this->entryFields[] = array('prod_header','','','endfieldset');
		// Production Entry is different than most modules, as the detail records are automatically generated based on the BOM steps.
		// The detail entry fields are needed for display() and edit().
		$this->entryFieldsAddDetail();
		$this->resetHeader();
	} // __construct
	private function entryFieldsAddDetail() {
		
	} // entryFieldsAddDetail
	private function entryFieldsRemoveDetail() {
		$this->entryFields = array_filter($this->entryFields,function($e) {
			return is_array($e) && $e[0]!='prod_detail';
		});
	} // entryFieldsRemoveDetail
	public function resetHeader() {
		$this->prod_id = null;
		$this->entity_id = null;
		$this->division_id = null;
		$this->department_id = null;
		$this->resulting_product_id = null;
		$this->maximum_quantity = 0.00;
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
		$this->step_counter = 1;
		$this->step_timer = null;
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
		$this->planned_consumed = 0.00;
		$this->planned_generated = 0.00;
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
		$result = null;
		$q = "SELECT prod_id,entity_name,division_name,product_name,maximum_quantity,prod_start,prod_due,prod_finished 
			FROM prod_header h
			LEFT OUTER JOIN ent_entities e ON h.entity_id=e.entity_id 
			LEFT OUTER JOIN ent_division_master d ON h.division_id=d.division_id
			LEFT OUTER JOIN item_master i ON h.resulting_product_id=i.product_id
			LEFT OUTER JOIN cx_addresses a ON e.primary_address=a.address_id";
		// Add $criteria
		if (!is_null($criteria) && is_array($criteria) && count($criteria)>0) {
			// The only key for Addresses is unified_search.
			if (is_array($criteria[0]) && count($criteria[0])>=2 && $criteria[0][0]=='unified_search') $criteria = $criteria[0][1];
			else $criteria='';
			$q .= " WHERE h.prod_id=? OR entity_name LIKE ? OR d.item_id = ? OR product_code LIKE ? OR product_description LIKE ? OR product_catalog_title LIKE ? OR gtin=? 
				OR city LIKE ? OR spc_abbrev = ? OR postal_code LIKE ?";
			// TODO: Add filter for finished production
			$q .= ' ORDER BY h.prod_id';
			$stmt = $this->dbconn->prepare($q);
			$stmt->bind_param('isisssssss',$p1,$p2,$p3,$p4,$p5,$p6,$p7,$p8,$p9,$p10);
			$p2 = $p4 = $p5 = $p6 = $p8 = $p10 = '%'.$criteria.'%';
			$p7 = $p9 = $criteria;
			$p1 = $p3 = ctype_digit($criteria)?$criteria:-99999;
			$result = $stmt->execute();
			if ($result !== false) {
				$this->recordSet = array();
				if (isset($_SESSION['recordSet']['Production'])) unset($_SESSION['recordSet']['Production']); // A search criteria was given, so do not display the last search on an empty set.
				$stmt->store_result();
				$entityname='';
				$divisionname = '';
				$productname = '';
				$stmt->bind_result($this->prod_id,$entityname,$divisionname,$productname,$this->prod_start,$this->prod_due,$this->prod_finished);
				while ($stmt->fetch()) {
					$this->recordSet[$this->prod_id] = array('Entity'=>$entityname,'Division'=>$divisionname,'Resulting Product'=>$productname,'Start date'=>$this->prod_start,
						'Due date'=>$this->prod_due,'Finished date'=>$this->prod_finished);
				}
			}
		// if criteria exists
		} else {
			$q .= " ORDER BY h.prod_id";
			$result = $this->dbconn->query($q);
			if ($result!==false) {
				$this->recordSet = array();
				while ($row=$result->fetch_assoc()) {
					$this->recordSet[$row['prod_id']] = array('Entity'=>$row['entity_name'],'Division'=>$row['divsion_name'],'Resulting Product'=>$row['product_name'],
						'Start date'=>$row['prod_start'],'Due date'=>$row['prod_due'],'Finished date'=>$row['prod_finished']);
				} // while rows
			} // if query succeeded
		} // if criteria does not exist
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
		$this->resetDetail();
		$this->entity_id = isset($_POST['entity_id'])?$_POST['entity_id']:null;
		$this->division_id = isset($_POST['division_id'])?$_POST['divsion_id']:null;
		$this->department_id = isset($_POST['department_id'])?$_POST['department_id']:null;
		$this->resulting_product_id = isset($_POST['resulting_product_id'])?$_POST['resulting_product_id']:null;
		$this->maximum_quantity = isset($_POST['maximum_quantity'])?$_POST['maximum_quantity']:0.00;
		$start_date = isset($_POST['prod_start_date'])?$_POST['prod_start_date']:null;
		$start_time = isset($_POST['prod_start_time'])?$_POST['prod_start_time']:null;
		$due_date = isset($_POST['prod_due_date'])?$_POST['prod_due_date']:null;
		$due_time = isset($_POST['prod_due_time'])?$_POST['prod_due_time']:null;
		$finished_date = isset($_POST['prod_finished_date'])?$_POST['prod_finished_date']:null;
		$finished_time = isset($_POST['prod_finished_time'])?$_POST['prod_finished_time']:null;
		$this->bom_id = isset($_POST['bom_id'])?$_POST['bom_id']:null;
		$this->rev_enabled = isset($_POST['rev_enabled'])?$_POST['rev_enabled']:false;
		$this->rev_number = isset($_POST['rev_number'])?$_POST['rev_number']:1;
		$this->prod_start = !empty($start_date)?new DateTime($start_date.' '.$start_time):null;
		$this->prod_due = !empty($due_date)?new DateTime($due_date.' '.$due_time):null;
		$this->prod_finished = !empty($finished_date)?new DateTime($finished_date.' '.$finished_time):null;
		$this->creation_date = new DateTime();
		$this->last_update_date = new DateTime();
		
		if (empty($this->entity_id)) {
			echo 'fail|All production must be assigned to an entity';
			return;
		}
		if (emtpy($this->bom_id)) {
			echo 'fail|Production is the application of a Bill of Materials to inventory.  Please select one before saving.';
			return;
		}
		$bom = new BOM($this->dbconn);
		$bom->display($this->bom_id,'update');
		$bomh = $bom->arrayifyHeader();
		if ($bomh['resulting_product_id']!=$this->resulting_product_id) {
			echo 'fail|The resulting product of the selected BOM must match the resulting product of production.';
			unset($bomh);
			unset($bom);
			return;
		}
		if ($this->maximum_quantity==0) $this->maximum_quantity = $bomh['resulting_quantity'];
		
		$q = "INSERT INTO prod_header (entity_id,division_id,department_id,resulting_product_id,maximum_quantity,prod_start,prod_due,prod_finished,
			bom_id,rev_enabled,rev_number,created_by,creation_date,last_update_by,last_update_date) VALUES 
			(?,?,?,?,?,?,?,?,?,?,?,?,NOW(),?,NOW());";
		$stmt = $this->dbconn->prepare($q);
		$stmt->bind_param('iiiidsssisiii',$p3,$p4,$p5,$p6,$p7,$p8,$p9,$p10,$p11,$p12,$p13,$p14,$p16);
		$p3 = $this->entity_id;
		$p4 = $this->division_id;
		$p5 = $this->department_id;
		$p6 = $this->resulting_product_id;
		$p7 = $this->maximum_quantity;
		$p8 = $this->prod_start;
		$p9 = $this->prod_due;
		$p10 = $this->prod_finished;
		$p11 = $this->bom_id;
		$p12 = ($this->rev_enabled=='true')?'Y':'N';
		if ($this->rev_number<1) $rev_number = 1;
		$p13 = $rev_number;
		$this->created_by = $p14 = $_SESSION['dbuserid'];
		$this->last_update_by = $p16 = $_SESSION['dbuserid'];
		$result = $stmt->execute();
		if ($result!==false) {
			$this->prod_id = $this->dbconn->insert_id;
			echo 'inserted|'.$this->prod_id."\r\n";
			$multiplier = floor($this->maximum_quantity/$bomh['resulting_quantity']);
			$this->step_timer = $this->prod_start;
			$bomd = $bom->getDetailArray();
			$stmt->close();
			foreach ($bomd as $bomdid=>$bomstep) {
				$this->insertDetail($bomdid,$bomstep,$multiplier);
			}
			// Update final step to set item_generated_id.
			$this->display($this->prod_id);
		} else {
			$stmt->close();
			echo 'fail|'.$this->dbconn->error;
			$this->mb->addError($this->dbconn->error);
		}
	} // insertHeader()
	private function insertDetail($bomdid=null,$bomstep=null,$multiplier=1) {
		if (!($this->prod_id>=1) || is_null($bomstep) || !is_array($bomstep)) {
			echo 'fail|Cannot create detail lines without a valid prod_id or bom.';
			return;
		}
		$this->resetDetail();
		$this->prod_step_number = $this->step_counter;
		$this->detail_rev_enabled = $this->rev_enabled;
		$this->detail_rev_number = $this->rev_number;
		$this->detail_created_by = $this->created_by;
		$this->detail_creation_date = $this->creation_date;
		$this->detail_last_update_by = $this->last_update_by;
		$this->detail_last_update_date = $this->last_update_date;
		if ($bomstep['step_type']=='C') {
			$this->bom_detail_id = $bomdid;
			$this->item_consumed_id = $bomstep['component_product_id'];
			$this->planned_consumed = $bomstep['component_quantity_used'] * $multiplier;
			if (!is_null($prod_finished)) {
				$this->step_finished = $this->prod_finished;
				$this->quantity_consumed = $this->planned_consumed;
			} else {
				$this->quantity_consumed = 0.00;
			}
			if (!is_null($prod_due)) $this->step_due = $this->prod_due;
			if (!is_null($prod_start)) $this->step_start = $this->step_timer->format('Y-m-d H:i:s');
			$q = 'INSERT INTO prod_detail (prod_step_number,bom_detail_id,item_consumed_id,step_started,step_due,step_finished,planned_consumed,quantity_consumed,
				rev_enabled,rev_number,created_by,creation_date,last_update_by,last_update_date) VALUES (?,?,?,?,?,?,?,?,?,?,?,NOW(),?,NOW())';
			$stmt = $this->dbconn->prepare($q);
			if ($stmt===false) {
				echo 'fail|'.$this->dbconn->error;
				return;
			} 
			$stmt->bind_param('iiisssddsiii',$p1,$p2,$p3,$p4,$p5,$p6,$p7,$p8,$p9,$p10,$p11,$p12);
			$p1 = $this->prod_step_number;
			$p2 = $this->bom_detail_id;
			$p3 = $this->item_consumed_id;
			$p4 = !empty($this->step_started)?$this->step_started->format('Y-m-d H:i:s'):null;
			$p5 = !empty($this->step_due)?$this->step_due->format('Y-m-d H:i:s'):null;
			$p6 = !empty($this->step_finished)?$this->step_finished->format('Y-m-d H:i:s'):null;
			$p7 = $this->planned_consumed;
			$p8 = $this->quantity_consumed;
			$p9 = ($this->detail_rev_enabled=='true')?'Y':'N';
			$p10 = $this->detail_rev_number;
			$p11 = $this->detail_created_by;
			$p12 = $this->detail_last_update_by;
			$stmt->execute();
			$this->step_counter++;
			$stmt->close();
			return;
		} elseif ($bomstep['step_type']=='P') {
			$this->bom_detail_id = $bomdid;
			if (!is_null($prod_finished)) $this->step_finished = $this->prod_finished;
			if (!is_null($prod_due)) $this->step_due = $this->prod_due;
			if (!is_null($prod_start)) {
				$this->step_timer = $this->step_timer->add(new DateInterval('P'.$bomstep['seconds_to_process'].'S'));
				$this->step_start = $this->step_timer->format('Y-m-d H:i:s');
			}
			$q = 'INSERT INTO prod_detail (prod_step_number,bom_detail_id,step_started,step_due,step_finished,
				rev_enabled,rev_number,created_by,creation_date,last_update_by,last_update_date) VALUES (?,?,?,?,?,?,?,?,NOW(),?,NOW())';
			$stmt = $this->dbconn->prepare($q);
			if ($stmt===false) {
				echo 'fail|'.$this->dbconn->error;
				return;
			} 
			$stmt->bind_param('iissssiii',$p1,$p2,$p4,$p5,$p6,$p9,$p10,$p11,$p12);
			$p1 = $this->prod_step_number;
			$p2 = $this->bom_detail_id;
			$p4 = !empty($this->step_started)?$this->step_started->format('Y-m-d H:i:s'):null;
			$p5 = !empty($this->step_due)?$this->step_due->format('Y-m-d H:i:s'):null;
			$p6 = !empty($this->step_finished)?$this->step_finished->format('Y-m-d H:i:s'):null;
			$p9 = ($this->detail_rev_enabled=='true')?'Y':'N';
			$p10 = $this->detail_rev_number;
			$p11 = $this->detail_created_by;
			$p12 = $this->detail_last_update_by;
			$stmt->execute();
			$this->step_counter++;
			$stmt->close();
			return;
		} elseif ($bomstep['step_type']=='B') {
			$subbom = new BOM($this->dbconn);
			$subbom->display($bomstep['sub_bom_id'],'update');
			$subbomd = $subbom->arrayifyDetail();
			foreach ($subbomd as $subbomdid=>$subbomstep) {
				$this->insertDetail($subbomdid,$subbomstep,$multiplier);
			}
			// Update final step to set item_generated_id.	
			$lastid = $this->dbconn->insert_id;
		}
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