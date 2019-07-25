<?php
class Purchasing extends ERPBase {
	private $purchase_order_number;
	private $vendor_id;
	private $order_date;
	private $purchase_order_reference;
	private $entity_id;
	private $division_id;
	private $department_id;
	private $terms_id;
	private $rev_enabled;
	private $rev_number;
	private $huser_creation;
	private $hdate_creation;
	private $huser_modify;
	private $hdate_modify;
	private $column_list_header = 'purchase_order_number,vendor_id,order_date,purchase_order_reference,entity_id,division_id,department_id,terms,rev_enabled,rev_number';
	
	private $pur_detail_id;
	private $po_line;
	private $parent_line;
	private $item_id;
	private $quantity;
	private $quantity_uom;
	private $price;
	private $gl_account_id;
	private $fv_vendor_id;
	private $quantity_shipped;
	private $date_shipped;
	private $tracking_number;
	private $detail_rev_enabled;
	private $detail_rev_number;
	private $duser_creation;
	private $ddate_creation;
	private $duser_modify;
	private $ddate_modify;
	private $column_list_detail = 'pur_detail_id,po_line,parent_line,item_id,quantity,quantity_uom,price,gl_account_id,fv_vendor_id,quantity_shipped,
		date_shipped,tracking_number,rev_enabled,rev_number';
	
	private $detail_array;
	public function __construct($link=null) {
		parent::__construct($link);
		$this->supportsAttachments = null;
		$this->supportsNotes = 'pur_header_notes';
		$this->primaryKey = 'purchase_order_number';
		$this->searchFields[] = array('pur_header','unified_search','Type in any item related info and click Search','textbox');
		
		$this->entryFields[] = array('pur_header','','Purchase Order','fieldset');
		$this->entryFields[] = array('pur_header','purchase_order_number','Order #','integerid');
		$this->entryFields[] = array('pur_header','vendor_id','Vendor','dropdown','pur_vendors',array('vendor_id','vendor_name'));
		$this->entryFields[] = array('pur_header','order_date','Order Date','datetime','now');
		$this->entryFields[] = array('pur_header','purchase_order_reference','Reference','textbox');
		$this->entryFields[] = array('pur_header','entity_id','Entity','dropdown','ent_entities',array('entity_id','entity_name'));
		$this->entryFields[] = array('pur_header','division_id','Division','dropdown','ent_division_master',array('division_id','division_name'));
		$this->entryFields[] = array('pur_header','department_id','Department','dropdown','ent_department_master',array('department_id','department_name'));
		$this->entryFields[] = array('pur_header','terms','Terms','dropdown','aa_terms',array('terms_id','terms_code'));
		$this->entryFields[] = array('pur_header','rev_enabled','Enable Revision Tracking','checkbox','rev_number');
		$this->entryFields[] = array('pur_header','rev_number','Revision number','integer');
		$this->entryFields[] = array('pur_header','','','endfieldset');
		$this->entryFields[] = array('pur_detail','','Purchase Order Detail','fieldtable');
		$this->entryFields[] = array('pur_detail','pur_detail_id','Order Detail #','integerid');
		$this->entryFields[] = array('pur_detail','po_line','Order Line #','integer');
		$this->entryFields[] = array('pur_detail','parent_line','Parent Line #','integer');
		$this->entryFields[] = array('pur_detail','item_id','Item','embedded');
		$this->entryFields[] = array('pur_detail','item_id','Item','Item');
		$this->entryFields[] = array('pur_detail','','','endembedded');
		$this->entryFields[] = array('pur_detail','quantity','Quantity','decimal',11,5);
		$this->entryFields[] = array('pur_detail','quantity_uom','Quantity UOM','dropdown','aa_uom',array('uom_code','uom_description'));
		$this->entryFields[] = array('pur_detail','price','Price','decimal',17,5);
		$this->entryFields[] = array('pur_detail','gl_account_id','G/L Account','dropdown','acgl_accounts',array('gl_account_id','gl_account_name'));
		$this->entryFields[] = array('pur_detail','fv_vendor_id','Shipper','dropdown','fv_freight_vendors',array('fv_vendor_id','fv_vendor_name'));
		$this->entryFields[] = array('pur_detail','quantity_shipped','Qty Shipped','decimal',17,5);
		$this->entryFields[] = array('pur_detail','date_shipped','Date Shipped','datetime');
		$this->entryFields[] = array('pur_detail','tracking_number','Tracking #','textbox');
		$this->entryFields[] = array('pur_detail','rev_enabled','Enable Revision Tracking','checkbox','rev_number');
		$this->entryFields[] = array('pur_detail','rev_number','Revision number','integer');
		$this->entryFields[] = array('pur_detail','','Add Row','newlinebutton','newPurchasingDetailRow();');
		$this->entryFields[] = array('pur_detail','','','endfieldtable');
	}
	public function resetHeader() {
		$this->purchase_order_number = 0;
		$this->vendor_id = 0;
		$this->order_date = null;
		$this->purchase_order_reference = '';
		$this->entity_id = 0;
		$this->division_id = 0;
		$this->department_id = 0;
		$this->terms_id = 0;
		$this->rev_enabled = false;
		$this->rev_number = 1;
		$this->detail_array = array();
	}
	public function resetDetail() {
		$this->pur_detail_id = 0;
		$this->po_line = 0;
		$this->parent_line = 0;
		$this->item_id = 0;
		$this->quantity = 0.00;
		$this->quantity_uom = '';
		$this->price = 0.00;
		$this->gl_account_id = 0;
		$this->fv_vendor_id = null;
		$this->quantity_shipped = 0.00;
		$this->date_shipped = null;
		$this->tracking_number = '';
		$this->detail_rev_enabled = false;
		$this->detail_rev_number = 1;
	}
	public function arrayifyHeader() {
		return array(
			'purchase_order_number'=>$this->purchase_order_number
			,'vendor_id'=>$this->vendor_id
			,'order_date'=>$this->order_date
			,'purchase_order_reference'=>$this->purchase_order_reference
			,'entity_id'=>$this->entity_id
			,'division_id'=>$this->division_id
			,'department_id'=>$this->department_id
			,'terms'=>$this->terms_id
			,'rev_enabled'=>$this->rev_enabled
			,'rev_number'=>$this->rev_number
		);
	}
	public function arrayifyDetail() {
		return array(
			'pur_detail_id'=>$this->pur_detail_id
			,'po_line'=>$this->po_line
			,'parent_line'=>$this->parent_line
			,'item_id'=>$this->item_id
			,'quantity'=>$this->quantity
			,'quantity_uom'=>$this->quantity_uom
			,'price'=>$this->price
			,'gl_account_id'=>$this->gl_account_id
			,'fv_vendor_id'=>$this->fv_vendor_id
			,'quantity_shipped'=>$this->quantity_shipped
			,'date_shipped'=>$this->date_shipped
			,'tracking_number'=>$this->tracking_number
			,'rev_enabled'=>$this->detail_rev_enabled
			,'rev_number'=>$this->detail_rev_number
		);
	}
	public function listRecords() {
		parent::abstractListRecords('Purchasing');
	} // function listRecords()
	public function searchPage() {
		parent::abstractSearchPage('PurchasingSearch');
	} // function searchPage()
	public function executeSearch($criteria) {
		$result = null;
		$q = 'SELECT DISTINCT h.purchase_order_number,vendor_name,order_date,purchase_order_reference 
			FROM pur_header h
			LEFT OUTER JOIN pur_detail d ON h.purchase_order_number=d.purchase_order_number
			LEFT OUTER JOIN pur_vendors v ON h.vendor_id=v.vendor_id
			LEFT OUTER JOIN item_master i ON d.item_id=i.product_id';
		// Add $criteria
		if (!is_null($criteria) && is_array($criteria) && count($criteria)>0) {
			// The only key for Addresses is unified_search.
			if (is_array($criteria[0]) && count($criteria[0])>=2 && $criteria[0][0]=='unified_search') $criteria = $criteria[0][1];
			else $criteria='';
			$q .= " WHERE h.purchase_order_number=? OR vendor_name LIKE ? OR purchase_order_reference LIKE ? OR d.item_id = ? OR product_code LIKE ? OR product_description LIKE ? OR product_catalog_title LIKE ? or gtin=?";
			$stmt = $this->dbconn->prepare($q);
			$stmt->bind_param('ississss',$p1,$p2,$p3,$p4,$p5,$p6,$p7,$p8);
			$p2 = $p3 = $p5 = $p6 = $p7 = '%'.$criteria.'%';
			$p8 = $criteria;
			$p1 = $p4 = ctype_digit($criteria)?$criteria:-99999;
			$result = $stmt->execute();
			if ($result !== false) {
				$this->recordSet = array();
				if (isset($_SESSION['recordSet']['Purchasing'])) unset($_SESSION['recordSet']['Purchasing']); // A search criteria was given, so do not display the last search on an empty set.
				$stmt->store_result();
				$vendorname='';
				$stmt->bind_result($this->purchase_order_number,$vendorname,$this->order_date,$this->purchase_order_reference);
				while ($stmt->fetch()) {
					$this->recordSet[$this->purchase_order_number] = array('Vendor Name'=>$vendorname,'Order Date'=>$this->order_date,'Reference'=>$this->purchase_order_reference);
				}
			}
		// if criteria exists
		} else {
			$q .= " ORDER BY h.purchase_order_number";
			$result = $this->dbconn->query($q);
			if ($result!==false) {
				$this->recordSet = array();
				while ($row=$result->fetch_assoc()) {
					$this->recordSet[$row['purchase_order_number']] = array('Vendor Name'=>$row['vendor_name'],'Order Date'=>$row['order_date'],'Reference'=>$row['purchase_order_reference']);
				} // while rows
			} // if query succeeded
		} // if criteria does not exist
		$this->listRecords();
		$_SESSION['currentScreen'] = 1007;
		$_SESSION['lastCriteria'] = $criteria;
		if (!isset($_SESSION['searchResults'])) $_SESSION['searchResults'] = array();
		$_SESSION['searchResults']['Purchasing'] = array_keys($this->recordSet);		
	} // function executeSearch()
	public function isIDValid($id) {
		// TODO: Validate that the ID is actually a record in the database
		if ($id<1) return false;
		if (is_integer($id)) return true;
		if (ctype_digit($id)) return true;
		return false;
	} // function isIDValid()
	public function display($id,$mode='view') {
		if (!$this->isIDValid($id)) return;
		$readonly = true;
		$html = '';
		$q = "SELECT {$this->column_list_header},h.created_by,h.creation_date,h.last_update_by,h.last_update_date 
			FROM pur_header h 
			WHERE purchase_order_number=?";
		$stmt = $this->dbconn->prepare($q);
		if ($stmt===false) {
			echo $this->dbconn->error;
			return;
		}
		$stmt->bind_param('i',$PurchaseOrdersid);
		$PurchaseOrdersid = $id;
		$result = $stmt->execute();
		// TODO: What if another user deletes the record while it's still in my search results?
		if ($result!==false) {
			$stmt->bind_result(
				$this->purchase_order_number
				,$this->vendor_id
				,$this->order_date
				,$this->purchase_order_reference
				,$this->entity_id
				,$this->division_id
				,$this->department_id
				,$this->terms_id
				,$this->rev_enabled
				,$this->rev_number
				,$this->huser_creation,$this->hdate_creation,$this->huser_modify,$this->hdate_modify
			);
			$stmt->store_result();
			$stmt->fetch();
			$this->currentRecord = $id;
			$stmt->close();		

			$q = "SELECT {$this->column_list_detail},d.created_by,d.creation_date,d.last_update_by,d.last_update_date 
				FROM pur_detail d 
				WHERE purchase_order_number=?";
			$stmt = $this->dbconn->prepare($q);
			if ($stmt===false) {
				echo $this->dbconn->error;
				return;
			}
			$stmt->bind_param('i',$PurchaseOrdersid);
			$PurchaseOrdersid = $id;
			$dresult = $stmt->execute();
			if ($dresult!==false) {
				$stmt->bind_result(
					$this->pur_detail_id
					,$this->po_line
					,$this->parent_line
					,$this->item_id
					,$this->quantity
					,$this->quantity_uom 
					,$this->price
					,$this->gl_account_id
					,$this->fv_vendor_id
					,$this->quantity_shipped
					,$this->date_shipped
					,$this->tracking_number
					,$this->detail_rev_enabled 
					,$this->detail_rev_number
					,$this->duser_creation
					,$this->ddate_creation
					,$this->duser_modify
					,$this->ddate_modify
				
				);
				$stmt->store_result();
				while ($stmt->fetch()) {
					$this->detail_array[$this->pur_detail_id] = $this->arrayifyDetail();
				}
				$stmt->close();
			}
			
			if ($mode!='update') {
				$hdata = $this->arrayifyHeader();
				echo parent::abstractRecord($mode,'Purchasing','',$hdata,$this->detail_array);
			}
		} // if result
		else $this->purchase_order_number = null;
		//echo $html;
		$_SESSION['currentScreen'] = 2007;
		if (!isset($_SESSION['searchResults']) && !isset($_SESSION['searchResults']['Purchasing']))
			$_SESSION['idarray'] = array(0,0,$id,0,0);
		else {
			$idloc = array_search($id,$_SESSION['searchResults']['Purchasing'],false);
			$f = $_SESSION['searchResults']['Purchasing'][0];
			$l = $_SESSION['searchResults']['Purchasing'][] = array_pop($_SESSION['searchResults']['Purchasing']); // https://stackoverflow.com/questions/3687358/whats-the-best-way-to-get-the-last-element-of-an-array-without-deleting-it#comment63556865_3687358
			if ($idloc > 0) $p = $_SESSION['searchResults']['Purchasing'][$idloc-1]; else $p = $f;
			if ($l != $id) $n = $_SESSION['searchResults']['Purchasing'][$idloc+1]; else $n = $l;
			$_SESSION['idarray'] = array($f,$p,$id,$n,$l);
		}		
			
	} // function display()
	public function newRecord() {
		echo parent::abstractNewRecord('Purchasing');
		$_SESSION['currentScreen'] = 3007;
	} // function newRecord()
	public function editRecord($id) {
		$this->display($id,'edit');
		$_SESSION['currentScreen'] = 4007;
	}
	private function insertHeader() {
		$this->resetHeader();
		$this->resetDetail();
		$vendorid = isset($_POST['vendorid'])?$_POST['vendorid']:0;
		$orderdate_date = isset($_POST['orderdate_date'])?$_POST['orderdate_date']:'';
		$orderdate_time = isset($_POST['orderdate_time'])?$_POST['orderdate_time']:'';
		$orderreference = isset($_POST['orderreference'])?$_POST['orderreference']:'';
		$entityid = isset($_POST['entityid'])?$_POST['entityid']:0;
		$divisionid = isset($_POST['divisionid'])?$_POST['divisionid']:0;
		$departmentid = isset($_POST['departmentid'])?$_POST['departmentid']:0;
		$termsid = isset($_POST['termsid'])?$_POST['termsid']:0;
		$rev_enabled = isset($_POST['rev_enabled'])?$_POST['rev_enabled']:false;
		$rev_number = isset($_POST['rev_number'])?$_POST['rev_number']:1;
		$return_date = false;
		if (strlen(trim($orderdate_date))==0) $return_date = true;
		$orderdate = new DateTime($orderdate_date.' '.$orderdate_time);
		$q = "INSERT INTO pur_header (vendor_id,order_date,purchase_order_reference,entity_id,division_id,department_id,terms,rev_enabled,rev_number,created_by,".
			"creation_date,last_update_by,last_update_date) VALUES (?,?,?,?,?,?,?,?,?,?,NOW(),?,NOW());";
		$stmt = $this->dbconn->prepare($q);
		$stmt->bind_param('issiiiisiii',$p1,$p2,$p3,$p4,$p5,$p6,$p7,$p8,$p9,$p10,$p12);
		if ($vendorid < 1) {
			$this->mb->addError("Vendor ID was not selected.");
			$stmt->close();
			return;
		}
		$p1 = $vendorid;
		if (is_null($orderdate)) {
			$this->mb->addError("The order date is not formatted correctly.");
			$stmt->close();
			return;
		}
		$p2 = $orderdate->format("Y-m-d H:i:s");
		$p3 = $orderreference;
		if ($entityid==0) {
			$this->mb->addError("The entity cannot be blank for purchase orders.");
			$stmt->close();
			return;
		}
		$p4 = $entityid;
		$p5 = ($divisionid==0)?null:$divisionid;
		$p6 = ($departmentid==0)?null:$departmentid;
		$p7 = ($termsid==0)?null:$termsid;
		$p8 = ($rev_enabled=='true')?'Y':'N';
		if ($rev_number < 1) $rev_number = 1;
		$p9 = $rev_number;
		$p10 = $_SESSION['dbuserid'];
		$p12 = $_SESSION['dbuserid'];
		$result = $stmt->execute();
		if ($result!==false) {
			echo 'inserted|'.$this->dbconn->insert_id.($return_date?'|'.$p2:'');
		} else {
			echo 'fail|'.$this->dbconn->error;
			$this->mb->addError($this->dbconn->error);
		}
		$stmt->close();
	}
	private function insertDetail() {
		$this->resetDetail();
		$orderkey = isset($_POST['orderkey'])?$_POST['orderkey']:0;
		$orderlinenum = isset($_POST['orderlinenum'])?$_POST['orderlinenum']:0;
		$parentlinenum = isset($_POST['parentlinenum'])?$_POST['parentlinenum']:0;
		$itemid = isset($_POST['itemid'])?$_POST['itemid']:'';
		$quantity = isset($_POST['quantity'])?$_POST['quantity']:0;
		$quantity_uom = isset($_POST['quantity_uom'])?$_POST['quantity_uom']:'EA';
		$price = isset($_POST['price'])?$_POST['price']:0.00;
		$gl_account_id = isset($_POST['gl_account_id'])?$_POST['gl_account_id']:null;
		$shipper = isset($_POST['fv_vendor_id'])?$_POST['fv_vendor_id']:null;
		$qtyshipped = isset($_POST['quantity_shipped'])?$_POST['quantity_shipped']:0.00;
		$dateshipped_date = isset($_POST['date_shipped_date'])?$_POST['date_shipped_date']:null;
		$dateshipped_time = isset($_POST['date_shipped_time'])?$_POST['date_shipped_time']:null;
		$tracking = isset($_POST['tracking_number'])?$_POST['tracking_number']:'';
		$rev_enabled = isset($_POST['rev_enabled'])?$_POST['rev_enabled']:false;
		$rev_number = isset($_POST['rev_number'])?$_POST['rev_number']:1;
		$entityid = isset($_POST['entityid'])?$_POST['entityid']:0;
		$divisionid = isset($_POST['divisionid'])?$_POST['divisionid']:0;
		$departmentid = isset($_POST['departmentid'])?$_POST['departmentid']:0;
		$dateshipped = new DateTime($dateshipped_date.' '.$dateshipped_time);
		
		/* The entity, division, and department are for future use, where one entity may be purchasing materials for another. */
		$q = "INSERT INTO pur_detail (purchase_order_number,po_line,parent_line,entity_id,division_id,department_id, item_id,quantity,quantity_uom,price,gl_account_id,
			fv_vendor_id,quantity_shipped,date_shipped,tracking_number,
			rev_enabled,rev_number,created_by,creation_date,last_update_by,last_update_date) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,NOW(),?,NOW());";
		$stmt = $this->dbconn->prepare($q);
		$stmt->bind_param('iiiiiisdsdiidsssiii',$p1,$p2,$p3,$p4,$p5,$p6,$p7,$p8,$p9,$p10,$p11,$p12,$p13,$p14,$p15,$p16,$p17,$p18,$p19);
		if ($orderkey==0) {
			$this->mb->addError("Details cannot be inserted when the purchase order number is zero.");
			$stmt->close();
			return;
		}
		$p1 = $orderkey;
		/* Question: Should orderlinenum automatically be updated to the next sequence if it's 0 or a number which already exists? */
		$p2 = $orderlinenum;
		/* Question: Should parentline be set to null if it references a line which no longer exists? */
		$p3 = $parentlinenum;
		if ($entityid==0) {
			$this->mb->addError("The entity cannot be blank for purchase orders.");
			$stmt->close();
			return;
		}
		$p4 = $entityid;
		$p5 = ($divisionid==0)?null:$divisionid;
		$p6 = ($departmentid==0)?null:$departmentid;
		$p7 = $itemid;
		/* Note: Orders with a quantity of 0 may be used as placeholders from a quote, where the remaining balance may be ordered at a future time. */
		$p8 = $quantity;
		$p9 = $quantity_uom;
		$p10 = $price;
		$p11 = $gl_account_id;
		$p12 = $shipper;
		$p13 = $qtyshipped;
		if (is_null($dateshipped)) {
			$this->mb->addError("The ship date is not formatted correctly.");
			$stmt->close();
			return;
		}
		$p14 = $dateshipped->format("Y-m-d H:i:s");
		$p15 = $tracking;
		$p16 = ($rev_enabled=='true')?'Y':'N';
		if ($rev_number<1) $rev_number = 1;
		$p17 = $rev_number;
		$p18 = $_SESSION['dbuserid'];
		$p19 = $_SESSION['dbuserid'];
		$result = $stmt->execute();
		if ($result!==false) {
			echo 'inserted|'.$this->dbconn->insert_id;
		} else {
			echo 'fail|'.$this->dbconn->error;
			$this->mb->addError($this->dbconn->error);
		}
		$stmt->close();
	}
	private function updateHeader() {
		$this->resetHeader();
		$this->resetDetail();
		$now = new DateTime();
		$id = $_POST['orderkey'];
		if ((!is_integer($id) && !ctype_digit($id)) || $id<1) {
			echo 'fail|Invalid purchase order number for updating';
			return;
		}
		$this->display($id,'update'); // Display already has the logic for loading the record.  TODO: Refactor into separate function.
		if (is_null($this->purchase_order_number)) {
			echo 'fail|Invalid purchase order number for updating';
			return;
		}
		$update = array();
		if (isset($_POST['vendorid']) && $_POST['vendorid']!=$this->vendor_id) $update['vendor_id'] = array('i',$_POST['vendorid']);
		$orderdate = null;
		if (isset($_POST['orderdate_date']) && isset($_POST['orderdate_time'])) $orderdate = new DateTime($_POST['orderdate_date'].' '.$_POST['orderdate_time']);
		if (!empty($orderdate) && $orderdate->format('Y-m-d H:i:s')!=$this->order_date) $update['order_date'] = array('s',$orderdate->format('Y-m-d H:i:s'));
		if (isset($_POST['orderreference']) && $_POST['orderreference']!=$this->purchase_order_reference) $update['purchase_order_reference'] = array('s',$_POST['orderreference']);
		if (isset($_POST['entityid']) && $_POST['entityid']!=$this->entity_id) $update['entity_id'] = array('i',$_POST['entityid']);
		if (isset($_POST['divisionid']) && $_POST['divisionid']!=$this->division_id) $update['division_id'] = array('i',$_POST['divisionid']);
		if (isset($_POST['departmentid']) && $_POST['departmentid']!=$this->department_id) $update['department_id'] = array('i',$_POST['departmentid']);
		if (isset($_POST['termsid']) && $_POST['termsid']!=$this->terms_id) $update['terms_id'] = array('i',$_POST['termsid']);
		$reven = null;
		if (isset($_POST['rev_enabled'])) $reven = ($_POST['rev_enabled']=='true')?'Y':'N';
		if (!is_null($reven) && $reven!=$this->rev_enabled) $update['rev_enabled'] = array('s',$reven);
		if ((!is_null($reven)) && $reven=='Y' && isset($_POST['rev_number']) && $_POST['rev_number']!=$this->rev_number) $update['rev_number'] = array('i',$_POST['rev_number']);
		$update['last_update_date'] = array('s',$now->format('Y-m-d H:i:s'));
		$update['last_update_by'] = array('i',$_SESSION['dbuserid']);

		// Create UPDATE String
		
		if (count($update)<=2) { // last update is always set
			echo 'fail|Nothing to update';
			return;
		}
		$q = 'UPDATE pur_header SET ';
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
		$q .= ' WHERE purchase_order_number=?';
		$ctr++;
		$bp_types .= 'i';
		$bp_values[$ctr] = $this->purchase_order_number;
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
			echo 'updated';
		} else {
			if ($this->dbconn->error) {
				echo 'fail|'.$this->dbconn->error;
				$this->mb->addError($this->dbconn->error);
			} else echo 'fail|No rows updated';
		}
		$stmt->close();
	} // updateHeader()
	private function updateDetail() {
		$this->resetDetail();
		$now = new DateTime();
		$id = $_POST['orderkey'];
		$dtlid = $_POST['orderlinekey'];
		if ((!is_integer($id) && !ctype_digit($id)) || $id<1) {
			echo 'fail|Invalid purchase order number for updating';
			return;
		}
		if ((!is_integer($dtlid) && !ctype_digit($dtlid)) || $dtlid<1) {
			echo 'fail|Invalid purchase order detail id for updating';
			return;
		}
		$this->display($id,'update'); // Display already has the logic for loading the header record.  TODO: Refactor into separate function.
		if (is_null($this->purchase_order_number)) {
			echo 'fail|Invalid purchase order number for updating';
			return;
		}
		$this->pur_detail_id = $dtlid;
		$this->po_line = $this->detail_array[$dtlid]['po_line'];
		$this->parent_line = $this->detail_array[$dtlid]['parent_line'];
		$this->item_id = $this->detail_array[$dtlid]['item_id'];
		$this->quantity = $this->detail_array[$dtlid]['quantity'];
		$this->quantity_uom = $this->detail_array[$dtlid]['quantity_uom'];
		$this->price = $this->detail_array[$dtlid]['price'];
		$this->gl_account_id = $this->detail_array[$dtlid]['gl_account_id'];
		$this->fv_vendor_id = $this->detail_array[$dtlid]['fv_vendor_id'];
		$this->quantity_shipped = $this->detail_array[$dtlid]['quantity_shipped'];
		$this->date_shipped = $this->detail_array[$dtlid]['date_shipped'];
		$this->tracking_number = $this->detail_array[$dtlid]['tracking_number'];
		$this->detail_rev_enabled = $this->detail_array[$dtlid]['rev_enabled'];
		$this->detail_rev_number = $this->detail_array[$dtlid]['rev_number'];
		$dateshipped_date = isset($_POST['date_shipped_date'])?$_POST['date_shipped_date']:null;
		$dateshipped_time = isset($_POST['date_shipped_time'])?$_POST['date_shipped_time']:null;
		$dateshipped = new DateTime($dateshipped_date.' '.$dateshipped_time);
		$update = array();
		if (isset($_POST['orderlinenum']) && $_POST['orderlinenum']!=$this->po_line) $update['po_line'] = array('i',$_POST['orderlinenum']);
		if (isset($_POST['parentlinenum']) && $_POST['parentlinenum']!=$this->parent_line) $update['parent_line'] = array('i',$_POST['parentlinenum']);
		if (isset($_POST['itemid']) && $_POST['itemid']!=$this->item_id) $update['item_id'] = array('i',$_POST['itemid']);
		if (isset($_POST['quantity']) && $_POST['quantity']!=$this->quantity) $update['quantity'] = array('d',$_POST['quantity']);
		if (isset($_POST['quantity_uom']) && $_POST['quantity_uom']!=$this->quantity_uom) $update['quantity_uom'] = array('s',$_POST['quantity_uom']);
		if (isset($_POST['price']) && $_POST['price']!=$this->price) $update['price'] = array('d',$_POST['price']);
		if (isset($_POST['gl_account_id']) && $_POST['gl_account_id']!=$this->gl_account_id) $update['gl_account_id'] = array('i',$_POST['gl_account_id']);
		if (isset($_POST['fv_vendor_id']) && $_POST['fv_vendor_id']!=$this->fv_vendor_id) $update['fv_vendor_id'] = array('i',$_POST['fv_vendor_id']);
		if (isset($_POST['quantity_shipped']) && $_POST['quantity_shipped']!=$this->quantity_shipped) $update['quantity_shipped'] = array('d',$_POST['quantity_shipped']);
		if (!empty($dateshipped) && $dateshipped->format('Y-m-d H:i:s')!=$this->date_shipped) $update['date_shipped'] = array('s',$dateshipped->format('Y-m-d H:i:s'));
		if (isset($_POST['tracking_number']) && $_POST['tracking_number']!=$this->tracking_number) $update['tracking_number'] = array('s',$_POST['tracking_number']);
		$reven = null;
		if (isset($_POST['rev_enabled'])) $reven = ($_POST['rev_enabled']=='true')?'Y':'N';
		if (!is_null($reven) && $reven!=$this->rev_enabled) $update['rev_enabled'] = array('s',$reven);
		if ((!is_null($reven)) && $reven=='Y' && isset($_POST['rev_number']) && $_POST['rev_number']!=$this->rev_number) $update['rev_number'] = array('i',$_POST['rev_number']);
		$update['last_update_date'] = array('s',$now->format('Y-m-d H:i:s'));
		$update['last_update_by'] = array('i',$_SESSION['dbuserid']);

		// Create UPDATE String
		
		if (count($update)<=2) { // last update is always set
			echo 'fail|Nothing to update';
			return;
		}
		$q = 'UPDATE pur_detail SET ';
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
		$q .= ' WHERE pur_detail_id=?';
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
			echo 'updated';
		} else {
			if ($this->dbconn->error) {
				echo 'fail|'.$this->dbconn->error;
				$this->mb->addError($this->dbconn->error);
			} else echo 'fail|No rows updated';
		}
		$stmt->close();
	} // updateDetail()
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
} // class Purchasing
?>