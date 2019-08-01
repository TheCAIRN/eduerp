<?php
/*
 * The InventoryManager class is not a typical BRED (or CRUD) module, in that transactions are not usually entered directly.
 * Item Variants and Locator Inventory will be added in a future revision, though the tables already exist.
 * This class provides support for all other inventory-affecting classes to manage transactions and maintain a consistent inventory.
 * SQL Stored Procedures will be available to correct any errors in inventory.
 */
class InventoryManager extends ERPBase {
	private $inventory_id;
	private $entity_id;
	private $product_id;
	private $variant_code;
	private $total_on_hand; 		// How many units are currently at this entity?
	private $total_in_wip;			// How many units are in production (BOM units already consumed)?
	private $total_on_order;		// How many units exist in the purchasing module?
	private $total_reserved;		// How many units are from Sales Orders in Lead or Quote status?
	private $total_unshipped_sold;	// How many units are from Sales Orders in Order or Pick status?
	private $total_shipped_sold;	// How many units are from Sales Orders in Shipped or Invoice status (reducing total_on_hand)?
		// Returned units have a negative shipped and positive on hand transaction.
	private $inv_created_by;
	private $inv_creation_date;
	private $inv_last_update_by;
	private $inv_last_update_date;
	
	private $inv_transaction_id;
	private $inv_transaction_type;
		/* Transaction Type may be one of C=Item change (multiple items), M=Item move between entities, 
		 * P=Physical Count (on_hand_delta is actual quantity counted),
		 * Q=Quantity adjustment (single item; inv id 1 and 2 will be the same)
		 */	
	private $reference_note;
	private $reference_table;
	private $reference_key_int;
	private $reference_key_char;
	private $inventory_id_1; // For types C and M, the entity or product losing units.
	private $inventory_id_2; // For types C and M, the entity or product gaining units.  For type P, this value is meaningless. 
							 // For type Q, the adjustment is being made to two different columns of the same inventory_id.
	private $quantity_on_hand_delta_1;
	private $quantity_on_hand_delta_2;
	private $quantity_in_wip_delta_1;
	private $quantity_in_wip_delta_2;
	private $quantity_on_order_delta_1;
	private $quantity_on_order_delta_2;
	private $quantity_reserved_delta_1;
	private $quantity_reserved_delta_2;
	private $quantity_unshipped_delta_1;
	private $quantity_unshipped_delta_2;
	private $quantity_shipped_delta_1;
	private $quanitty_shipped_delta_2;
	private $trans_created_by;
	private $trans_creation_date;
	private $trans_last_update_by;
	private $trans_last_update_date;
	public function __construct($link=null) {
		parent::__construct($link);
		$this->supportsNotes = false;
		$this->supportsAttachments = false;
		$this->searchFields[] = array('inv_master','unified_search','Type an entity name, item number, or product text.','textbox');
		$this->entryFields[] = array('inv_master','','Inventory','fieldset');
		$this->entryFields[] = array('inv_master','inventory_id','ID','integerid');
		$this->entryFields[] = array('inv_master','entity_id','Entity','dropdown','ent_entities',array('entity_id','entity_name'));
		$this->entryFields[] = array('inv_master','product_id','Item','embedded');
		$this->entryFields[] = array('inv_master','product_id','Item','Item');
		$this->entryFields[] = array('inv_master','','','endembedded');
		//$this->entryFields[] = array('inv_master','variant_code','Variant','dropdown','item_variant_codes',array('variant_code','variant_description'));
		$this->entryFields[] = array('inv_master','total_on_hand','On Hand','decimal',24,5);
		$this->entryFields[] = array('inv_master','total_in_wip','Work In Progress','decimal',24,5);
		$this->entryFields[] = array('inv_master','total_on_order','On Order','decimal',24,5);
		$this->entryFields[] = array('inv_master','total_reserved','Reserved','decimal',24,5);
		$this->entryFields[] = array('inv_master','total_unshipped_sold','Sold - unshipped','decimal',24,5);
		$this->entryFields[] = array('inv_master','total_shipped_sold','Sold - shipped','decimal',24,5);
		$this->entryFields[] = array('inv_master','total_ats','Avail. to Sell','decimal',24,5);
		$this->entryFields[] = array('inv_master','','','endfieldset');
		$this->entryFields[] = array('inv_transactions','','Transactions','fieldtable');
		$this->entryFields[] = array('inv_transactions','inv_transaction_id','Trans ID','integerid');
		$this->entryFields[] = array('inv_transactions','inv_transaction_type','Trans Type','textbox');
		$this->entryFields[] = array('inv_transactions','reference_table','Table','textbox');
		$this->entryFields[] = array('inv_transactions','reference_key','Key','textbox');
		$this->entryFields[] = array('inv_transactions','on_hand_delta','Chg On Hand','decimal',24,5);
		$this->entryFields[] = array('inv_transactions','in_wip_delta','Chg In WIP','decimal',24,5);
		$this->entryFields[] = array('inv_transactions','on_order_delta','Chg On Order','decimal',24,5);
		$this->entryFields[] = array('inv_transactions','reserved_delta','Chg Reserved','decimal',24,5);
		$this->entryFields[] = array('inv_transactions','unshipped_delta','Chg Unshipped','decimal',24,5);
		$this->entryFields[] = array('inv_transactions','shipped_delta','Chg Shipped','decimal',24,5);
		$this->entryFields[] = array('inv_transactions','','','endfieldtable');		
	} // constructor
	public function resetEntityInventory() {
		$this->inventory_id = -1;
		$this->entity_id = -1;
		$this->product_id = -1;
		$this->variant_code = null;
		$this->total_on_hand = 0.00; 
		$this->total_in_wip = 0.00;	
		$this->total_on_order = 0.00;
		$this->total_reserved = 0.00;
		$this->total_unshipped_sold = 0.00;
		$this->total_shipped_sold = 0.00;	
		$this->inv_created_by = null;
		$this->inv_creation_date = null;
		$this->inv_last_update_by = null;
		$this->inv_last_update_date = null;		
	} // resetEntityInventory()
	public function resetTransaction() {
		$this->inv_transaction_id = -1;
		$this->inv_transaction_type = '';
		$this->reference_note = '';
		$this->reference_table = '';
		$this->reference_key_int = -1;
		$this->reference_key_char = null;
		$this->inventory_id_1 = null;
		$this->inventory_id_2 = null;
		$this->quantity_on_hand_delta_1 = 0.00;
		$this->quantity_on_hand_delta_2 = 0.00;
		$this->quantity_in_wip_delta_1 = 0.00;
		$this->quantity_in_wip_delta_2 = 0.00;
		$this->quantity_on_order_delta_1 = 0.00;
		$this->quantity_on_order_delta_2 = 0.00;
		$this->quantity_reserved_delta_1 = 0.00;
		$this->quantity_reserved_delta_2 = 0.00;
		$this->quantity_unshipped_delta_1 = 0.00;
		$this->quantity_unshipped_delta_2 = 0.00;
		$this->quantity_shipped_delta_1 = 0.00;
		$this->quanitty_shipped_delta_2 = 0.00;
		$this->trans_created_by = null;
		$this->trans_creation_date = null;
		$this->trans_last_update_by = null;
		$this->trans_last_update_date = null;
	} // resetTransaction()
	public function resetLocationInventory() {
		
	} // resetLocationInventory()
	public function arrayifyEntityInventory() {
		return array('inventory_id'=>$this->inventory_id,'entity_id'=>$this->entity_id,'product_id'=>$this->product_id,'variant_code'=>$this->variant_code,
			'total_on_hand'=>$this->total_on_hand,'total_in_wip'=>$this->total_in_wip,'total_on_order'=>$this->total_on_order,'total_reserved'=>$this->total_reserved,
			'total_unshipped_sold'=>$this->total_unshipped_sold,'total_shipped_sold'=>$this->total_shipped_sold,'created_by'=>$this->inv_created_by,
			'creation_date'=>$this->inv_creation_date,'last_update_by'=>$this->inv_last_update_by,'last_update_date'=>$this->inv_last_update_date,
			'total_ats'=>($this->total_on_hand+$this->total_in_wip+$this->total_on_order-$this->total_reserved-$this->total_unshipped_sold));		
	} // arrayifyEntityInventory()
	public function arrayifyTransaction() {
		return array('inv_transaction_id'=>$this->inv_transaction_id,'inv_transaction_type'=>$this->inv_transaction_type,'reference_note'=>$this->reference_note,
			'reference_table'=>$this->reference_table,'reference_key_int'=>$this->reference_key_int,'reference_key_char'=>$this->reference_key_char,
			'inventory_id_1'=>$this->inventory_id_1,'inventory_id_2'=>$this->inventory_id_2,'quantity_on_hand_delta_1'=>$this->quantity_on_hand_delta_1,
			'quantity_on_hand_delta_2'=>$this->quantity_on_hand_delta_2,'quantity_in_wip_delta_1'=>$this->quantity_in_wip_delta_1,'quantity_in_wip_delta_2'=>$this->quantity_in_wip_delta_2,
			'quantity_on_order_delta_1'=>$this->quantity_on_order_delta_1,'quantity_on_order_delta_2'=>$this->quantity_on_order_delta_2,'quantity_reservced_delta_1'=>$this->quantity_reserved_delta_1,
			'quantity_reserved_delta_2'=>$this->quantity_reserved_delta_2,'quantity_unshipped_delta_1'=>$this->quantity_unshipped_delta_1,
			'quantity_unshipped_delta_2'=>$this->quantity_unshipped_delta_2,'quantity_shipped_delta_1'=>$this->quantity_shipped_delta_1,
			'quantity_shipped_delta_2'=>$this->quantity_shipped_delta_2,'created_by'=>$this->trans_created_by,'creation_date'=>$this->trans_creation_date,
			'last_update_by'=>$this->trans_last_update_by,'last_update_date'=>$this->trans_last_update_date);
	} // arrayifyTransaction()
	public function arrayifyLocationInventory() {
		return null;
	} // arrayifyLocationInventory
	private function getInventoryId($entity,$item,$variant=null) {
		if ($entity==0 || $item==0) return null;
		$q = 'SELECT inventory_id FROM inv_master WHERE entity_id=? AND product_id=?';
		if (!is_null($variant)) {
			$q .= ' AND variant_code=?';
			$stmt = $this->dbconn->prepare($q);
			$stmt->bind_param('iis',$p1,$p2,$p3);
			$p1 = $entity;
			$p2 = $item;
			$p3 = $variant;
		} else {
			$q .= ' AND variant_code IS NULL';
			$stmt = $this->dbconn->prepare($q);
			$stmt->bind_param('ii',$p1,$p2);
			$p1 = $entity;
			$p2 = $item;
		}
		$result = $stmt->execute();
		if ($result!==false) {
			$stmt->store_result();
			if ($stmt->num_rows==0) {
				// Inventory record doesn't exist, so create one.
				$stmt->close();
				$this->resetEntityInventory();
				$nr = 'INSERT INTO inv_master (entity_id,product_id,variant_code,total_on_hand,total_in_wip,total_on_order,total_reserved,
					total_unshipped_sold,total_shipped_sold,created_by,creation_date,last_update_by,last_update_date) VALUES
					(?,?,?,0.00,0.00,0.00,0.00,0.00,0.00,?,NOW(),?,NOW());';
				$stmt = $this->dbconn->prepare($nr);
				$stmt->bind_param('iisii',$n1,$n2,$n3,$n4,$n5);
				$n1 = $entity;
				$n2 = $item;
				$n3 = $variant;
				$n4 = $_SESSION['dbuserid'];
				$n5 = $_SESSION['dbuserid'];
				$result = $stmt->execute();
				if ($result!==false) {
					$this->inventory_id = $this->dbconn->insert_id;
					$this->entity_id = $entity;
					$this->product_id = $item;
					$this->variant_code = $variant;
					$this->inv_creation_date = new DateTime();
					$this->inv_last_update_date = new DateTime();
					$this->inv_created_by = $_SESSION['dbuserid'];
					$this->inv_last_update_by = $_SESSION['dbuserid'];
					$stmt->close();
					return $this->inventory_id;
				} else {
					$stmt->close();
					return null;
				}
			} else {
				$this->resetEntityInventory();
				$stmt->bind_result($this->inventory_id);
				$stmt->fetch();
				$stmt->close();
				return $this->inventory_id;
			}
		} else {
			$stmt->close();
			return null;
		}
	} // getInventoryId
	public function purchasingPlaceOrder($purdetailid,$entity,$item,$quantity) {
		// Record the purchase in inventory transactions, and update the inventory master accordingly.
		$invid = $this->getInventoryId($entity,$item);
		if (is_null($invid)) {
			echo 'fail|Could not get or create the Entity Inventory ID. '.$this->dbconn->error;
			return false;
		}
		$q1 = "INSERT INTO inv_transactions (inv_transaction_type,reference_table,reference_key_int,inventory_id_1,quantity_on_order_delta_1,created_by,creation_date,last_update_by,last_update_date)
			VALUES ('Q','pur_detail',?,?,?,?,NOW(),?,NOW());";
		$stmt1 = $this->dbconn->prepare($q1);
		$stmt1->bind_param('iidii',$o1,$o2,$o3,$o4,$o5);
		$o1 = $purdetailid;
		$o2 = $invid;
		$o3 = $quantity;
		$o4 = $o5 = $_SESSION['dbuserid'];
		$result1 = $stmt1->execute();
		if ($result1!==false) {
			$stmt1->close();
			$this->display($invid,'update');
			$this->total_on_order += $quantity;
			$q2 = "UPDATE inv_master SET total_on_order=?,last_update_by=?,last_update_date=NOW() WHERE inventory_id=?";
			$stmt2 = $this->dbconn->prepare($q2);
			$stmt2->bind_param('dii',$u1,$u2,$u3);
			$u1 = $this->total_on_order;
			$u2 = $_SESSION['dbuserid'];
			$u3 = $invid;
			$result = $stmt2->execute();
			$stmt2->close();
			if ($result!==false) return true;
			else return false;
		} else {
			$stmt1->close();
			return false;
		}
	} // purchasingPlaceOrder()
	public function purchasingChangeOrder($purdetailid,$entity1,$item1,$quantity1,$entity2,$item2,$quantity2) {
		// For a location or item change, $quantity1 should be negative and $quantity2 positive.
		// For a simple quantity change of the same location and item, $quantity1 should still contain a negative of the old number, and $quantity2 a positive of the new.
		// The delta will be calculated here.
		$invid1 = $this->getInventoryId($entity1,$item1);
		if (is_null($invid1)) {
			echo 'fail|Could not get or create the Entity Inventory ID. '.$this->dbconn->error;
			return false;
		}
		if (!empty($entity2) && !empty($item2)) $invid2 = $this->getInventoryId($entity2,$item2);
		else $invid2 = $invid1;
		$type = 'Q';
		if ($invid2 != $invid1)
			if ($entity1 != $entity2 && !empty($entity2)) $type = 'M';
			else $type = 'C';
		$q1 = "INSERT INTO inv_transactions (inv_transaction_type,reference_table,reference_key_int,inventory_id_1,inventory_id_2,quantity_on_order_delta_1,quantity_on_order_delta_2,
			created_by,creation_date,last_update_by,last_update_date) VALUES (?,'pur_detail',?,?,?,?,?,?,NOW(),?,NOW());";
		$stmt1 = $this->dbconn->prepare($q1);
		$stmt1->bind_param('siiiddii',$o1,$o2,$o3,$o4,$o5,$o6,$o7,$o8);
		$o1 = $type;
		$o2 = $purdetailid;
		$o3 = $invid1;
		$o4 = $invid2;
		$o5 = ($type=='Q')?$quantity2+$quantity1:$quantity1;
		$o6 = ($type!='Q')?$quantity2:null;
		$o7 = $o8 = $_SESSION['dbuserid'];
		$result1 = $stmt1->execute();
		if ($result1!==false) {
			$success = true;
			$stmt1->close();
			$this->display($invid1,'update');
			if ($type=='Q') $this->total_on_order += $quantity1 + $quantity2;
			else $this->total_on_order += $quantity1;
			$q2 = "UPDATE inv_master SET total_on_order=?,last_update_by=?,last_update_date=NOW() WHERE inventory_id=?";
			$stmt2 = $this->dbconn->prepare($q2);
			$stmt2->bind_param('dii',$u1,$u2,$u3);
			$u1 = $this->total_on_order;
			$u2 = $_SESSION['dbuserid'];
			$u3 = $invid1;
			$result = $stmt2->execute();
			$stmt2->close();
			if ($result===false) $success = false;
			if ($type!='Q' && !empty($quantity2)) {
				$this->display($invid2,'update');
				$this->total_on_order += $quantity2;
				$q3 = "UPDATE inv_master SET total_on_order=?,last_update_by=?,last_update_date=NOW() WHERE inventory_id=?";
				$stmt3 = $this->dbconn->prepare($q3);
				$stmt3->bind_param('dii',$u4,$u5,$u6);
				$u4 = $this->total_on_order;
				$u5 = $_SESSION['dbuserid'];
				$u6 = $invid2;
				$result = $stmt3->execute();
				$stmt3->close();
				if ($result===false) $success = false;
			}
			return $success;
		} else {
			$stmt1->close();
			return false;
		}
	} // purchasingChangeOrder()
	public function purchasingReceiveOrder($purdetailid,$entity,$item,$quantity) {
		// $quantity received should be a positive number.  It will be deducted from on_order, and added to on_hand.
		$invid = $this->getInventoryId($entity,$item);
		if (is_null($invid)) {
			echo 'fail|Could not get or create the Entity Inventory ID. '.$this->dbconn->error;
			return false;
		}
		$q1 = "INSERT INTO inv_transactions (inv_transaction_type,reference_table,reference_key_int,inventory_id_1,inventory_id_2,
			quantity_on_hand_delta_1,quantity_on_order_delta_1,created_by,creation_date,last_update_by,last_update_date)
			VALUES ('Q','pur_detail',?,?,?,?,?,?,NOW(),?,NOW());";
		$stmt1 = $this->dbconn->prepare($q1);
		if ($stmt1===false) {
			echo '|'.$this->dbconn->error;
			return false;
		}
		$stmt1->bind_param('iiiddii',$o1,$o2,$o3,$o4,$o5,$o6,$o7);
		$o1 = $purdetailid;
		$o2 = $o3 = $invid;
		$o4 = $quantity;
		$o5 = $quantity * -1;
		$o6 = $o7 = $_SESSION['dbuserid'];
		$result1 = $stmt1->execute();
		if ($result1!==false) {
			$stmt1->close();
			$this->display($invid,'update');
			$this->total_on_order -= $quantity;
			$this->total_on_hand += $quantity;
			$q2 = "UPDATE inv_master SET total_on_hand=?,total_on_order=?,last_update_by=?,last_update_date=NOW() WHERE inventory_id=?";
			$stmt2 = $this->dbconn->prepare($q2);
			$stmt2->bind_param('ddii',$u1,$u2,$u3,$u4);
			$u1 = $this->total_on_hand;
			$u2 = $this->total_on_order;
			$u3 = $_SESSION['dbuserid'];
			$u4 = $invid;
			$result = $stmt2->execute();
			$stmt2->close();
			if ($result!==false) return true;
			else return false;
		} else {
			echo '|'.$this->dbconn->error;
			$stmt1->close();
			return false;
		}
	} // purchasingReceiveOrder
	
	
	/***************************************************************
	 *** UI SUPPORT ************************************************
	 ***************************************************************/
	public function searchPage() {
		parent::abstractSearchPage('InventoryManagerSearch');
	} // searchPage()
	public function listRecords() {
		parent::abstractListRecords('InventoryManager');
	} // listRecords()
	public function executeSearch($criteria) {
		$q = 'SELECT inventory_id,inv.entity_id,entity_name,inv.product_id,product_description,total_on_hand,total_in_wip,total_on_order,total_reserved,total_unshipped_sold,total_shipped_sold
			FROM inv_master inv 
			JOIN ent_entities ent ON inv.entity_id=ent.entity_id
			JOIN cx_addresses addr ON ent.primary_address=addr.address_id
			JOIN item_master item ON inv.product_id=item.product_id';
		if (!is_null($criteria) && is_array($criteria) && count($criteria)>0) {
			if (is_array($criteria[0]) && count($criteria[0])>=2 && $criteria[0][0]=='unified_search') $criteria = $criteria[0][1];
			else $criteria='';
			$qc = " WHERE inv.product_id = ? OR product_code LIKE ? OR product_description LIKE ? OR product_catalog_title LIKE ? or gtin=?";
			$qc .= " OR inv.entity_id = ? OR entity_name LIKE ? OR street LIKE ? OR city LIKE ? OR spc_abbrev = ? OR postal_code LIKE ?";
			if (strpos($criteria,'<')!==false || strpos($criteria,'>')!==false) {
				$q .= " ORDER BY inventory_id;";
				$stmt = $this->dbconn->prepare($q);
				
			} else {
				$q .= $qc;
				$q .= " ORDER BY inventory_id;";
				$stmt = $this->dbconn->prepare($q);
				if ($stmt===false) {
					echo $this->dbconn->error;
					return;
				}
				$stmt->bind_param('issssisssss',$i2,$i3,$i4,$i5,$i6,$e1,$e2,$e3,$e4,$e5,$e6);
				$i2 = $e1 = ctype_digit($criteria)?$criteria:-99999;
				$i6 = $e5 = $criteria;
				$i3 = $i4 = $i5 = $e2 = $e3 = $e4 = $e6 = '%'.$criteria.'%';
			}
			$result = $stmt->execute();
			if ($result !== false) {
				$this->recordSet = array();
				if (isset($_SESSION['recordSet']['InventoryManager'])) unset($_SESSION['recordSet']['InventoryManager']); // A search criteria was given, so do not display the last search on an empty set.
				$stmt->store_result();
				$vendorname='';
				$stmt->bind_result($this->inventory_id,$this->entity_id,$entname,$this->product_id,$prodname,$this->total_on_hand,$this->total_in_wip,$this->total_on_order,
					$this->total_reserved,$this->total_unshipped_sold,$this->total_shipped_sold);
				while ($stmt->fetch()) {
					$this->recordSet[$this->inventory_id] = array('Entity'=>$entname,'Item'=>$prodname,'QOH'=>$this->total_on_hand,
						'WIP'=>$this->total_in_wip,'PUR'=>$this->total_on_order,'RES'=>$this->total_reserved,'ORD'=>$this->total_unshipped_sold,
						'INV'=>$this->total_shipped_sold);
				}
			}
		} else {
			$q .= ' ORDER BY inventory_id;';
			$result = $this->dbconn->query($q);
					if ($result!==false) {
				$this->recordSet = array();
				while ($row=$result->fetch_assoc()) {
					$this->recordSet[$row['inventory_id']] = array('Entity'=>$row['entity_name'],'Item'=>$row['product_description'],'QOH'=>$row['total_on_hand'],
						'WIP'=>$row['total_in_wip'],'PUR'=>$row['total_on_order'],'RES'=>$row['total_reserved'],'ORD'=>$row['total_unshipped_sold'],
						'INV'=>$row['total_shipped_sold']);
				} // while rows
			} // if query succeeded
		} // if criteria does not exist
		$this->listRecords();
		$_SESSION['currentScreen'] = 1018;
		$_SESSION['lastCriteria'] = $criteria;
		if (!isset($_SESSION['searchResults'])) $_SESSION['searchResults'] = array();
		$_SESSION['searchResults']['InventoryManager'] = array_keys($this->recordSet);		
	} // executeSearch()
	public function isIDValid($id) {
		// TODO: Validate that the ID is actually a record in the database
		if ($id<1) return false;
		if (is_integer($id)) return true;
		if (ctype_digit($id)) return true;
		return false;
	} // function isIDValid()
	public function display($id,$mode='view') {
		$readonly = true;
		$html = '';
		$q = 'SELECT entity_id,product_id,total_on_hand,total_in_wip,total_on_order,total_reserved,total_unshipped_sold,total_shipped_sold,
				created_by,creation_date,last_update_by,last_update_date
				FROM inv_master
				WHERE inventory_id=?;';
		$stmt = $this->dbconn->prepare($q);
		if ($stmt===false) {
			echo 'fail|'.$this->dbconn->error;
			return;
		}
		$stmt->bind_param('i',$p1);
		$p1 = $id;
		$result = $stmt->execute();
		if ($result!==false) {
			$this->inventory_id = $id;
			$stmt->bind_result($this->entity_id,$this->product_id,$this->total_on_hand,$this->total_in_wip,$this->total_on_order,$this->total_reserved,
				$this->total_unshipped_sold,$this->total_shipped_sold,$this->inv_created_by,$this->inv_creation_date,
				$this->inv_last_update_by,$this->inv_last_update_date);
			$stmt->store_result();
			$stmt->fetch();
			$this->currentRecord = $id;
			$stmt->close();
			
			$q = 'SELECT inv_transaction_id,inv_transaction_type,reference_table,COALESCE(reference_key_int,reference_key_char) AS reference_key,
				quantity_on_hand_delta_1 AS on_hand_delta,quantity_in_wip_delta_1 AS in_wip_delta,
				quantity_on_order_delta_1 AS on_order_delta,quantity_reserved_delta_1 AS reserved_delta,
				quantity_unshipped_delta_1 AS unshipped_delta,quantity_shipped_delta_1 AS shipped_delta
				FROM inv_transactions
				WHERE inventory_id_1=?
				UNION ALL
				SELECT inv_transaction_id,inv_transaction_type,reference_table,COALESCE(reference_key_int,reference_key_char) AS reference_key,
				quantity_on_hand_delta_2 AS on_hand_delta,quantity_in_wip_delta_2 AS in_wip_delta,
				quantity_on_order_delta_2 AS on_order_delta,quantity_reserved_delta_2 AS reserved_delta,
				quantity_unshipped_delta_2 AS unshipped_delta,quantity_shipped_delta_2 AS shipped_delta
				FROM inv_transactions
				WHERE inventory_id_2=? AND inventory_id_1<>inventory_id_2';
			$stmt = $this->dbconn->prepare($q);
			if ($stmt===false) {
				echo 'fail|'.$this->dbconn->error;
				return;
			}
			$stmt->bind_param('ii',$t1,$t2);
			$t1 = $t2 = $id;
			$result = $stmt->execute();
			$dtl_array = array();
			if ($result!==false) {
				$stmt->bind_result(
					$txid,
					$txtype,
					$reftable,
					$refkey,
					$qoh,
					$wip,
					$ord,
					$reserved,
					$unshipped,
					$shipped
				);
				$stmt->store_result();
				while ($stmt->fetch()) {
					$dtl_array[$txid] = array(
						'inv_transaction_id'=>$txid
						,'inv_transaction_type'=>$txtype
						,'reference_table'=>$reftable
						,'reference_key'=>$refkey
						,'on_hand_delta'=>$qoh
						,'in_wip_delta'=>$wip
						,'on_order_delta'=>$ord
						,'reserved_delta'=>$reserved
						,'unshipped_delta'=>$unshipped
						,'shipped_delta'=>$shipped
					);
				}
				$stmt->close();
			}
			if ($mode!='update') {
				$hdata = $this->arrayifyEntityInventory();
				echo parent::abstractRecord($mode,'InventoryManager','',$hdata,$dtl_array);
			}
		} else $this->inventory_id = null;
		if ($mode!='update') {
			$_SESSION['currentScreen'] = 2018;
			if (!isset($_SESSION['searchResults']) && !isset($_SESSION['searchResults']['InventoryManager']))
				$_SESSION['idarray'] = array(0,0,$id,0,0);
			else {
				$idloc = array_search($id,$_SESSION['searchResults']['InventoryManager'],false);
				$f = $_SESSION['searchResults']['InventoryManager'][0];
				$l = $_SESSION['searchResults']['InventoryManager'][] = array_pop($_SESSION['searchResults']['InventoryManager']); // https://stackoverflow.com/questions/3687358/whats-the-best-way-to-get-the-last-element-of-an-array-without-deleting-it#comment63556865_3687358
				if ($idloc > 0) $p = $_SESSION['searchResults']['InventoryManager'][$idloc-1]; else $p = $f;
				if ($l != $id) $n = $_SESSION['searchResults']['InventoryManager'][$idloc+1]; else $n = $l;
				$_SESSION['idarray'] = array($f,$p,$id,$n,$l);
			}		
		}
	} // display()
} // class InventoryManager
?>