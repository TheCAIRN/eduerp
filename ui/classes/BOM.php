<?php
class BOM extends ERPBase {
	private $bom_id;
	private $resulting_product;
	private $resulting_product_qty;
	private $bom_description;
	private $rev_enabled;
	private $rev_number;
	
	private $bom_detail_id;
	private $step_number;
	private $step_type;
	private $component_product_id;
	private $component_quantity_used;
	private $bom_step_id;
	private $seconds_to_process;
	private $sub_bom_id;
	private $detail_description;
	private $detail_rev_enabled;
	private $detail_rev_number;
	private $detail_array();
	
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
		$this->resulting_product_qty = 0.00;
		$this->bom_description = '';
		$this->rev_enabled = false;
		$this->rev_number = 1;
		$this->detail_array() = array();
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
		$this->detail_rev_enabled = false;
		$this->detail_rev_number = 1;
	}
	public function listRecords() {
		parent::abstractListRecords('BOM');
	} // function listRecords()
	public function searchPage() {
		parent::abstractSearchPage('BOMSearch');
	} // function searchPage()
	public function executeSearch($criteria) {
		
	} // function executeSearch()
	public function isIDValid($id) {
		// TODO: Validate that the ID is actually a record in the database
		if ($id<1) return false;
		if (is_integer($id)) return true;
		if (ctype_digit($id)) return true;
		return false;
	} // function isIDValid()
	public function display($id) {
		
	} // function display()
	public function newRecord() {
		echo parent::abstractNewRecord('BOM');
		$_SESSION['currentScreen'] = 319;
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