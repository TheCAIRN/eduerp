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
		$this->searchFields[] = array('inv_master','entity_id','Entity','dropdown','ent_entities',array('entity_id','entity_name'));
		$this->searchFields[] = array('inv_master','product_id','Item','Item');
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
			'creation_date'=>$this->inv_creation_date,'last_update_by'=>$this->inv_last_update_by,'last_update_date'=>$this->inv_last_update_date);		
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
		$q = 'SELECT inventory_id FROM inv_master WHERE entity_id=? AND product_id=? AND variant_code=?';
		$stmt = $this->dbconn->prepare($q);
		$stmt->bind_param('iis',$p1,$p2,$p3);
		$p1 = $entity;
		$p2 = $item;
		$p3 = $variant;
		$result = $stmt->execute();
		if ($result!==false) {
			if ($stmt->num_row==0) {
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
					$this->inv_last_update_By = $_SESSION['dbuserid'];
					$stmt->close();
					return $this->inventory_id;
				} else {
					$stmt->close();
					return null;
				}
			} else {
				$this->resetEntityInventory();
				$stmt->bind_result($this->inventory_id);
				$stmt->store_result();
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
		if (is_null($invid)) return false;
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
			$this->quantity_on_order += $quantity;
			$q2 = "UPDATE inv_master SET quantity_on_order=?,last_update_by=?,last_update_date=NOW() WHERE inventory_id=?";
			$stmt2 = $this->dbconn->prepare($q2);
			$stmt2->bind_param('dii',$u1,$u2,$u3);
			$u1 = $this->quantity_on_order;
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
		// For a simple quantity change of the same location and item, $quantity1 should be the delta and $quantity2 null.
		$invid1 = $this->getInventoryId($entity1,$item1);
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
		$o5 = $quantity1;
		$o6 = ($type!='Q')?$quantity2:null;
		$o7 = $o8 = $_SESSION['dbuserid'];
		$result1 = $stmt1->execute();
		if ($result1!==false) {
			$success = true;
			$stmt1->close();
			$this->display($invid1,'update');
			$this->quantity_on_order += $quantity1;
			$q2 = "UPDATE inv_master SET quantity_on_order=?,last_update_by=?,last_update_date=NOW() WHERE inventory_id=?";
			$stmt2 = $this->dbconn->prepare($q2);
			$stmt2->bind_param('dii',$u1,$u2,$u3);
			$u1 = $this->quantity_on_order;
			$u2 = $_SESSION['dbuserid'];
			$u3 = $invid1;
			$result = $stmt2->execute();
			$stmt2->close();
			if ($result===false) $success = false;
			if ($type!='Q' && !empty($quantity2)) {
				$this->display($invid2,'update');
				$this->quantity_on_order += $quantity2;
				$q3 = "UPDATE inv_master SET quantity_on_order=?,last_update_by=?,last_update_date=NOW() WHERE inventory_id=?";
				$stmt3 = $this->dbconn->prepare($q3);
				$stmt3->bind_param('dii',$u4,$u5,$u6);
				$u4 = $this->quantity_on_order;
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
		if (is_null($invid)) return false;
		$q1 = "INSERT INTO inv_transactions (inv_transaction_type,reference_table,reference_key_int,inventory_id_1,
			quantity_on_hand_delta_1,quantity_on_order_delta_1,created_by,creation_date,last_update_by,last_update_date)
			VALUES ('Q','pur_detail',?,?,?,?,?,NOW(),?,NOW());";
		$stmt1 = $this->dbconn->prepare($q1);
		$stmt1->bind_param('iiddii',$o1,$o2,$o3,$o4,$o5,$o6);
		$o1 = $purdetailid;
		$o2 = $invid;
		$o3 = $quantity;
		$o4 = $quantity * -1;
		$o5 = $o6 = $_SESSION['dbuserid'];
		$result1 = $stmt1->execute();
		if ($result1!==false) {
			$stmt1->close();
			$this->display($invid,'update');
			$this->quantity_on_order -= $quantity;
			$this->quantity_on_hand += $quantity;
			$q2 = "UPDATE inv_master SET quantity_on_hand=?,quantity_on_order=?,last_update_by=?,last_update_date=NOW() WHERE inventory_id=?";
			$stmt2 = $this->dbconn->prepare($q2);
			$stmt2->bind_param('ddii',$u1,$u2,$u3,$u4);
			$u1 = $this->quantity_on_hand;
			$u2 = $this->quantity_on_order;
			$u3 = $_SESSION['dbuserid'];
			$u4 = $invid;
			$result = $stmt2->execute();
			$stmt2->close();
			if ($result!==false) return true;
			else return false;
		} else {
			$stmt1->close();
			return false;
		}
	} // purchasingReceiveOrder
	
	
	/***************************************************************
	 *** UI SUPPORT ************************************************
	 ***************************************************************/
	public function searchPage() {
		
	} // searchPage()
	public function listRecords() {
		
	} // listRecords()
	public function executeSearch($criteria) {
		
	} // executeSearch()
	public function display($id,$mode='view') {
		
	} // display()
} // class InventoryManager
?>