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
		$this->entryFields[] = array('prod_detail','','Production Detail','fieldtable');
		$this->entryFields[] = array('prod_detail','prod_detail_id','ID','integerid');
		$this->entryFields[] = array('prod_detail','prod_step_number','Step #','integer');
		$this->entryFields[] = array('prod_detail','bom_detail_id','BOM Step','integerid');
		$this->entryFields[] = array('prod_detail','item_consumed_id','Item consumed','embedded');
		$this->entryFields[] = array('prod_detail','item_consumed_id','Item consumed','Item');
		$this->entryFields[] = array('prod_detail','','','endembedded');
		$this->entryFields[] = array('prod_detail','item_generated_id','Item generated','embedded');
		$this->entryFields[] = array('prod_detail','item_generated_id','Item generated','Item');
		$this->entryFields[] = array('prod_detail','','','endembedded');
		$this->entryFields[] = array('prod_detail','step_started','Step Started Date','datetime');
		$this->entryFields[] = array('prod_detail','step_due','Step Due Date','datetime');
		$this->entryFields[] = array('prod_detail','step_finished','Step Finished Date','datetime');
		$this->entryFields[] = array('prod_detail','step_cost','Cost','decimal',11,3);
		$this->entryFields[] = array('prod_detail','currency_code','Currency','dropdown','aa_currency',
			array('code','code'),isset($_SESSION['Options']['DEFAULT_CURRENCY_CODE'])?$_SESSION['Options']['DEFAULT_CURRENCY_CODE']:'USD');
		$this->entryFields[] = array('prod_detail','planned_consumed','Planned Consumed','decimal',24,5);
		$this->entryFields[] = array('prod_detail','planned_generated','Planned Generated','decimal',24,5);
		$this->entryFields[] = array('prod_detail','quantity_consumed','Qty Consumed','decimal',24,5);
		$this->entryFields[] = array('prod_detail','quantity_generated','Qty Generated','decimal',24,5);
		$this->entryFields[] = array('prod_detail','rev_enabled','Enable Revision Tracking','checkbox','rev_number');
		$this->entryFields[] = array('prod_detail','rev_number','Revision number','integer');
		$this->entryFields[] = array('prod_detail','','Save Changes','newlinebutton','saveProductionDetail();');
		$this->entryFields[] = array('prod_detail','','','endfieldtable');
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
		return array(
			'prod_id'=>$this->prod_id,
			'entity_id'=>$this->entity_id,
			'division_id'=>$this->division_id,
			'department_id'=>$this->department_id,
			'resulting_product_id'=>$this->resulting_product_id,
			'maximum_quantity'=>$this->maximum_quantity,
			'prod_start'=>$this->prod_start,
			'prod_due'=>$this->prod_due,
			'prod_finished'=>$this->prod_finished,
			'bom_id'=>$this->bom_id,
			'rev_enabled'=>$this->rev_enabled,
			'rev_number'=>$this->rev_number,
			'created_by'=>$this->created_by,
			'creation_date'=>$this->creation_date,
			'last_update_by'=>$this->last_update_by,
			'last_update_date'=>$this->last_update_date		
		);
	} // arrayifyHeader()
	public function arrayifyDetail() {
		return array(
			'prod_detail_id'=>$this->prod_detail_id,
			'prod_step_number'=>$this->prod_step_number,
			'bom_detail_id'=>$this->bom_detail_id,
			'item_consumed_id'=>$this->item_consumed_id,
			'item_generated_id'=>$this->item_generated_id,
			'step_started'=>$this->step_started,
			'step_due'=>$this->step_due,
			'step_finished'=>$this->step_finished,
			'step_cost'=>$this->step_cost,
			'currency_code'=>$this->currency_code,
			'planned_consumed'=>$this->planned_consumed,
			'planned_generated'=>$this->planned_generated,
			'quantity_consumed'=>$this->quantity_consumed,
			'quantity_generated'=>$this->quantity_generated,
			'rev_enabled'=>$this->detail_rev_enabled,
			'rev_number'=>$this->detail_rev_number,
			'created_by'=>$this->detail_created_by,
			'creation_date'=>$this->detail_creation_date,
			'last_update_by'=>$this->detail_last_update_by,
			'last_update_date'=>$this->detail_last_update_date
		);
	} // arrayifyDetail()
	private function unarrayifyDetail($index) {
		if (!is_array($this->detail_array)) return false;
		if (!isset($this->detail_array[$index])) return false;
		$rec = $this->detail_array[$index];
		// TODO
	}
	public function listRecords() {
		parent::abstractListRecords('Production');
	} // listRecords()
	public function searchPage() {
		parent::abstractSearchPage('ProductionSearch');
	} // searchPage()
	public function executeSearch($criteria) {
		$result = null;
		$q = "SELECT h.prod_id,entity_name,division_name,i.product_description,maximum_quantity,prod_start,prod_due,prod_finished 
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
			$q .= " WHERE h.prod_id=? OR entity_name LIKE ? OR h.resulting_product_id = ? OR product_code LIKE ? OR product_description LIKE ? OR product_catalog_title LIKE ? OR gtin=? 
				OR city LIKE ? OR spc_abbrev = ? OR postal_code LIKE ?";
			// TODO: Add filter for finished production
			$q .= ' ORDER BY h.prod_id';
			$stmt = $this->dbconn->prepare($q);
			if ($stmt===false) {
				echo 'fail|'.$this->dbconn->error;
				return;
			}
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
				$stmt->bind_result($this->prod_id,$entityname,$divisionname,$productname,$maxqty,$this->prod_start,$this->prod_due,$this->prod_finished);
				while ($stmt->fetch()) {
					$this->recordSet[$this->prod_id] = array('Entity'=>$entityname,'Division'=>$divisionname,'Resulting Product'=>$productname,'Maximum quantity'=>$maxqty,
						'Start date'=>$this->prod_start,'Due date'=>$this->prod_due,'Finished date'=>$this->prod_finished);
				}
			}
		// if criteria exists
		} else {
			$q .= " ORDER BY h.prod_id";
			$result = $this->dbconn->query($q);
			if ($result!==false) {
				$this->recordSet = array();
				while ($row=$result->fetch_assoc()) {
					$this->recordSet[$row['prod_id']] = array('Entity'=>$row['entity_name'],'Division'=>$row['division_name'],'Resulting Product'=>$row['product_description'],
						'Maximum quantity'=>$row['maximum_quantity'],'Start date'=>$row['prod_start'],'Due date'=>$row['prod_due'],'Finished date'=>$row['prod_finished']);
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
	public function display($id,$mode='view') {
		if (!$this->isIDValid($id)) return;
		$readonly = true;
		$html = '';
		$q = "SELECT prod_id,entity_id,division_id,department_id,resulting_product_id,maximum_quantity,prod_start,prod_due,prod_finished,
			bom_id,rev_enabled,rev_number,created_by,creation_date,last_update_by,last_update_date
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
			$stmt->store_result();
			$stmt->bind_result(
				$this->prod_id
				,$this->entity_id
				,$this->division_id
				,$this->department_id
				,$this->resulting_product_id
				,$this->maximum_quantity
				,$this->prod_start
				,$this->prod_due
				,$this->prod_finished
				,$this->bom_id
				,$this->rev_enabled
				,$this->rev_number
				,$this->created_by
				,$this->creation_date
				,$this->last_update_by
				,$this->last_update_date
			);
			$stmt->fetch();
			$this->currentRecord = $id;
			$stmt->close();		
			$q = 'SELECT prod_detail_id,prod_id,prod_step_number,bom_detail_id,item_consumed_id,item_generated_id,step_started,
				step_due,step_finished,step_cost,currency_code,planned_consumed,planned_generated,quantity_consumed,quantity_generated,
				rev_enabled,rev_number,created_by,creation_date,last_update_by,last_update_date
				FROM prod_detail
				WHERE prod_id=?';
			$stmt = $this->dbconn->prepare($q);
			if ($stmt===false) {
				echo $this->dbconn->error;
				return;
			}
			$stmt->bind_param('i',$prodid);
			$prodid = $this->prod_id;
			$dresult = $stmt->execute();
			if ($dresult!==false) {
				$stmt->store_result();
				$stmt->bind_result(
					$this->prod_detail_id
					,$this->prod_id
					,$this->prod_step_number
					,$this->bom_detail_id
					,$this->item_consumed_id
					,$this->item_generated_id
					,$this->step_started
					,$this->step_due
					,$this->step_finished
					,$this->step_cost
					,$this->currency_code
					,$this->planned_consumed
					,$this->planned_generated
					,$this->quantity_consumed
					,$this->quantity_generated
					,$this->detail_rev_enabled
					,$this->detail_rev_number
					,$this->detail_created_by
					,$this->detail_creation_date
					,$this->detail_last_update_by
					,$this->detail_last_update_date
				);
				while ($stmt->fetch()) {
					$this->detail_array[$this->prod_detail_id] = $this->arrayifyDetail();
				}
				$stmt->close();
			}
			if ($mode!='update') {
				$this->entryFieldsRemoveDetail();
				$this->entryFieldsAddDetail();
				$hdata = $this->arrayifyHeader();
				echo parent::abstractRecord($mode,'Production','',$hdata,$this->detail_array);
			}
		} // if result
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
		$this->entryFieldsRemoveDetail();
		echo parent::abstractNewRecord('Production');
		$_SESSION['currentScreen'] = 3008;
	} // newRecord()
	public function editRecord($id) {
		$this->entryFieldsRemoveDetail();
		$this->entryFieldsAddDetail();
		$this->display($id,'edit');
		$_SESSION['currentScreen'] = 4008;
		echo 
		'<SCRIPT type="text/javascript">
			$("#entity_id").prop("disabled",true);
			$("#division_id").prop("disabled",true);
			$("#department_id").prop("disabled",true);
			embeddedItemSelectReadonly("resulting_product_id",$("#resulting_product_id-div #resulting_product_id-product_id").text());
			$("#bom_id").prop("disabled",true);
			$("#prod_step_number").prop("readonly",true);
			$("#bom_detail_id").prop("readonly",true);
			$("#planned_consumed").prop("readonly",true);
			$("#planned_generated").prop("readonly",true);
			$("#item_consumed_id-div").html("<DIV id=\"item_consumed_id-product_id\">None selected</DIV>");
			$("#item_generated_id-div").html("<DIV id=\"item_generated_id-product_id\">None selected</DIV>");
		</SCRIPT>';
	}
	public function insertHeader($headless=false) {
		$return_status = '';
		$this->resetHeader();
		$this->resetDetail();
		$this->entity_id = !empty($_POST['entity_id'])?$_POST['entity_id']:null;
		$this->division_id = !empty($_POST['division_id'])?$_POST['division_id']:null;
		$this->department_id = !empty($_POST['department_id'])?$_POST['department_id']:null;
		$this->resulting_product_id = !empty($_POST['resulting_product_id'])?$_POST['resulting_product_id']:null;
		$this->maximum_quantity = isset($_POST['maximum_quantity'])?$_POST['maximum_quantity']:0.00;
		$start_date = isset($_POST['prod_start_date'])?$_POST['prod_start_date']:null;
		$start_time = isset($_POST['prod_start_time'])?$_POST['prod_start_time']:null;
		$due_date = isset($_POST['prod_due_date'])?$_POST['prod_due_date']:null;
		$due_time = isset($_POST['prod_due_time'])?$_POST['prod_due_time']:null;
		$finished_date = isset($_POST['prod_finished_date'])?$_POST['prod_finished_date']:null;
		$finished_time = isset($_POST['prod_finished_time'])?$_POST['prod_finished_time']:null;
		$this->bom_id = !empty($_POST['bom_id'])?$_POST['bom_id']:null;
		$this->rev_enabled = isset($_POST['rev_enabled'])?$_POST['rev_enabled']:false;
		$this->rev_number = isset($_POST['rev_number'])?$_POST['rev_number']:1;
		$this->prod_start = !empty($start_date)?new DateTime($start_date.' '.$start_time):null;
		$this->prod_due = !empty($due_date)?new DateTime($due_date.' '.$due_time):null;
		$this->prod_finished = !empty($finished_date)?new DateTime($finished_date.' '.$finished_time):null;
		$this->creation_date = new DateTime();
		$this->last_update_date = new DateTime();
		
		if (!isset($_SESSION['Options']) || !isset($_SESSION['Options']['DEFAULT_CURRENCY_CODE'])) {
			return 'fail|Please set the default currency code in the Options module.';
		}
		if (empty($this->entity_id)) {
			return 'fail|All production must be assigned to an entity';
		}
		if (empty($this->bom_id)) {
			return 'fail|Production is the application of a Bill of Materials to inventory.  Please select one before saving.';
		}
		if (isset($_SESSION['searchResults']) && isset($_SESSION['searchResults']['BOM'])) unset($_SESSION['searchResults']['BOM']);
		$bom = new BOM($this->dbconn);
		$bom->display($this->bom_id,'update');
		$bomh = $bom->arrayifyHeader();
		if ($bomh['resulting_product_id']!=$this->resulting_product_id) {
			unset($bomh);
			unset($bom);
			return 'fail|The resulting product of the selected BOM must match the resulting product of production.';
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
		$p8 = !empty($this->prod_start)?$this->prod_start->format('Y-m-d H:i:s'):null;
		$p9 = !empty($this->prod_due)?$this->prod_due->format('Y-m-d H:i:s'):null;
		$p10 = !empty($this->prod_finished)?$this->prod_finished->format('Y-m-d H:i:s'):null;
		$p11 = $this->bom_id;
		$p12 = ($this->rev_enabled=='true')?'Y':'N';
		if ($this->rev_number<1) $this->rev_number = 1;
		$p13 = $this->rev_number;
		$this->created_by = $p14 = $_SESSION['dbuserid'];
		$this->last_update_by = $p16 = $_SESSION['dbuserid'];
		$result = $stmt->execute();
		if ($result!==false) {
			$this->prod_id = $this->dbconn->insert_id;
			$return_status .= 'inserted|'.$this->prod_id."\r\n";
			// Update inventory WIP
			$inv = new InventoryManager($this->dbconn);
			$inv->productionUpdateWIP($this->prod_id,$this->entity_id,$this->resulting_product_id,$this->maximum_quantity);
			// Create detail records
			$multiplier = floor($this->maximum_quantity/$bomh['resulting_quantity']);
			$this->step_timer = $this->prod_start;
			$bomd = $bom->getDetailArray();
			$stmt->close();
			$update_done = null;
			foreach ($bomd as $bomdid=>$bomstep) {
				$this->insertDetail($bomdid,$bomstep,$multiplier);
				// Sub-BOMs will set the generated fields at the end.
				if ($bomstep['step_type']=='B') $update_done = true;
				else $update_done = false;
			}
			// Update final step to set item_generated_id.
			$lastid = $this->dbconn->insert_id;
			if (!$update_done) {
				$lq = 'UPDATE prod_detail SET item_generated_id=?,planned_generated=? WHERE prod_detail_id=?';
				$sq = $this->dbconn->prepare($lq);
				$sq->bind_param('idi',$l1,$l2,$l3);
				$this->item_generated_id = $l1 = $bomh['resulting_product_id'];
				$this->planned_generated = $l2 = $bomh['resulting_quantity']*$multiplier;
				$l3 = $lastid;
				$sq->execute();
			}
			if (!isset($_SESSION['searchResults'])) $_SESSION['searchResults'] = array();
			if (!isset($_SESSION['searchResults']['Production'])) $_SESSION['searchResults']['Production'] = array();
			$_SESSION['searchResults']['Production'][] = $this->prod_id;		
			if (!$headless) {
				echo $return_status;
				$return_status = '';
				$this->display($this->prod_id);
			}
		} else {
			$return_status .= 'fail|'.$this->dbconn->errno.': '.$this->dbconn->error;
			$this->mb->addError($this->dbconn->error);
			$stmt->close();
		}
		return $return_status;
	} // insertHeader()
	public function insertDetail($bomdid=null,$bomstep=null,$multiplier=1) {
		$output = "";
		if (!($this->prod_id>=1) || is_null($bomstep) || !is_array($bomstep)) {
			return 'fail|Cannot create detail lines without a valid prod_id or bom.';
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
			if (!is_null($this->prod_finished)) {
				$this->step_finished = $this->prod_finished;
				$this->quantity_consumed = $this->planned_consumed;
			} else {
				$this->quantity_consumed = 0.00;
			}
			if (!is_null($this->prod_due)) $this->step_due = $this->prod_due;
			if (!is_null($this->prod_start)) $this->step_started = $this->step_timer;
			$q = 'INSERT INTO prod_detail (prod_id,prod_step_number,bom_detail_id,item_consumed_id,step_started,
				step_due,step_finished,planned_consumed,quantity_consumed,currency_code,
				rev_enabled,rev_number,created_by,creation_date,last_update_by,last_update_date) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,NOW(),?,NOW())';
			$stmt = $this->dbconn->prepare($q);
			if ($stmt===false) {
				return 'fail|'.$this->dbconn->error;
			} 
			$stmt->bind_param('iiiisssddssiii',$h1,$p1,$p2,$p3,$p4,$p5,$p6,$p7,$p8,$cc,$p9,$p10,$p11,$p12);
			$h1 = $this->prod_id;
			$p1 = $this->prod_step_number;
			$p2 = $this->bom_detail_id;
			$p3 = $this->item_consumed_id;
			$p4 = !empty($this->step_started)?$this->step_started->format('Y-m-d H:i:s'):null;
			$p5 = !empty($this->step_due)?$this->step_due->format('Y-m-d H:i:s'):null;
			$p6 = !empty($this->step_finished)?$this->step_finished->format('Y-m-d H:i:s'):null;
			$p7 = $this->planned_consumed;
			$p8 = $this->quantity_consumed;
			$this->currency_code = $cc = $_SESSION['Options']['DEFAULT_CURRENCY_CODE'];
			$p9 = ($this->detail_rev_enabled=='true')?'Y':'N';
			$p10 = $this->detail_rev_number;
			$p11 = $this->detail_created_by;
			$p12 = $this->detail_last_update_by;
			if (!$stmt->execute()) $output .= 'fail|Detail - '.$this->dbconn->error.'|';
			$this->step_counter++;
			$stmt->close();
			return;
		} elseif ($bomstep['step_type']=='P') {
			$this->bom_detail_id = $bomdid;
			if (!is_null($this->prod_finished)) $this->step_finished = $this->prod_finished;
			if (!is_null($this->prod_due)) $this->step_due = $this->prod_due;
			if (!is_null($this->prod_start)) {
				$this->step_timer = $this->step_timer->add(new DateInterval('PT'.(int)$bomstep['seconds_to_process'].'S'));
				$this->step_started = $this->step_timer;
			}
			$q = 'INSERT INTO prod_detail (prod_id,prod_step_number,bom_detail_id,step_started,step_due,step_finished,currency_code,
				rev_enabled,rev_number,created_by,creation_date,last_update_by,last_update_date) VALUES (?,?,?,?,?,?,?,?,?,?,NOW(),?,NOW())';
			$stmt = $this->dbconn->prepare($q);
			if ($stmt===false) {
				return 'fail|'.$this->dbconn->error;
			} 
			$stmt->bind_param('iiisssssiii',$h1,$p1,$p2,$p4,$p5,$p6,$cc,$p9,$p10,$p11,$p12);
			$h1 = $this->prod_id;
			$p1 = $this->prod_step_number;
			$p2 = $this->bom_detail_id;
			$p4 = !empty($this->step_started)?$this->step_started->format('Y-m-d H:i:s'):null;
			$p5 = !empty($this->step_due)?$this->step_due->format('Y-m-d H:i:s'):null;
			$p6 = !empty($this->step_finished)?$this->step_finished->format('Y-m-d H:i:s'):null;
			$this->currency_code = $cc = $_SESSION['Options']['DEFAULT_CURRENCY_CODE'];
			$p9 = ($this->detail_rev_enabled=='true')?'Y':'N';
			$p10 = $this->detail_rev_number;
			$p11 = $this->detail_created_by;
			$p12 = $this->detail_last_update_by;
			if (!$stmt->execute()) $output .= 'fail|Detail - '.$this->dbconn->error.'|';
			$this->step_counter++;
			$stmt->close();
			return;
		} elseif ($bomstep['step_type']=='B') {
			// Use a recursive algorithm.
			$subbom = new BOM($this->dbconn);
			$subbom->display($bomstep['sub_bom_id'],'update');
			$subbomh = $subbom->arrayifyHeader();
			$subbomd = $subbom->getDetailArray();
			$update_done = null;
			foreach ($subbomd as $subbomdid=>$subbomstep) {
				$this->insertDetail($subbomdid,$subbomstep,$multiplier);
				if ($subbomstep['step_type']=='B') $update_done = true;
				else $update_done = false;
			}
			// Update final step to set item_generated_id.	
			$lastid = $this->dbconn->insert_id;
			if (!$update_done) {
				$lq = 'UPDATE prod_detail SET item_generated_id=?,planned_generated=? WHERE prod_detail_id=?';
				$sq = $this->dbconn->prepare($lq);
				$sq->bind_param('idi',$l1,$l2,$l3);
				$this->item_generated_id = $l1 = $subbomh['resulting_product_id'];
				$this->planned_generated = $l2 = $subbomh['resulting_quantity']*$multiplier;
				$l3 = $lastid;
				$sq->execute();
			}
		} // if step_type
	} // insertDetail()
	public function updateHeader() {
		$output = "";
		$this->resetHeader();
		$this->resetDetail();
		$now = new DateTime();
		$id = $_POST['prod_id'];
		if ((!is_integer($id) && !ctype_digit($id)) || $id<1) {
			return 'fail|Invalid Production id for updating';
		}
		$this->display($id,'update'); // Display already has the logic for loading the record.  TODO: Refactor into separate function.
		if (is_null($this->prod_id)) {
			return 'fail|Invalid Production id for updating';
		}
		$update = array();
		if (isset($_POST['maximum_quantity']) && $_POST['maximum_quantity']!=$this->maximum_quantity) $update['maximum_quantity'] = array('d',$_POST['maximum_quantity']);
		if (!empty($_POST['prod_start_date']) && !is_null($_POST['prod_start_time'])) {
			$start = new DateTime($_POST['prod_start_date'].' '.$_POST['prod_start_time']);
			if ($start->format('Y-m-d H:i:s')!=$this->prod_start) $update['prod_start'] = array('s',$start->format('Y-m-d H:i:s'));
		}
		if (!empty($_POST['prod_due_date']) && !is_null($_POST['prod_due_time'])) {
			$due = new DateTime($_POST['prod_due_date'].' '.$_POST['prod_due_time']);
			if ($due->format('Y-m-d H:i:s')!=$this->prod_due) $update['prod_due'] = array('s',$due->format('Y-m-d H:i:s'));
		}
		if (!empty($_POST['prod_finished_date']) && !is_null($_POST['prod_finished_time'])) {
			$finished = new DateTime($_POST['prod_finished_date'].' '.$_POST['prod_finished_time']);
			if ($finished->format('Y-m-d H:i:s')!=$this->prod_finished) $update['prod_finished'] = array('s',$finished->format('Y-m-d H:i:s'));
		}
		$reven = null;
		if (isset($_POST['rev_enabled'])) $reven = ($_POST['rev_enabled']=='true')?'Y':'N';
		if (!is_null($reven) && $reven!=$this->rev_enabled) $update['rev_enabled'] = array('s',$reven);
		if ((!is_null($reven)) && $reven=='Y' && isset($_POST['rev_number']) && $_POST['rev_number']!=$this->rev_number) $update['rev_number'] = array('i',$_POST['rev_number']);
		$update['last_update_date'] = array('s',$now->format('Y-m-d H:i:s'));
		$update['last_update_by'] = array('i',$_SESSION['dbuserid']);
		
		// Create UPDATE String
		
		if (count($update)==2) { // last_update is always there
			return 'fail|Nothing to update';
		}
		$q = 'UPDATE prod_header SET ';
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
		$q .= ' WHERE prod_id=?';
		$ctr++;
		$bp_types .= 'i';
		$bp_values[$ctr] = $this->prod_id;
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
			$output .= 'updated';
			// Update inventory WIP
			$inv = new InventoryManager($this->dbconn);
			if (isset($update['maximum_quantity'])) {
				$inv->productionUpdateWIP($this->prod_id,$this->entity_id,$this->resulting_product_id,($update['maximum_quantity'][1])-($this->maximum_quantity));			
			}
			if (isset($update['prod_finished']) && is_null($this->prod_finished)) {
				$inv->productionUpdateWIP($this->prod_id,$this->entity_id,$this->resulting_product_id,-1*($this->maximum_quantity));			
			}
		} else {
			if ($this->dbconn->error) {
				$output .= 'fail|'.$this->dbconn->error;
				$this->mb->addError($this->dbconn->error);
			} else $output .= 'fail|No rows updated';
		}
		$stmt->close();
		return $output;
	} // updateHeader()
	public function updateDetail() {
		$output = "";
		$this->resetDetail();
		$now = new DateTime();
		$id = $_POST['prod_id'];
		$dtlid = $_POST['prod_detail_id'];
		if ((!is_integer($id) && !ctype_digit($id)) || $id<1) {
			return 'fail|Invalid Production id for updating';
		}
		if ((!is_integer($dtlid) && !ctype_digit($dtlid)) || $dtlid<1) {
			return 'fail|Invalid Production detail id for updating';
		}
		$this->display($id,'update'); // Display already has the logic for loading the header record.  TODO: Refactor into separate function.
		if (is_null($this->prod_id)) {
			return 'fail|Invalid Production id for updating';
		}
		$update = array();
		// Set existing fields from detail_array.
		$this->prod_detail_id = $dtlid;
		$this->prod_step_number = $this->detail_array[$dtlid]['prod_step_number'];
		$this->bom_detail_id = $this->detail_array[$dtlid]['bom_detail_id'];
		$this->item_consumed_id = $this->detail_array[$dtlid]['item_consumed_id'];
		$this->item_generated_id = $this->detail_array[$dtlid]['item_generated_id'];
		$this->step_started = new DateTime($this->detail_array[$dtlid]['step_started']);
		$this->step_due = new DateTime($this->detail_array[$dtlid]['step_due']);
		$this->step_finished = new DateTime($this->detail_array[$dtlid]['step_finished']);
		$this->step_cost = $this->detail_array[$dtlid]['step_cost'];
		$this->currency_code = $this->detail_array[$dtlid]['currency_code'];
		$this->planned_consumed = $this->detail_array[$dtlid]['planned_consumed'];
		$this->planned_generated = $this->detail_array[$dtlid]['planned_generated'];
		$this->quantity_consumed = $this->detail_array[$dtlid]['quantity_consumed'];
		$this->quantity_generated = $this->detail_array[$dtlid]['quantity_generated'];
		$this->detail_rev_enabled = $this->detail_array[$dtlid]['rev_enabled'];
		$this->detail_rev_number = $this->detail_array[$dtlid]['rev_number'];
		$this->detail_created_by = $this->detail_array[$dtlid]['created_by'];
		$this->detail_creation_date = $this->detail_array[$dtlid]['creation_date'];
		$this->detail_last_update_by = $this->detail_array[$dtlid]['last_update_by'];
		$this->detail_last_update_date = $this->detail_array[$dtlid]['last_update_date'];
		// Compare updatable fields
		if (!empty($_POST['step_started_date']) && !is_null($_POST['step_started_time'])) {
			$startdate = new DateTime($_POST['step_started_date'].' '.$_POST['step_started_time']);
			if (!empty($startdate) && $startdate->format('Y-m-d H:i:s')!=$this->step_started->format('Y-m-d H:i:s')) 
				$update['step_started'] = array('s',$startdate->format('Y-m-d H:i:s'));
		}
		if (!empty($_POST['step_due_date']) && !is_null($_POST['step_due_time'])) {
			$duedate = new DateTime($_POST['step_due_date'].' '.$_POST['step_due_time']);
			if (!empty($duedate) && $duedate->format('Y-m-d H:i:s')!=$this->step_due->format('Y-m-d H:i:s')) 
				$update['step_due'] = array('s',$duedate->format('Y-m-d H:i:s'));
		}
		if (!empty($_POST['step_finished_date']) && !is_null($_POST['step_finished_time'])) {
			$finisheddate = new DateTime($_POST['step_finished_date'].' '.$_POST['step_finished_time']);
			if (!empty($finisheddate) && $finisheddate->format('Y-m-d H:i:s')!=$this->step_finished->format('Y-m-d H:i:s')) 
				$update['step_finished'] = array('s',$finisheddate->format('Y-m-d H:i:s'));
		}
		if (isset($_POST['step_cost']) && $_POST['step_cost']!=$this->step_cost) $update['step_cost'] = array('d',$_POST['step_cost']);
		if (isset($_POST['currency_code']) && $_POST['currency_code']!=$this->currency_code) $update['currency_code'] = array('s',$_POST['currency_code']);
		if (isset($_POST['quantity_consumed']) && $_POST['quantity_consumed']!=$this->quantity_consumed) $update['quantity_consumed'] = array('d',$_POST['quantity_consumed']);
		if (isset($_POST['quantity_generated']) && $_POST['quantity_generated']!=$this->quantity_generated) $update['quantity_generated'] = array('d',$_POST['quantity_generated']);
		$reven = null;
		if (isset($_POST['rev_enabled'])) $reven = ($_POST['rev_enabled']=='true')?'Y':'N';
		if (!is_null($reven) && $reven!=$this->detail_rev_enabled) $update['rev_enabled'] = array('s',$reven);
		if ((!is_null($reven)) && $reven=='Y' && isset($_POST['rev_number']) && $_POST['rev_number']!=$this->detail_rev_number) $update['rev_number'] = array('i',$_POST['rev_number']);
		$update['last_update_date'] = array('s',$now->format('Y-m-d H:i:s'));
		$update['last_update_by'] = array('i',$_SESSION['dbuserid']);

		// Create UPDATE String
		
		if (count($update)==2) { // last_update is always there
			return 'fail|Nothing to update';
		}
		$q = 'UPDATE prod_detail SET ';
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
		$q .= ' WHERE prod_detail_id=?';
		$ctr++;
		$bp_types .= 'i';
		$bp_values[$ctr] = $dtlid;
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
			$output .= 'updated';
			// Update Inventory
			$inv = new InventoryManager($this->dbconn);
			if (isset($update['quantity_consumed'])) {
				$inv->productionConsume($dtlid,$this->entity_id,$this->item_consumed_id,($update['quantity_consumed'][1])-($this->quantity_consumed));
			}
			if (isset($update['quantity_generated'])) {
				$inv->productionGenerate($dtlid,$this->entity_id,$this->item_generated_id,($update['quantity_generated'][1])-($this->quantity_generated));
			}
		} else {
			if ($this->dbconn->error) {
				$output .= 'fail|'.$this->dbconn->error;
				$this->mb->addError($this->dbconn->error);
			} else $output .= 'fail|No rows updated';
		}
		$stmt->close();		
		return $output;
	} // updateDetail()
	public function insertRecord() {
		echo $this->insertHeader();
		// There can be no insertDetail called from the UI in this module.
	} // insertRecord()
	public function updateRecord() {
		// Assumes values are stored in $_POST
		if (isset($_POST['level']) && $_POST['level']=='header') echo $this->updateHeader();
		if (isset($_POST['level']) && $_POST['level']=='detail') echo $this->updateDetail();
	} // updateRecord()
	public function saveRecord() {
	
	} // saveRecord()
} // class _template
?>