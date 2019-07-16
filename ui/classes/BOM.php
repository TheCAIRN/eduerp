<?php
class BOM extends ERPBase {
	private $bom_id;
	private $resulting_product;
	private $resulting_product_code;
	private $resulting_product_description;
	private $resulting_product_qty;
	private $bom_description;
	private $rev_enabled;
	private $rev_number;
	private	$huser_creation;
	private $hdate_creation;
	private $huser_modify;
	private $hdate_modify;
	private $column_list_header = 'bom_id,resulting_product_id,resulting_quantity,description,rev_enabled,rev_number';
	
	private $bom_detail_id;
	private $step_number;
	private $step_type;
	private $component_product_id;
	private $component_quantity_used;
	private $bom_step_id;
	private $seconds_to_process;
	private $sub_bom_id;
	private $detail_description;
	private $drev_enabled;
	private $drev_number;
	private	$duser_creation;
	private $ddate_creation;
	private $duser_modify;
	private $ddate_modify;
	private $detail_array;
	private $column_list_detail = 'bom_id,bom_detail_id,step_number,step_type,component_product_id,
		component_quantity_used,bom_step_id,seconds_to_process,sub_bom_id,description,rev_enabled,rev_number';
	
	public function __construct($link=null) {
		parent::__construct($link);
		$this->searchFields[] = array('bom_header','bom_id','BOM ID','integer');
		$this->searchFields[] = array('item_master',array('product_id','product_code'),'Resulting Product ID','dropdown');
		
		$this->entryFields[] = array('bom_header','','Bill of Materials','fieldset');
		$this->entryFields[] = array('bom_header','bom_id','BOM ID','integerid');
		$this->entryFields[] = array('bom_header','resulting_product_id','Resulting Product','dropdown','item_master',array('product_id','product_code'));
		$this->entryFields[] = array('bom_header','resulting_quantity','Quantity','decimal',11,5);
		$this->entryFields[] = array('bom_header','description','Description','textarea');
		$this->entryFields[] = array('bom_header','rev_enabled','Enable Revision Tracking','checkbox','rev_number');
		$this->entryFields[] = array('bom_header','rev_number','Revision number','integer');
		$this->entryFields[] = array('bom_header','','','endfieldset');
		
		$this->entryFields[] = array('bom_detail','','BOM Detail','fieldtable');
		$this->entryFields[] = array('bom_detail','bom_detail_id','BOM Detail ID','integerid');
		$this->entryFields[] = array('bom_detail','step_number','Step #','integer');
		$this->entryFields[] = array('bom_detail','step_type','Step Type','dropdown',array(array('C','Component'),array('P','Process'),array('B','Sub-BOM')));
		$this->entryFields[] = array('bom_detail','component_product_id','Component Product','dropdown','item_master',array('product_id','product_code'));
		$this->entryFields[] = array('bom_detail','component_quantity_used','Quantity used','decimal',11,5);
		$this->entryFields[] = array('bom_detail','bom_step_id','Process Step','dropdown','bom_steps',array('bom_step_id','bom_step_name'));
		$this->entryFields[] = array('bom_detail','seconds_to_process','Time (s)','decimal',17,3);
		$this->entryFields[] = array('bom_detail','sub_bom_id','Sub BOM','dropdown','bom_header',array('bom_id','resulting_product_id'));
		$this->entryFields[] = array('bom_detail','description','Instructions','textarea');
		$this->entryFields[] = array('bom_detail','rev_enabled','Enable Revision Tracking','checkbox','rev_number');
		$this->entryFields[] = array('bom_detail','rev_number','Revision number','integer');
		$this->entryFields[] = array('bom_detail','','Next Step','newlinebutton','newBOMDetailRow();');
		$this->entryFields[] = array('bom_detail','','','endfieldtable');
	}
	public function resetHeader() {
		$this->bom_id = 0;
		$this->resulting_product = 0; // product_id
		$this->resulting_product_code = '';
		$this->resulting_product_description = '';
		$this->resulting_product_qty = 0.00;
		$this->bom_description = '';
		$this->rev_enabled = false;
		$this->rev_number = 1;
		$this->huser_creation = null;
		$this->hdate_creation = null;
		$this->huser_modify = null;
		$this->hdate_modify = null;
		$this->detail_array = array();
	}
	public function resetDetail() {
		$this->bom_detail_id = 0;
		$this->step_number = 0;
		$this->step_type = '';	// C = Component, P = Process, B = Sub-BOM
		$this->component_product_id = 0;
		$this->component_quantity_used = 0.00;
		$this->bom_step_id = 0;
		$this->seconds_to_process = 0;
		$this->sub_bom_id = 0;
		$this->detail_description = '';
		$this->drev_enabled = false;
		$this->drev_number = 1;
		$this->duser_creation = null;
		$this->ddate_creation = null;
		$this->duser_modify = null;
		$this->ddate_modify = null;
	}
	public function arrayifyHeader() {
		return array(
			'bom_id'=>$this->bom_id,
			'resulting_product_id'=>$this->resulting_product,
			'resulting_quantity'=>$this->resulting_product_qty,
			'description'=>$this->bom_description,
			'rev_enabled'=>$this->rev_enabled,
			'rev_number'=>$this->rev_number,
			'huser_creation'=>$this->huser_creation,
			'hdate_creation'=>$this->hdate_creation,
			'huser_modify'=>$this->huser_modify,
			'hdate_modify'=>$this->hdate_modify
		);
	} // arrayifyHeader()
	public function arrayifyDetail() {
		return array(
			'bom_detail_id'=>$this->bom_detail_id,
			'step_number'=>$this->step_number,
			'step_type'=>$this->step_type,
			'component_product_id'=>$this->component_product_id,
			'component_quantity_used'=>$this->component_quantity_used,
			'bom_step_id'=>$this->bom_step_id,
			'seconds_to_process'=>$this->seconds_to_process,
			'sub_bom_id'=>$this->sub_bom_id,
			'description'=>$this->detail_description,
			'rev_enabled'=>$this->drev_enabled,
			'rev_number'=>$this->drev_number,
			'duser_creation'=>$this->duser_creation,
			'ddate_creation'=>$this->ddate_creation,
			'duser_modify'=>$this->duser_modify,
			'ddate_modify'=>$this->ddate_modify
		);
	}
	public function listRecords() {
		parent::abstractListRecords('BOM');
	} // function listRecords()
	public function searchPage() {
		parent::abstractSearchPage('BOMSearch');
	} // function searchPage()
	public function executeSearch($criteria) {
		$this->resetHeader();
		$q = "SELECT bom_id,resulting_product_id,product_code,product_description,resulting_quantity,description 
			FROM bom_header bh JOIN item_master im ON bh.resulting_product_id=im.product_id";
		if (!is_null($criteria) && is_array($criteria) && count($criteria)>0) {
			if (isset($criteria['bom_id']) && isset($criteria['product_id'])) {
				$q .= ' WHERE bom_id=? OR product_id=? ORDER BY bom_id';
				$stmt = $this->dbconn->prepare($q);
				if ($stmt!==false) {
					$stmt->bind_param('ii',$p1,$p2);
					$p1 = $criteria['bom_id'];
					$p2 = $criteria['product_id'];
					$result = $stmt->execute();
					if ($result !== false) {
						$stmt->store_result();
						$stmt->bind_result($this->bom_id,$this->resulting_product_id,$this->product_code,$this->product_description,
							$this->resulting_quantity,$this->description);
						while ($stmt->fetch()) {
							$this->recordSet[$this->bom_id] = array('product'=>$this->resulting_product_id,'code'=>$this->product_code,
								'product_description'=>$this->product_description,'quantity'=>$this->resulting_quantity,'description'=>$this->description);
						}
					}
					
				} else echo $this->dbconn->error;
			}
		// if criteria exists
		} else {
			$q .= " ORDER BY bom_id";
			$result = $this->dbconn->query($q);
			if ($result!==false) {
				$this->recordSet = array();
				while ($row=$result->fetch_assoc()) {
					$this->recordSet[$row['bom_id']] = array('product'=>$row['resulting_product_id'],'code'=>$row['product_code'],
						'product_description'=>$row['product_description'],'quantity'=>$row['resulting_quantity'],'description'=>$row['description']);
				} // while rows
			} // if query succeeded
		} // if criteria does not exist
		$this->listRecords();
		$_SESSION['currentScreen'] = 1019;
		$_SESSION['lastCriteria'] = $criteria;
		if (!isset($_SESSION['searchResults'])) $_SESSION['searchResults'] = array();
		$_SESSION['searchResults']['BOM'] = array_keys($this->recordSet);
			
	} // function executeSearch()
	public function isIDValid($id) {
		// TODO: Validate that the ID is actually a record in the database
		if ($id<1) return false;
		if (is_integer($id)) return true;
		if (ctype_digit($id)) return true;
		return false;
	} // function isIDValid()
	public function display($id,$mode='view') {
		if (!($this->isIDValid($id))) return;
		$readonly = true;
		$html = '';
		$q = "SELECT {$this->column_list_header},h.created_by,h.creation_date,h.last_update_by,h.last_update_date FROM bom_header h WHERE bom_id=?;";
		$stmt = $this->dbconn->prepare($q);
		if ($stmt===false) {
			echo $this->dbconn->error;
			return;
		}
		$stmt->bind_param('i',$BOMid);
		$BOMid = $id;
		$result = $stmt->execute();
		// TODO: What if another user deletes the record while it's still in my search results?
		if ($result!==false) {
			$stmt->bind_result(
				$this->bom_id
				,$this->resulting_product
				,$this->resulting_product_qty
				,$this->bom_description
				,$this->rev_enabled
				,$this->rev_number
				,$this->huser_creation
				,$this->hdate_creation
				,$this->huser_modify
				,$this->hdate_modify
			);
			$stmt->store_result();
			$stmt->fetch();
			$stmt->close();		
			
			$q = "SELECT {$this->column_list_detail},d.created_by,d.creation_date,d.last_update_by,d.last_update_date 
				FROM bom_detail d 
				WHERE bom_id=?";
			$stmt = $this->dbconn->prepare($q);
			if ($stmt===false) {
				echo $this->dbconn->error;
				return;
			}
			$stmt->bind_param('i',$BOMid);
			$BOMid = $id;
			$dresult = $stmt->execute();
			if ($dresult!==false) {
				$stmt->bind_result(
					$this->bom_id
					,$this->bom_detail_id
					,$this->step_number
					,$this->step_type
					,$this->component_product_id
					,$this->component_quantity_used
					,$this->bom_step_id
					,$this->seconds_to_process
					,$this->sub_bom_id
					,$this->detail_description
					,$this->drev_enabled
					,$this->drev_number				
					,$this->duser_creation
					,$this->ddate_creation
					,$this->duser_modify
					,$this->ddate_modify
				);
				$stmt->store_result();
				while ($stmt->fetch()) {
					$this->detail_array[$this->bom_detail_id] = $this->arrayifyDetail();
				}
				$stmt->close();
			} // if detail result
			else echo $this->dbconn->error;
			if ($mode!='update') {
				$hdata = $this->arrayifyHeader();
				echo parent::abstractRecord($mode,'BOM','',$hdata,$this->detail_array);
			}
		} // if result
		else $this->bom_id = null;
		$_SESSION['currentScreen'] = 2019;
		if (!isset($_SESSION['searchResults']) && !isset($_SESSION['searchResults']['BOM']))
			$_SESSION['idarray'] = array(0,0,$id,0,0);
		else {
			$idloc = array_search($id,$_SESSION['searchResults']['BOM'],false);
			$f = $_SESSION['searchResults']['BOM'][0];
			$l = $_SESSION['searchResults']['BOM'][] = array_pop($_SESSION['searchResults']['BOM']); // https://stackoverflow.com/questions/3687358/whats-the-best-way-to-get-the-last-element-of-an-array-without-deleting-it#comment63556865_3687358
			if ($idloc > 0) $p = $_SESSION['searchResults']['BOM'][$idloc-1]; else $p = $f;
			if ($l != $id) $n = $_SESSION['searchResults']['BOM'][$idloc+1]; else $n = $l;
			$_SESSION['idarray'] = array($f,$p,$id,$n,$l);
		}		
	} // function display()
	public function newRecord() {
		echo parent::abstractNewRecord('BOM');
		$_SESSION['currentScreen'] = 3019;
		echo "<SCRIPT type=\"text/javascript\">
			$(\"#bom_detail_edit td:nth-child(4), #bom_detail_edit th:nth-child(4)\").hide();
			$(\"#bom_detail_edit td:nth-child(5), #bom_detail_edit th:nth-child(5)\").hide();
			$(\"#bom_detail_edit td:nth-child(6), #bom_detail_edit th:nth-child(6)\").hide();
			$(\"#bom_detail_edit td:nth-child(7), #bom_detail_edit th:nth-child(7)\").hide();
			$(\"#bom_detail_edit td:nth-child(8), #bom_detail_edit th:nth-child(8)\").hide();
			$(\"#step_type\").change(function() {
				var st = $(\"#step_type option:selected\").val();
				if (st=='C') {
					$(\"#bom_detail_edit td:nth-child(4), #bom_detail_edit th:nth-child(4)\").show();
					$(\"#bom_detail_edit td:nth-child(5), #bom_detail_edit th:nth-child(5)\").show();
					$(\"#bom_detail_edit td:nth-child(6), #bom_detail_edit th:nth-child(6)\").hide();
					$(\"#bom_detail_edit td:nth-child(7), #bom_detail_edit th:nth-child(7)\").hide();
					$(\"#bom_detail_edit td:nth-child(8), #bom_detail_edit th:nth-child(8)\").hide();
				} else if (st=='P') {
					$(\"#bom_detail_edit td:nth-child(4), #bom_detail_edit th:nth-child(4)\").hide();
					$(\"#bom_detail_edit td:nth-child(5), #bom_detail_edit th:nth-child(5)\").hide();
					$(\"#bom_detail_edit td:nth-child(6), #bom_detail_edit th:nth-child(6)\").show();
					$(\"#bom_detail_edit td:nth-child(7), #bom_detail_edit th:nth-child(7)\").show();
					$(\"#bom_detail_edit td:nth-child(8), #bom_detail_edit th:nth-child(8)\").hide();
				} else if (st=='B') {
					$(\"#bom_detail_edit td:nth-child(4), #bom_detail_edit th:nth-child(4)\").hide();
					$(\"#bom_detail_edit td:nth-child(5), #bom_detail_edit th:nth-child(5)\").hide();
					$(\"#bom_detail_edit td:nth-child(6), #bom_detail_edit th:nth-child(6)\").hide();
					$(\"#bom_detail_edit td:nth-child(7), #bom_detail_edit th:nth-child(7)\").hide();
					$(\"#bom_detail_edit td:nth-child(8), #bom_detail_edit th:nth-child(8)\").show();
				}
			});
			</SCRIPT>";
	} // function newRecord()
	public function editRecord($id) {
		$this->display($id,'edit');
		$_SESSION['currentScreen'] = 4019;
	}
	private function insertHeader() {
		$this->resetHeader();
		$this->resetDetail();
		$this->bom_id = isset($_POST['bomid'])?$_POST['bomid']:0;
		$this->resulting_product = isset($_POST['resultingproductid'])?$_POST['resultingproductid']:0;
		$this->resulting_product_qty = isset($_POST['resultingquantity'])?$_POST['resultingquantity']:1.00;
		$this->description = isset($_POST['description'])?$_POST['description']:'';
		$this->rev_enabled = isset($_POST['rev_enabled'])?$_POST['rev_enabled']:false;
		$this->rev_number = isset($_POST['rev_number'])?$_POST['rev_number']:1;
		$q = "INSERT INTO bom_header (resulting_product_id,resulting_quantity,description,rev_enabled,
			rev_number,created_by,creation_date,last_update_by,last_update_date) VALUES (?,?,?,?,?,?,NOW(),?,NOW());";
		$stmt = $this->dbconn->prepare($q);
		$stmt->bind_param('iissiii',$p1,$p2,$p3,$p4,$p5,$p6,$p8);
		if ($this->resulting_product < 1) {
			$this->mb->addError("A Bill of Materials must result in a product.");
			$stmt->close();
			return;
		}
		$p1 = $this->resulting_product;
		$p2 = $this->resulting_product_qty;
		$p3 = $this->description;
		$p4 = ($this->rev_enabled=='true')?'Y':'N';
		if ($this->rev_number < 1) $this->rev_number = 1;
		$p5 = $this->rev_number;
		$p6 = $_SESSION['dbuserid'];
		$p8 = $_SESSION['dbuserid'];
		$result = $stmt->execute();
		if ($result!==false) {
			$this->bom_id = $this->dbconn->insert_id;
			echo 'inserted|'.$this->bom_id;
		} else {
			echo 'fail|'.$this->dbconn->error;
			$this->mb->addError($this->dbconn->error);
		}
		$stmt->close();		
	}
	private function insertDetail() {
		$this->resetDetail();
		$this->bom_id = isset($_POST['bomid'])?$_POST['bomid']:0;
		$this->bom_detail_id = isset($_POST['bomdetailid'])?$_POST['bomdetailid']:0;
		$this->step_number = isset($_POST['stepnumber'])?$_POST['stepnumber']:0;
		$this->step_type = isset($_POST['steptype'])?$_POST['steptype']:'';
		$this->component_product_id = isset($_POST['component'])?$_POST['component']:null;
		$this->component_quantity_used = isset($_POST['componentqty'])?$_POST['componentqty']:null;
		$this->bom_step_id = isset($_POST['bom_step_id'])?$_POST['bom_step_id']:null;
		$this->seconds_to_process = isset($_POST['processtime'])?$_POST['processtime']:null;
		$this->sub_bom_id = isset($_POST['sub_bom_id'])?$_POST['sub_bom_id']:null;
		$this->detail_description = isset($_POST['description'])?$_POST['description']:'';
		$this->detail_rev_enabled = isset($_POST['rev_enabled'])?$_POST['rev_enabled']:false;
		$this->detail_rev_number = isset($_POST['rev_number'])?$_POST['rev_number']:1;
		$q = "INSERT INTO bom_detail (bom_id,step_number,step_type,component_product_id,component_quantity_used,bom_step_id,seconds_to_process,
			sub_bom_id,description,rev_enabled,rev_number,created_by,creation_date,last_update_by,last_update_date) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,NOW(),?,NOW());";
		$stmt = $this->dbconn->prepare($q);
		$stmt->bind_param('iisidiiissiii',$p1,$p3,$p4,$p5,$p6,$p7,$p8,$p9,$p10,$p11,$p12,$p13,$p15);
		if ($this->bom_id==0) {
			$this->mb->addError("Details cannot be inserted when the BOM ID is zero.");
			$stmt->close();
			return;
		}
		$p1 = $this->bom_id;
		// For update: $p2 = $this->bom_detail_id;
		$p3 = $this->step_number;
		if (strpos('CcPpBb',$this->step_type)===false) {
			$this->mb->addError("Please select a step type: Component, Process, or Sub-BOM.");
			$stmt->close();
			return;
		}
		$p4 = $this->step_type;
		if ($this->step_type=='C') {
			if (is_null($this->component_product_id) || is_null($this->component_quantity_used)) {
				$this->mb->addError("When implementing a component step, both a component product id and a quantity are required.");
				$stmt->close();
				return;
			}
			$p5 = $this->component_product_id;
			$p6 = $this->component_quantity_used;
			$p7 = null;
			$p8 = null;
			$p9 = null;
		}
		if ($this->step_type=='P') {
			if (is_null($this->bom_step_id) || is_null($this->seconds_to_process)) {
				$this->mb->addError("When implementing a process step, both a process type and time are required.");
				$stmt->close();
				return;
			}
			$p5 = null;
			$p6 = null;
			$p7 = $this->bom_step_id;
			$p8 = $this->seconds_to_process;
			$p9 = null;
		}
		if ($this->step_type=='B') {
			if (is_null($this->sub_bom_id)) {
				$this->mb->addError("When implementing a Sub-BOM, the ID of the link is required.");
				$stmt->close();
				return;
			}
			$p5 = null;
			$p6 = null;
			$p7 = null;
			$p8 = null;
			$p9 = $this->sub_bom_id;
		}
		$p10 = $this->detail_description;
		$p11 = ($this->detail_rev_enabled=='true')?'Y':'N';
		if ($this->detail_rev_number < 1) $this->detail_rev_number = 1;
		$p12 = $this->detail_rev_number;
		$p13 = $_SESSION['dbuserid'];
		$p15 = $_SESSION['dbuserid'];
		$result = $stmt->execute();
		if ($result!==false) {
			$this->bom_detail_id = $this->dbconn->insert_id;
			echo 'inserted|'.$this->bom_detail_id;
		} else {
			echo 'fail|'.$this->dbconn->error;
			$this->mb->addError($this->dbconn->error);
		}
		$stmt->close();				
		// TODO: Update all detail records where step_number >= $this->step_number to +1.
	}
	private function updateHeader() {
		$this->resetHeader();
		$this->resetDetail();
		
	}
	private function updateDetail() {
		$this->resetDetail();
		
	}
	public function insertRecord() {
		// Assumes values are stored in $_POST
		$this->mb->addInfo("Inserting ".$_POST['level']);
		if (isset($_POST['level']) && $_POST['level']=='header') $this->insertHeader();
		if (isset($_POST['level']) && $_POST['level']=='detail') $this->insertDetail();
	}
	public function updateRecord() {
		// Assumes values are stored in $_POST
		if (isset($_POST['level']) && $_POST['level']=='header') $this->updateHeader();
		if (isset($_POST['level']) && $_POST['level']=='detail') $this->updateDetail();
	}
	public function saveRecord() {
		
	} // function saveRecord()
} // class BOM (Bill of Material 
?>