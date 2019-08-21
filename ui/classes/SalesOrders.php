<?php
class SalesOrders extends ERPBase {
	private $sales_order_number;
	private $parent;
	private $sales_order_type;
	private $sales_order_status;
	private $customer_id;
	private $buyer;
	private $seller;
	private $entity_id;
	private $division_id;
	private $department_id;
	private $inventory_entity;
	private $currency_code;
	private $visible;
	private $rev_enabled;
	private $rev_number;
	private $quote_number;
	private $quote_approved_by;
	private $quote_given_date;
	private $quote_expires_date;
	private $customer_purchase_order_number;
	private $customer_department;
	private $customer_product_group;
	private $store_code;
	private $terms;
	private $order_date;
	private $credit_release_date;
	private $ship_window_start;
	private $ship_window_end;
	private $must_route_by;
	private $must_arrive_by;
	private $order_cancelled_date;
	private $wave_number;
	private $wave_date;
	private $inventory_needed_by;
	private $inventory_pulled_complete;
	private $inventory_packed_complete;
	private $fv_vendor_id;
	private $bill_of_lading;
	private $rrc;
	private $load_id;
	private $routing_requested;
	private $pickup_scheduled_for;
	private $inventory_loaded_complete;
	private $bol_date;
	private $order_shipped_date;
	private $invoice_number;
	private $order_invoiced_date;
	private $invoice_paid_complete;
	private $shipping_from;
	private $shipping_to;
	private $remit_to;
	private	$huser_creation;
	private $hdate_creation;
	private $huser_modify;
	private $hdate_modify;
	
	private $sales_order_line;
	private $parent_line;
	private $dentity_id;
	private $ddivision_id;
	private $ddepartment_id;
	private $customer_line;
	private $edi_raw1;
	private $edi_raw2;
	private $item_id;
	private $quantity_requested;
	private $quantity_shipped;
	private $quantity_returned;
	private $quantity_backordered;
	private $quantity_cancelled;
	private $quantity_uom;
	private $price;
	private $discount_percent;
	private $discount_amount;
	private $retail_high;
	private $retail_low;
	private $dcredit_release_date;
	private $dwave_date;
	private $assigned_to;
	private $dinventory_needed_by;
	private $dinventory_location;
	private $dinventory_pulled;
	private $dinventory_pulled_by;
	private $dinventory_packed;
	private $dinventory_packed_by;
	private $dinventory_loaded;
	private $dinventory_loaded_by;
	private $line_shipped_date;
	private $line_invoiced_date;
	private $line_cancelled_date;
	private $dvisible;
	private $drev_enabled;
	private $drev_number;
	private	$duser_creation;
	private $ddate_creation;
	private $duser_modify;
	private $ddate_modify;
	private $detail_array;
	
	private $column_list_header = 'sales_order_number,parent,sales_order_type,sales_order_status,customer_id,buyer,seller,entity_id,division_id,department_id,
		inventory_entity,currency_code,visible,rev_enabled,rev_number,quote_number,quote_approved_by,quote_given_date,quote_expires_date,
		customer_purchase_order_number,customer_department,customer_product_group,store_code,terms,order_date,credit_release_date,ship_window_start,ship_window_end,
		must_route_by,must_arrive_by,order_cancelled_date,wave_number,wave_date,inventory_needed_by,inventory_pulled_complete,inventory_packed_complete,
		fv_vendor_id,bill_of_lading,rrc,load_id,routing_requested,pickup_scheduled_for,inventory_loaded_complete,bol_date,order_shipped_date,
		invoice_number,order_invoiced_date,invoice_paid_complete,shipping_from,shipping_to,remit_to';
	private $column_list_detail = 'sales_order_number,sales_order_line,parent_line,entity_id,division_id,department_id,customer_line,edi_raw1,edi_raw2,item_id,
		quantity_requested,quantity_shipped,quantity_returned,quantity_backordered,quantity_cancelled,quantity_uom,price,discount_percent,discount_amount,
		retail_high,retail_low,credit_release_date,wave_date,assigned_to,inventory_needed_by,inventory_location,inventory_pulled,inventory_pulled_by,inventory_packed,
		inventory_packed_by,inventory_loaded,inventory_loaded_by,line_shipped_date,line_invoiced_date,line_cancelled_date,visible,rev_enabled,rev_number';
	
	public function __construct ($link=null) {
		parent::__construct($link);
		$this->supportsNotes = 'sales_header_notes';
		$this->supportsAttachments = false;
		$this->primaryKey = 'sales_order_number';
		$this->searchFields[] = array('sales_header','sales_order_number','Sales Order #','integer');
		$this->searchFields[] = array('sales_header','customer_purchase_order_number','PO #','textbox');
		$this->searchFields[] = array('sales_header','bill_of_lading','BOL #','textbox');
		$this->searchFields[] = array('sales_header','wave_number','Wave #','textbox');
		$this->searchFields[] = array('sales_header','invoice_number','Invoice #','integer');
		
		$this->entryFields[] = array('sales_header','','Header','fieldset');
		$this->entryFields[] = array('sales_header','','Sales Order','fieldset');
		$this->entryFields[] = array('sales_header','sales_order_number','Sales Order #','integerid');
		$this->entryFields[] = array('sales_header','parent','Parent Order #','integer');
		$this->entryFields[] = array('sales_header','sales_order_type','Order Type','dropdown','sales_order_types',array('sales_order_type','description'));
		$this->entryFields[] = array('sales_header','sales_order_status','Status','function',$this,'statusSelect');
		$this->entryFields[] = array('sales_header','customer_id','Customer','dropdown','cust_master',array('customer_id','customer_name'));
		$this->entryFields[] = array('sales_header','buyer','Buyer','dropdown','v_cust_contacts',array('human_id','contact_name'),'customer_id');
		$this->entryFields[] = array('sales_header','seller','Seller','dropdown','v_sec_users',array('user_id','human_name'));
		$this->entryFields[] = array('sales_header','entity_id','Entity','dropdown','ent_entities',array('entity_id','entity_name'));
		$this->entryFields[] = array('sales_header','division_id','Division','dropdown','ent_division_master',array('division_id','division_name'));
		$this->entryFields[] = array('sales_header','department_id','Department','dropdown','ent_department_master',array('department_id','department_name'));
		$this->entryFields[] = array('sales_header','inventory_entity','Inventory Entity','dropdown','ent_entities',array('entity_id','entity_name'),'INV');
		$this->entryFields[] = array('sales_header','currency_code','Currency','dropdown','aa_currency',array('code','code'),'USD');
		$this->entryFields[] = array('sales_header','visible','Visible','checkbox',null,true);
		$this->entryFields[] = array('sales_header','rev_enabled','Enable Revision Tracking','checkbox','rev_number',false);
		$this->entryFields[] = array('sales_header','rev_number','Revision number','integer');
		$this->entryFields[] = array('sales_header','','','endfieldset');
		$this->entryFields[] = array('sales_header','-quote','Pre-sales','fieldset');
		$this->entryFields[] = array('sales_header','quote_number','Quote #','textbox');
		$this->entryFields[] = array('sales_header','quote_approved_by','Quote approved by','dropdown','v_sec_users',array('user_id','human_name'));
		$this->entryFields[] = array('sales_header','quote_given_date','Quote Given','date');
		$this->entryFields[] = array('sales_header','quote_expires_date','Quote Expires','date');
		$this->entryFields[] = array('sales_header','','','endfieldset');
		$this->entryFields[] = array('sales_header','-ordered','Order confirmation','fieldset');
		$this->entryFields[] = array('sales_header','customer_purchase_order_number','Customer PO #','textbox');
		$this->entryFields[] = array('sales_header','customer_department','Customer Dept','textbox');
		$this->entryFields[] = array('sales_header','customer_product_group','Product Group','textbox');
		$this->entryFields[] = array('sales_header','store_code','Store','dropdown','cust_stores',array('store_code','store_name'),'customer_id');
		$this->entryFields[] = array('sales_header','terms','Terms','dropdown','aa_terms',array('terms_id','terms_code'));
		$this->entryFields[] = array('sales_header','order_date','Order Date','date');
		$this->entryFields[] = array('sales_header','credit_release_date','Credit Released','date');
		$this->entryFields[] = array('sales_header','ship_window_start','Start Date','date');
		$this->entryFields[] = array('sales_header','ship_window_end','End Date','date');
		$this->entryFields[] = array('sales_header','must_route_by','Must route by','date');
		$this->entryFields[] = array('sales_header','must_arrive_by','Must arrive by','date');
		$this->entryFields[] = array('sales_header','order_cancelled_date','Order Cancelled','date');
		$this->entryFields[] = array('sales_header','','','endfieldset');
		$this->entryFields[] = array('sales_header','-processing','Processing','fieldset');
		$this->entryFields[] = array('sales_header','wave_number','Wave #','integer');
		$this->entryFields[] = array('sales_header','wave_date','Wave date','date');
		$this->entryFields[] = array('sales_header','inventory_needed_by','Inventory needed by','datetime');
		$this->entryFields[] = array('sales_header','inventory_pulled_complete','Inventory Pulled','datetime');
		$this->entryFields[] = array('sales_header','inventory_packed_complete','Inventory Packed','datetime');
		$this->entryFields[] = array('sales_header','','','endfieldset');
		$this->entryFields[] = array('sales_header','-shipping','Shipping','fieldset');
		$this->entryFields[] = array('sales_header','fv_vendor_id','Shipper','dropdown','fv_freight_vendors',array('fv_vendor_id','fv_vendor_name'));
		$this->entryFields[] = array('sales_header','bill_of_lading','BOL','textbox');
		$this->entryFields[] = array('sales_header','rrc','Routing request #','textbox');
		$this->entryFields[] = array('sales_header','load_id','Load ID','textbox');
		$this->entryFields[] = array('sales_header','routing_requested','Routing Requested','datetime');
		$this->entryFields[] = array('sales_header','pickup_scheduled_for','Pickup Scheduled For','datetime');
		$this->entryFields[] = array('sales_header','inventory_loaded_complete','Inventory Loaded','datetime');
		$this->entryFields[] = array('sales_header','bol_date','BOL Date','date');
		$this->entryFields[] = array('sales_header','order_shipped_date','Order Shipped','date');
		$this->entryFields[] = array('sales_header','','','endfieldset');
		$this->entryFields[] = array('sales_header','-invoicing','Invoicing','fieldset');
		$this->entryFields[] = array('sales_header','invoice_number','Invoice #','integer');
		$this->entryFields[] = array('sales_header','order_invoiced_date','Order Invoiced','date');
		$this->entryFields[] = array('sales_header','invoice_paid_complete','Invoice Paid','date');
		$this->entryFields[] = array('sales_header','','','endfieldset');
		$this->entryFields[] = array('sales_header','shipping_from','Ship From','embedded');
		$this->entryFields[] = array('sales_header','shipping_from','Ship From','Address');
		$this->entryFields[] = array('sales_header','','','endembedded');
		$this->entryFields[] = array('sales_header','shipping_to','Ship To','embedded');
		$this->entryFields[] = array('sales_header','shipping_to','Ship To','Address');
		$this->entryFields[] = array('sales_header','','','endembedded');
		$this->entryFields[] = array('sales_header','remit_to','Remit To','embedded');
		$this->entryFields[] = array('sales_header','remit_to','Remit To','Address');
		$this->entryFields[] = array('sales_header','','','endembedded');
		$this->entryFields[] = array('sales_header','','','endfieldset');
		
		$this->entryFields[] = array('sales_detail','','Detail','fieldset');
		$this->entryFields[] = array('sales_detail','','Sales Order Detail','fieldtable');
		$this->entryFields[] = array('sales_detail','sales_order_line','Line #','integer');
		$this->entryFields[] = array('sales_detail','parent_line','Parent','integer');
		$this->entryFields[] = array('sales_detail','dentity_id','Entity','dropdown','ent_entities',array('entity_id','entity_name'));
		$this->entryFields[] = array('sales_detail','ddivision_id','Division','dropdown','ent_division_master',array('division_id','division_name'));
		$this->entryFields[] = array('sales_detail','ddepartment_id','Department','dropdown','ent_department_master',array('department_id','department_name'));
		$this->entryFields[] = array('sales_detail','customer_line','Customer Line #','textbox');
		$this->entryFields[] = array('sales_detail','','Item','embedded');
		$this->entryFields[] = array('sales_detail','item_id','Item','Item');
		$this->entryFields[] = array('sales_detail','','','endembedded');
		$this->entryFields[] = array('sales_detail','quantity_requested','Qty Req','decimal',9,5);
		$this->entryFields[] = array('sales_detail','quantity_shipped','Qty Ship','decimal',9,5);
		$this->entryFields[] = array('sales_detail','quantity_returned','Qty Rtn','decimal',9,5);
		$this->entryFields[] = array('sales_detail','quantity_backordered','Qty BO','decimal',9,5);
		$this->entryFields[] = array('sales_detail','quantity_cancelled','Qty Xcl','decimal',9,5);
		$this->entryFields[] = array('sales_detail','quantity_uom','Quantity UOM','dropdown','aa_uom',array('uom_code','uom_description'));
		$this->entryFields[] = array('sales_detail','price','Price','decimal',17,5);
		$this->entryFields[] = array('sales_detail','discount_percent','Disc %','decimal',7,3);
		$this->entryFields[] = array('sales_detail','discount_amount','Disc $','decimal',17,5);
		$this->entryFields[] = array('sales_detail','retail_high','Retail H','decimal',17,5);
		$this->entryFields[] = array('sales_detail','retail_low','Retail L','decimal',17,5);
		$this->entryFields[] = array('sales_detail','dcredit_release_date','Credit Released','date');
		$this->entryFields[] = array('sales_detail','dwave_date','Wave date','date');
		$this->entryFields[] = array('sales_detail','assigned_to','Assigned','dropdown','v_sec_users',array('user_id','human_name'));
		$this->entryFields[] = array('sales_detail','dinventory_needed_by','Inventory needed by','datetime');
		$this->entryFields[] = array('sales_detail','inventory_location','Inventory Location','dropdown','ent_entities',array('entity_id','entity_name'),'INV');
		$this->entryFields[] = array('sales_detail','dinventory_pulled_complete','Inventory Pulled','datetime');
		$this->entryFields[] = array('sales_detail','dinventory_pulled_by','Inv pulled by','dropdown','v_sec_users',array('user_id','human_name'));
		$this->entryFields[] = array('sales_detail','dinventory_packed_complete','Inventory Packed','datetime');
		$this->entryFields[] = array('sales_detail','dinventory_packed_by','Inv packed by','dropdown','v_sec_users',array('user_id','human_name'));
		$this->entryFields[] = array('sales_detail','dinventory_loaded_complete','Inventory Loaded','datetime');
		$this->entryFields[] = array('sales_detail','dinventory_loaded_by','Inv loaded by','dropdown','v_sec_users',array('user_id','human_name'));
		$this->entryFields[] = array('sales_detail','line_shipped_date','Line shipped','date');
		$this->entryFields[] = array('sales_detail','line_invoiced_date','Line invoiced','date');
		$this->entryFields[] = array('sales_detail','line_cancelled_date','Line cancelled','date');
		$this->entryFields[] = array('sales_detail','dvisible','Visible','checkbox',null,true);
		$this->entryFields[] = array('sales_detail','drev_enabled','Enable Revision Tracking','checkbox','rev_number',false);
		$this->entryFields[] = array('sales_detail','drev_number','Revision number','integer');
		$this->entryFields[] = array('sales_detail','','Add Row','newlinebutton','newSalesOrdersDetailRow();');
		$this->entryFields[] = array('sales_detail','','','endfieldtable');
		$this->entryFields[] = array('sales_detail','','','endfieldset');
		$this->resetHeader();
	} // __construct
	public function resetHeader() {
		$this->sales_order_number = null;
		$this->parent = null;
		$this->sales_order_type = null;
		$this->sales_order_status = null;
		$this->customer_id = null;
		$this->buyer = null;
		$this->seller = null;
		$this->entity_id = null;
		$this->division_id = null;
		$this->department_id = null;
		$this->inventory_entity = null;
		$this->currency_code = null;
		$this->visible = null;
		$this->rev_enabled = null;
		$this->rev_number = null;
		$this->quote_number = null;
		$this->quote_approved_by = null;
		$this->quote_given_date = null;
		$this->quote_expires_date = null;
		$this->customer_purchase_order_number = null;
		$this->customer_department = null;
		$this->customer_product_group = null;
		$this->store_code = null;
		$this->terms = null;
		$this->order_date = null;
		$this->credit_release_date = null;
		$this->ship_window_start = null;
		$this->ship_window_end = null;
		$this->must_route_by = null;
		$this->must_arrive_by = null;
		$this->order_cancelled_date = null;
		$this->wave_number = null;
		$this->wave_date = null;
		$this->inventory_needed_by = null;
		$this->inventory_pulled_complete = null;
		$this->inventory_packed_complete = null;
		$this->fv_vendor_id = null;
		$this->bill_of_lading = null;
		$this->rrc = null;
		$this->load_id = null;
		$this->routing_requested = null;
		$this->pickup_scheduled_for = null;
		$this->inventory_loaded_complete = null;
		$this->bol_date = null;
		$this->order_shipped_date = null;
		$this->invoice_number = null;
		$this->order_invoiced_date = null;
		$this->invoice_paid_complete = null;
		$this->shipping_from = null;
		$this->shipping_to = null;
		$this->remit_to = null;
		$this->detail_array = array();
	} // resetHeader()
	public function resetDetail() {
		$this->sales_order_line = null;
		$this->parent_line = null;
		$this->dentity_id = null;
		$this->ddivision_id = null;
		$this->ddepartment_id = null;
		$this->customer_line = null;
		$this->edi_raw1 = null;
		$this->edi_raw2 = null;
		$this->item_id = null;
		$this->quantity_requested = null;
		$this->quantity_shipped = null;
		$this->quantity_returned = null;
		$this->quantity_backordered = null;
		$this->quantity_cancelled = null;
		$this->quantity_uom = null;
		$this->price = null;
		$this->discount_percent = null;
		$this->discount_amount = null;
		$this->retail_high = null;
		$this->retail_low = null;
		$this->dcredit_release_date = null;
		$this->dwave_date = null;
		$this->assigned_to = null;
		$this->dinventory_needed_by = null;
		$this->dinventory_location = null;
		$this->dinventory_pulled = null;
		$this->dinventory_pulled_by = null;
		$this->dinventory_packed = null;
		$this->dinventory_packed_by = null;
		$this->dinventory_loaded = null;
		$this->dinventory_loaded_by = null;
		$this->line_shipped_date = null;
		$this->line_invoiced_date = null;
		$this->line_cancelled_date = null;
		$this->dvisible = null;
		$this->drev_enabled = null;
		$this->drev_number = null;
		$this->duser_creation = null;
		$this->ddate_creation = null;
		$this->duser_modify = null;
		$this->ddate_modify = null;
	} // resetDetail()
	public function arrayifyHeader() {
		return array(
			'sales_order_number'=>$this->sales_order_number,
			'parent'=>$this->parent,
			'sales_order_type'=>$this->sales_order_type,
			'sales_order_status'=>$this->sales_order_status,
			'customer_id'=>$this->customer_id,
			'buyer'=>$this->buyer,
			'seller'=>$this->seller,
			'entity_id'=>$this->entity_id,
			'division_id'=>$this->division_id,
			'department_id'=>$this->department_id,
			'inventory_entity'=>$this->inventory_entity,
			'currency_code'=>$this->currency_code,
			'visible'=>$this->visible,
			'rev_enabled'=>$this->rev_enabled,
			'rev_number'=>$this->rev_number,
			'quote_number'=>$this->quote_number,
			'quote_approved_by'=>$this->quote_approved_by,
			'quote_given_date'=>$this->quote_given_date,
			'quote_expires_date'=>$this->quote_expires_date,
			'customer_purchase_order_number'=>$this->customer_purchase_order_number,
			'customer_department'=>$this->customer_department,
			'customer_product_group'=>$this->customer_product_group,
			'store_code'=>$this->store_code,
			'terms'=>$this->terms,
			'order_date'=>$this->order_date,
			'credit_release_date'=>$this->credit_release_date,
			'ship_window_start'=>$this->ship_window_start,
			'ship_window_end'=>$this->ship_window_end,
			'must_route_by'=>$this->must_route_by,
			'must_arrive_by'=>$this->must_arrive_by,
			'order_cancelled_date'=>$this->order_cancelled_date,
			'wave_number'=>$this->wave_number,
			'wave_date'=>$this->wave_date,
			'inventory_needed_by'=>$this->inventory_needed_by,
			'inventory_pulled_complete'=>$this->inventory_pulled_complete,
			'inventory_packed_complete'=>$this->inventory_packed_complete,
			'fv_vendor_id'=>$this->fv_vendor_id,
			'bill_of_lading'=>$this->bill_of_lading,
			'rrc'=>$this->rrc,
			'load_id'=>$this->load_id,
			'routing_requested'=>$this->routing_requested,
			'pickup_scheduled_for'=>$this->pickup_scheduled_for,
			'inventory_loaded_complete'=>$this->inventory_loaded_complete,
			'bol_date'=>$this->bol_date,
			'order_shipped_date'=>$this->order_shipped_date,
			'invoice_number'=>$this->invoice_number,
			'order_invoiced_date'=>$this->order_invoiced_date,
			'invoice_paid_complete'=>$this->invoice_paid_complete,
			'shipping_from'=>$this->shipping_from,
			'shipping_to'=>$this->shipping_to,
			'remit_to'=>$this->remit_to,
			'huser_creation'=>$this->huser_creation,
			'hdate_creation'=>$this->hdate_creation,
			'huser_modify'=>$this->huser_modify,
			'hdate_modify'=>$this->hdate_modify
		);
	} // arrayifyHeader
	public function arrayifyDetail() {
		return array(
			'sales_order_number'=>$this->sales_order_number,
			'sales_order_line'=>$this->sales_order_line,
			'parent_line'=>$this->parent_line,
			'entity_id'=>$this->dentity_id,
			'division_id'=>$this->ddivision_id,
			'department_id'=>$this->ddepartment_id,
			'customer_line'=>$this->customer_line,
			'edi_raw1'=>$this->edi_raw1,
			'edi_raw2'=>$this->edi_raw2,
			'item_id'=>$this->item_id,
			'quantity_requested'=>$this->quantity_requested,
			'quantity_shipped'=>$this->quantity_shipped,
			'quantity_returned'=>$this->quantity_returned,
			'quantity_backordered'=>$this->quantity_backordered,
			'quantity_cancelled'=>$this->quantity_cancelled,
			'quantity_uom'=>$this->quantity_uom,
			'price'=>$this->price,
			'discount_percent'=>$this->discount_percent,
			'discount_amount'=>$this->discount_amount,
			'retail_high'=>$this->retail_high,
			'retail_low'=>$this->retail_low,
			'credit_release_date'=>$this->dcredit_release_date,
			'wave_date'=>$this->dwave_date,
			'assigned_to'=>$this->assigned_to,
			'inventory_needed_by'=>$this->dinventory_needed_by,
			'inventory_location'=>$this->dinventory_location,
			'inventory_pulled'=>$this->dinventory_pulled,
			'inventory_pulled_by'=>$this->dinventory_pulled_by,
			'inventory_packed'=>$this->dinventory_packed,
			'inventory_packed_by'=>$this->dinventory_packed_by,
			'inventory_loaded'=>$this->dinventory_loaded,
			'inventory_loaded_by'=>$this->dinventory_loaded_by,
			'line_shipped_date'=>$this->line_shipped_date,
			'line_invoiced_date'=>$this->line_invoiced_date,
			'line_cancelled_date'=>$this->line_cancelled_date,
			'visible'=>$this->dvisible,
			'rev_enabled'=>$this->drev_enabled,
			'rev_number'=>$this->drev_number,
			'duser_creation'=>$this->duser_creation,
			'ddate_creation'=>$this->ddate_creation,
			'duser_modify'=>$this->duser_modify,
			'ddate_modify'=>$this->ddate_modify
		);
	} // arrayifyDetail
	private function unarrayifyDetail($index) {
		if (!is_array($this->detail_array)) return false;
		if (!isset($this->detail_array[$index])) return false;
		$rec = $this->detail_array[$index];
		$this->sales_order_line = $rec['sales_order_line'];
		$this->parent_line = $rec['parent_line'];
		$this->dentity_id = $rec['entity_id'];
		$this->ddivision_id = $rec['division_id'];
		$this->ddepartment_id = $rec['department_id'];
		$this->customer_line = $rec['customer_line'];
		$this->edi_raw1 = $rec['edi_raw1'];
		$this->edi_raw2 = $rec['edi_raw2'];
		$this->item_id = $rec['item_id'];
		$this->quantity_requested = $rec['quantity_requested'];
		$this->quantity_shipped = $rec['quantity_shipped'];
		$this->quantity_returned = $rec['quantity_returned'];
		$this->quantity_backordered = $rec['quantity_backordered'];
		$this->quantity_cancelled = $rec['quantity_cancelled'];
		$this->quantity_uom = $rec['quantity_uom'];
		$this->price = $rec['price'];
		$this->discount_percent = $rec['discount_percent'];
		$this->discount_amount = $rec['discount_amount'];
		$this->retail_high = $rec['retail_high'];
		$this->retail_low = $rec['retail_low'];
		$this->dcredit_release_date = $rec['credit_release_date'];
		$this->dwave_date = $rec['wave_date'];
		$this->assigned_to = $rec['assigned_to'];
		$this->dinventory_needed_by = $rec['inventory_needed_by'];
		$this->dinventory_location = $rec['inventory_location'];
		$this->dinventory_pulled = $rec['inventory_pulled'];
		$this->dinventory_pulled_by = $rec['inventory_pulled_by'];
		$this->dinventory_packed = $rec['inventory_packed'];
		$this->dinventory_packed_by = $rec['inventory_packed_by'];
		$this->dinventory_loaded = $rec['inventory_loaded'];
		$this->dinventory_loaded_by = $rec['inventory_loaded_by'];
		$this->line_shipped_date = $rec['line_shipped_date'];
		$this->line_invoiced_date = $rec['line_invoiced_date'];
		$this->line_cancelled_date = $rec['line_cancelled_date'];
		$this->dvisible = $rec['visible'];
		$this->drev_enabled = $rec['rev_enabled'];
		$this->drev_number = $rec['rev_number'];
		$this->duser_creation = $rec['duser_creation'];
		$this->ddate_creation = $rec['ddate_creation'];
		$this->duser_modify = $rec['duser_modify'];
		$this->ddate_modify = $rec['ddate_modify'];
		return true;
	} // unarrayifyDetail
	public function _templateSelect($id=0,$readonly=false) {
		return parent::abstractSelect($id,$readonly,'sales_header','sales_order_number','sales_order_number','SalesOrders');
	} // _templateSelect()
	public function statusSelect($status='',$readonly=false,$include_label=false) {
		// QqOoHhPpBbSsIiCc
		$html = '';
		if ($include_label) $html .= '<LABEL for="SalesOrdersStatus">Status:</LABEL>';
		$html .= '<SELECT id="salesOrderStatus">';
		if ($status=='Q' || !$readonly) $html .= '<OPTION value="Q"'.($status=='Q'?' selected="selected">':'>').'Quote</OPTION>';
		if ($status=='O' || !$readonly) $html .= '<OPTION value="O"'.($status=='O'?' selected="selected">':'>').'Ordered</OPTION>';
		if ($status=='H' || !$readonly) $html .= '<OPTION value="H"'.($status=='H'?' selected="selected">':'>').'Held</OPTION>';
		if ($status=='P' || !$readonly) $html .= '<OPTION value="P"'.($status=='P'?' selected="selected">':'>').'Processing</OPTION>';
		if ($status=='B' || !$readonly) $html .= '<OPTION value="B"'.($status=='B'?' selected="selected">':'>').'Backordered</OPTION>';
		if ($status=='S' || !$readonly) $html .= '<OPTION value="S"'.($status=='S'?' selected="selected">':'>').'Shipped</OPTION>';
		if ($status=='I' || !$readonly) $html .= '<OPTION value="I"'.($status=='I'?' selected="selected">':'>').'Invoiced</OPTION>';
		if ($status=='C' || !$readonly) $html .= '<OPTION value="C"'.($status=='C'?' selected="selected">':'>').'Cancelled</OPTION>';
		if ($status=='R' || !$readonly) $html .= '<OPTION value="R"'.($status=='R'?' selected="selected">':'>').'Misdirected Return</OPTION>';
		$html .= '</SELECT>';
		return $html;
	} // statusSelect()
	public function listRecords() {
		parent::abstractListRecords('SalesOrders');
	} // listRecords()
	public function searchPage() {
		parent::abstractSearchPage('SalesOrdersSearch');
	} // searchPage()
	public function executeSearch($criteria) {
		$q = "SELECT sales_order_number,sales_order_status,quote_number,c.customer_name,customer_purchase_order_number,wave_number,bill_of_lading,invoice_number FROM sales_header h
			LEFT OUTER JOIN cust_master c ON h.customer_id=c.customer_id";
		// TODO: Add $criteria
		// TODO: Convert to prepared statements
		$q .= " ORDER BY sales_order_number";
		$result = $this->dbconn->query($q);
		if ($result!==false) {
			$this->recordSet = array();
			while ($row=$result->fetch_assoc()) {
				$this->recordSet[$row['sales_order_number']] = array('status'=>$row['sales_order_status'],'quote'=>$row['quote_number'],'customer'=>$row['customer_name'],
					'PO'=>$row['customer_purchase_order_number'],'Wave'=>$row['wave_number'],'BOL'=>$row['bill_of_lading'],'Invoice'=>$row['invoice_number']);
			} // while rows
		} // if query succeeded
		$this->listRecords();
		$_SESSION['currentScreen'] = 1044;
		$_SESSION['lastCriteria'] = $criteria;
		if (!isset($_SESSION['searchResults'])) $_SESSION['searchResults'] = array();
		$_SESSION['searchResults']['SalesOrders'] = array_keys($this->recordSet);		
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
		$q = "SELECT {$this->column_list_header},h.created_by,h.creation_date,h.last_update_by,h.last_update_date 
			FROM sales_header h 
			WHERE sales_order_number=?";
		$stmt = $this->dbconn->prepare($q);
		if ($stmt===false) {
			echo $this->dbconn->error;
			return;
		}
		$stmt->bind_param('i',$SalesOrdersid);
		$SalesOrdersid = $id;
		$result = $stmt->execute();
		// TODO: What if another user deletes the record while it's still in my search results?
		if ($result!==false) {
			$stmt->bind_result(
				$this->sales_order_number,
				$this->parent,
				$this->sales_order_type,
				$this->sales_order_status,
				$this->customer_id,
				$this->buyer,
				$this->seller,
				$this->entity_id,
				$this->division_id,
				$this->department_id,
				$this->inventory_entity,
				$this->currency_code,
				$this->visible,
				$this->rev_enabled,
				$this->rev_number,
				$this->quote_number,
				$this->quote_approved_by,
				$this->quote_given_date,
				$this->quote_expires_date,
				$this->customer_purchase_order_number,
				$this->customer_department,
				$this->customer_product_group,
				$this->store_code,
				$this->terms,
				$this->order_date,
				$this->credit_release_date,
				$this->ship_window_start,
				$this->ship_window_end,
				$this->must_route_by,
				$this->must_arrive_by,
				$this->order_cancelled_date,
				$this->wave_number,
				$this->wave_date,
				$this->inventory_needed_by,
				$this->inventory_pulled_complete,
				$this->inventory_packed_complete,
				$this->fv_vendor_id,
				$this->bill_of_lading,
				$this->rrc,
				$this->load_id,
				$this->routing_requested,
				$this->pickup_scheduled_for,
				$this->inventory_loaded_complete,
				$this->bol_date,
				$this->order_shipped_date,
				$this->invoice_number,
				$this->order_invoiced_date,
				$this->invoice_paid_complete,
				$this->shipping_from,
				$this->shipping_to,
				$this->remit_to,
				$this->huser_creation,$this->hdate_creation,$this->huser_modify,$this->hdate_modify
			);
			$stmt->store_result();
			$stmt->fetch();
			$stmt->close();		

			$q = "SELECT {$this->column_list_detail},d.created_by,d.creation_date,d.last_update_by,d.last_update_date 
				FROM sales_detail d 
				WHERE sales_order_number=?";
			$stmt = $this->dbconn->prepare($q);
			if ($stmt===false) {
				echo $this->dbconn->error;
				return;
			}
			$stmt->bind_param('i',$SalesOrdersid);
			$SalesOrdersid = $id;
			$dresult = $stmt->execute();
			if ($dresult!==false) {
				$stmt->bind_result(
					$this->sales_order_number,
					$this->sales_order_line,
					$this->parent_line,
					$this->dentity_id,
					$this->ddivision_id,
					$this->ddepartment_id,
					$this->customer_line,
					$this->edi_raw1,
					$this->edi_raw2,
					$this->item_id,
					$this->quantity_requested,
					$this->quantity_shipped,
					$this->quantity_returned,
					$this->quantity_backordered,
					$this->quantity_cancelled,
					$this->quantity_uom,
					$this->price,
					$this->discount_percent,
					$this->discount_amount,
					$this->dcredit_release_date,
					$this->dwave_date,
					$this->assigned_to,
					$this->dinventory_needed_by,
					$this->dinventory_location,
					$this->dinventory_pulled,
					$this->dinventory_pulled_by,
					$this->dinventory_packed,
					$this->dinventory_packed_by,
					$this->dinventory_loaded,
					$this->dinventory_loaded_by,
					$this->line_shipped_date,
					$this->line_invoiced_date,
					$this->line_cancelled_date,
					$this->dvisible,
					$this->drev_enabled,
					$this->drev_number,
					$this->duser_creation,
					$this->ddate_creation,
					$this->duser_modify,
					$this->ddate_modify
				);
				$stmt->store_result();
				while ($stmt->fetch()) {
					$this->detail_array[$this->sales_order_line] = $this->arrayifyDetail();
				}
				$stmt->close();
			}
			
			if ($mode!='update') {
				$hdata = $this->arrayifyHeader();
				echo parent::abstractRecord($mode,'SalesOrders','',$hdata,$this->detail_array);
				echo '<SCRIPT>$("#sales_header-ordered_edit legend").siblings().hide(); 
					$("#sales_header-processing_edit legend").siblings().hide(); 
					$("#sales_header-shipping_edit legend").siblings().hide();
					$("#sales_header-invoicing_edit legend").siblings().hide();
					$("#salesOrderStatus").change(function() {onChange_SalesOrderStatus();});
			</SCRIPT>';
			}
		} // if result
		else $this->sales_order_number = null;
		//echo $html;
		$_SESSION['currentScreen'] = 2044;
		if (!isset($_SESSION['searchResults']) && !isset($_SESSION['searchResults']['SalesOrders']))
			$_SESSION['idarray'] = array(0,0,$id,0,0);
		else {
			$idloc = array_search($id,$_SESSION['searchResults']['SalesOrders'],false);
			$f = $_SESSION['searchResults']['SalesOrders'][0];
			$l = $_SESSION['searchResults']['SalesOrders'][] = array_pop($_SESSION['searchResults']['SalesOrders']); // https://stackoverflow.com/questions/3687358/whats-the-best-way-to-get-the-last-element-of-an-array-without-deleting-it#comment63556865_3687358
			if ($idloc > 0) $p = $_SESSION['searchResults']['SalesOrders'][$idloc-1]; else $p = $f;
			if ($l != $id) $n = $_SESSION['searchResults']['SalesOrders'][$idloc+1]; else $n = $l;
			$_SESSION['idarray'] = array($f,$p,$id,$n,$l);
		}		
	} // display()
	public function newRecord() {
		echo parent::abstractRecord('new','SalesOrders');
		echo '<SCRIPT>$("#sales_header-ordered_edit legend").siblings().hide(); 
			$("#sales_header-processing_edit legend").siblings().hide(); 
			$("#sales_header-shipping_edit legend").siblings().hide();
			$("#sales_header-invoicing_edit legend").siblings().hide();
			$("#salesOrderStatus").change(function() {onChange_SalesOrderStatus();});
			$("#entity_id").change(function() {onChange_SalesOrdersHeaderEntity();});
			$("#department_id").change(function() {onChange_SalesOrdersHeaderDepartment();});
			$("#division_id").change(function() {onChange_SalesOrdersHeaderDivision();});
			$("#credit_release_date-date").change(function() {onChange_SalesOrdersHeaderCreditReleaseDate();});
			$("#wave_date-date").change(function() {onChange_SalesOrdersHeaderWaveDate();});
			$("#inventory_needed_by-date").change(function() {onChange_SalesOrdersHeaderInventoryNeededBy();});
			$("#inventory_needed_by-time").change(function() {onChange_SalesOrdersHeaderInventoryNeededBy();});
			</SCRIPT>';
		$_SESSION['currentScreen'] = 3044;
	} // newRecord()
	public function editRecord($id) {
		$this->display($id,'edit');
		echo '<SCRIPT>onChange_SalesOrderStatus();</SCRIPT>';
		$_SESSION['currentScreen'] = 4044;
	}
	private function insertHeader() {
		$this->resetHeader();
		$this->resetDetail();
		$ordernum = isset($_POST['h1'])?$_POST['h1']:0;
		$parent = isset($_POST['h2'])?$_POST['h2']:null;
		$ordertype = isset($_POST['h3'])?$_POST['h3']:null;
		$orderstatus = isset($_POST['h4'])?$_POST['h4']:'Q';
		$customerid = isset($_POST['h5'])?$_POST['h5']:null;
		$buyer = isset($_POST['h6'])?$_POST['h6']:null;
		$seller = isset($_POST['h7'])?$_POST['h7']:null;
		$entityid = isset($_POST['h8'])?$_POST['h8']:null;
		$divisionid = isset($_POST['h9'])?$_POST['h9']:null;
		$departmentid = isset($_POST['h10'])?$_POST['h10']:null;
		$invent = isset($_POST['h11'])?$_POST['h11']:null;
		$currency = isset($_POST['h12'])?$_POST['h12']:null;
		$visible = isset($_POST['h13'])?$_POST['h13']:false;
		$rev_enabled = isset($_POST['h14'])?$_POST['h14']:false;
		$rev_number = isset($_POST['h15'])?$_POST['h15']:1;
		$quotenum = isset($_POST['q1'])?$_POST['q1']:0;
		$quoteapprovedby = isset($_POST['q2'])?$_POST['q2']:null;
		$quotegiven = isset($_POST['q3'])?new DateTime($_POST['q3']):null;
		if (!is_null($quotegiven)) $quotegiven = $quotegiven->format('Y-m-d');
		$quoteexpires = isset($_POST['q4'])?new DateTime($_POST['q4']):null;
		if (!is_null($quoteexpires)) $quoteexpires = $quoteexpires->format('Y-m-d');
		$customerpo = isset($_POST['o1'])?$_POST['o1']:'';
		$customerdept = isset($_POST['o2'])?$_POST['o2']:'';
		$customerpg = isset($_POST['o3'])?$_POST['o3']:'';
		$store = isset($_POST['o4'])?$_POST['o4']:null;
		$termsid = isset($_POST['o5'])?$_POST['o5']:null;
		$orderdate = isset($_POST['o6'])?new DateTime($_POST['o6']):null;
		if (!is_null($orderdate)) $orderdate = $orderdate->format('Y-m-d');
		$creditrelease = isset($_POST['o7'])?new DateTime($_POST['o7']):null;
		if (!is_null($creditrelease)) $creditrelease = $creditrelease->format('Y-m-d');
		$startship = isset($_POST['o8'])?new DateTime($_POST['o8']):null;
		if (!is_null($startship)) $startship = $startship->format('Y-m-d');
		$endship = isset($_POST['o9'])?new DateTime($_POST['o9']):null;
		if (!is_null($endship)) $endship = $endship->format('Y-m-d');
		$routeby = isset($_POST['o10'])?new DateTime($_POST['o10']):null;
		if (!is_null($routeby)) $routeby = $routeby->format('Y-m-d');
		$mustarrive = isset($_POST['o11'])?new DateTime($_POST['o11']):null;
		if (!is_null($mustarrive)) $mustarrive = $mustarrive->format('Y-m-d');
		$cancelleddate = isset($_POST['o12'])?new DateTime($_POST['o12']):null;
		if (!is_null($cancelleddate)) $cancelleddate = $cancelleddate->format('Y-m-d');
		$wavenum = isset($_POST['p1'])?$_POST['p1']:0;
		$wavedate = isset($_POST['p2'])?new DateTime($_POST['p2']):null;
		if (!is_null($wavedate)) $wavedate = $wavedate->format('Y-m-d');
		$invneeded_d = isset($_POST['p3d'])?$_POST['p3d']:'';
		$invneeded_t = isset($_POST['p3t'])?$_POST['p3t']:'';
		$invpulled_d = isset($_POST['p4d'])?$_POST['p4d']:'';
		$invpulled_t = isset($_POST['p4t'])?$_POST['p4t']:'';
		$invpacked_d = isset($_POST['p5d'])?$_POST['p5d']:'';
		$invpacked_t = isset($_POST['p5t'])?$_POST['p5t']:'';
		$shipper = isset($_POST['s1'])?$_POST['s1']:null;
		$bol = isset($_POST['s2'])?$_POST['s2']:'';
		$rrc = isset($_POST['s3'])?$_POST['s3']:'';
		$loadid = isset($_POST['s4'])?$_POST['s4']:'';
		$routed_d = isset($_POST['s5d'])?$_POST['s5d']:'';
		$routed_t = isset($_POST['s5t'])?$_POST['s5t']:'';
		$pickup_d = isset($_POST['s6d'])?$_POST['s6d']:'';
		$pickup_t = isset($_POST['s6t'])?$_POST['s6t']:'';
		$invloaded_d = isset($_POST['s7d'])?$_POST['s7d']:'';
		$invloaded_t = isset($_POST['s7t'])?$_POST['s7t']:'';
		$boldate = isset($_POST['s8'])?new DateTime($_POST['s8']):null;
		if (!is_null($boldate)) $boldate = $boldate->format('Y-m-d');
		$ordershipped = isset($_POST['s9'])?new DateTime($_POST['s9']):null;
		if (!is_null($ordershipped)) $ordershipped = $ordershipped->format('Y-m-d');
		$invoicenum = isset($_POST['i1'])?$_POST['i1']:0;
		$invoicedate = isset($_POST['i2'])?new DateTime($_POST['i2']):null;
		if (!is_null($invoicedate)) $invoicedate = $invoicedate->format('Y-m-d');
		$paiddate = isset($_POST['i3'])?new DateTime($_POST['i3']):null;
		if (!is_null($paiddate)) $paiddate = $paiddate->format('Y-m-d');
		$shipfrom = isset($_POST['o13'])?$_POST['o13']:null;
		$shipto = isset($_POST['o14'])?$_POST['o14']:null;
		$remitto = isset($_POST['i4'])?$_POST['i4']:null;
		$return_date = false;
		if ($orderstatus=='O' && strlen(trim($orderdate))==0) $return_date = true;
		$invneeded = new DateTime($invneeded_d.' '.$invneeded_t);
		if (!is_null($invneeded)) $invneeded = $invneeded->format('Y-m-d H:i:s');
		$invpulled = new DateTime($invpulled_d.' '.$invpulled_t);
		if (!is_null($invpulled)) $invpulled = $invpulled->format('Y-m-d H:i:s');
		$invpacked = new DateTime($invpacked_d.' '.$invpacked_t);
		if (!is_null($invpacked)) $invpacked = $invpacked->format('Y-m-d H:i:s');
		$routed = new DateTime($routed_d.' '.$routed_t);
		if (!is_null($routed)) $routed = $routed->format('Y-m-d H:i:s');
		$pickup = new DateTime($pickup_d.' '.$pickup_t);
		if (!is_null($pickup)) $pickup = $pickup->format('Y-m-d H:i:s');
		$invloaded = new DateTime($invloaded_d.' '.$invloaded_t);
		if (!is_null($invloaded)) $invloaded = $invloaded->format('Y-m-d H:i:s');
		$q = "INSERT INTO sales_header (
			parent,sales_order_type,sales_order_status,customer_id,buyer,seller,entity_id,division_id,department_id,inventory_entity,currency_code,visible,
			quote_number,quote_approved_by,quote_given_date,quote_expires_date,
			customer_purchase_order_number,customer_department,customer_product_group,store_code,terms,order_date,credit_release_date,ship_window_start,ship_window_end,must_route_by,must_arrive_by,order_cancelled_date,
			wave_number,wave_date,inventory_needed_by,inventory_pulled_complete,inventory_packed_complete,
			fv_vendor_id,bill_of_lading,rrc,load_id,routing_requested,pickup_scheduled_for,inventory_loaded_complete,bol_date,order_shipped_date,
			invoice_number,order_invoiced_date,invoice_paid_complete,
			shipping_from,shipping_to,remit_to,
			rev_enabled,rev_number,created_by,creation_date,last_update_by,last_update_date) VALUES 
			(?,?,?,?,?,?,?,?,?,?,?,?,
			?,?,?,?,
			?,?,?,?,?,?,?,?,?,?,?,?,
			?,?,?,?,?,
			?,?,?,?,?,?,?,?,?,
			?,?,?,?,?,?,
			?,?,?,NOW(),?,NOW());";
		$stmt = $this->dbconn->prepare($q);
		$stmt->bind_param('iisiiiiiiiss'.'siss'.'sssiisssssss'.'issss'.'issssssss'.'issiii'.'siii',
			$h2,$h3,$h4,$h5,$h6,$h7,$h8,$h9,$h10,$h11,$h12,$h13,
			$q1,$q2,$q3,$q4,
			$o1,$o2,$o3,$o4,$o5,$o6,$o7,$o8,$o9,$o10,$o11,$o12,
			$p1,$p2,$p3,$p4,$p5,
			$s1,$s2,$s3,$s4,$s5,$s6,$s7,$s8,$s9,
			$i1,$i2,$i3,$o13,$o14,$i4,
			$h14,$h15,$h16,$h17
		);
		// TODO: Validate all fields & send appropriate error messages
		if (is_integer($parent) || ctype_digit($parent)) $h2 = $parent; else $h2 = null;
		if (is_integer($ordertype) || ctype_digit($ordertype)) $h3 = $ordertype; else $h3 = null;
		if (strpos($orderstatus,'QqOoHhPpBbSsIiCcRr')===false) {
			echo 'fail|The order status provided is not a valid choice.';
			return;
		}
		$h4 = $orderstatus;
		if (is_integer($customerid) || ctype_digit($customerid)) $h5 = $customerid; else $h5 = null;
		if (is_integer($buyer) || ctype_digit($buyer)) $h6 = $buyer; else $h6 = null;
		if (is_integer($seller) || ctype_digit($seller)) $h7 = $seller; else $h7 = null;
		if (is_integer($entityid) || ctype_digit($entityid)) $h8 = $entityid; else $h8 = null;
		if (is_integer($divisionid) || ctype_digit($divisionid)) $h9 = $divisionid; else $h9 = null;
		if (is_integer($departmentid) || ctype_digit($departmentid)) $h10 = $departmentid; else $h10 = null;
		if (is_integer($invent) || ctype_digit($invent)) $h11 = $invent; else $h11 = null;
		if ($currency!='') $h12 = $currency; else $h12 = null;
		$h13 = ($visible=='true')?'Y':'N';
		$q1 = $quotenum;
		if (is_integer($quoteapprovedby) || ctype_digit($quoteapprovedby)) $q2 = $quoteapprovedby; else $q2 = null;
		$q3 = $quotegiven;
		$q4 = $quoteexpires;
		$o1 = $customerpo;
		$o2 = $customerdept;
		$o3 = $customerpg;
		if (is_integer($store) || ctype_digit($store)) $o4 = $store; else $o4 = null;
		if (is_integer($termsid) || ctype_digit($termsid)) $o5 = $termsid; else $o5 = null;
		$o6 = $orderdate;
		$o7 = $creditrelease;
		$o8 = $startship;
		$o9 = $endship;
		$o10 = $routeby;
		$o11 = $mustarrive;
		$o12 = $cancelleddate;
		$p1 = $wavenum;
		$p2 = $wavedate;
		$p3 = $invneeded;
		$p4 = $invpulled;
		$p5 = $invpacked;
		if (is_integer($shipper) || ctype_digit($shipper)) $s1 = $shipper; else $s1 = null;
		$s2 = $bol;
		$s3 = $rrc;
		$s4 = $loadid;
		$s5 = $routed;
		$s6 = $pickup;
		$s7 = $invloaded;
		$s8 = $boldate;
		$s9 = $ordershipped;
		$i1 = $invoicenum;
		$i2 = $invoicedate;
		$i3 = $paiddate;
		if (is_integer($shipfrom) || ctype_digit($shipfrom)) $o13 = $shipfrom; else $o13 = null;
		if (is_integer($shipto) || ctype_digit($shipto)) $o14 = $shipto; else $o14 = null;
		if (is_integer($remitto) || ctype_digit($remitto)) $i4 = $remitto; else $i4 = null;
		$h14 = ($rev_enabled=='true')?'Y':'N';
		if ($rev_number<1) $rev_number = 1;
		$h15 = $rev_number;
		$h16 = $_SESSION['dbuserid'];
		$h17 = $_SESSION['dbuserid'];
		$result = $stmt->execute();
		if ($result!==false) {
			echo 'inserted|'.$this->dbconn->insert_id;
		} else {
			echo 'fail|'.$this->dbconn->error;
			$this->mb->addError($this->dbconn->error);
		}
		$stmt->close();
	} // insertHeader()
	private function insertDetail() {
		$this->resetDetail();
		if (!isset($_POST['sales_order_number']) /*|| $_POST['sales_order_number']!=$this->sales_order_number*/) {
			$this->mb->addError("Details cannot be inserted when the sales order number is zero, or the header and detail don't match.");
			echo 'fail|Header-detail mismatch {'.$_POST['sales_order_number'].'}';
			return;
		}
		$this->sales_order_number = $_POST['sales_order_number'];
		$this->display($this->sales_order_number,'update'); // Display already has the logic for loading the record.  TODO: Refactor into separate function.
		if (is_null($this->sales_order_number)) {
			echo 'fail|Invalid sales order number for adding items.';
			return;
		}
		$this->sales_order_line = !empty($_POST['sales_order_line'])?$_POST['sales_order_line']:0;
		if (empty($this->sales_order_line)) {
			$sq = 'SELECT MAX(sales_order_line)+1 AS linenum FROM sales_detail WHERE sales_order_number=?;';
			$sst = $this->dbconn->prepare($sq);
			$sst->bind_param('i',$son);
			$son = $this->sales_order_number;
			$sres = $sst->execute();
			if ($sres!==false) {
				$sst->store_result();
				if ($sst->num_rows>0) {
					$sst->bind_result($this->sales_order_line);
					$sst->fetch();
					if (empty($this->sales_order_line)) $this->sales_order_line = 1;
				} else $this->sales_order_line = 1;
			} else $this->sales_order_line=1;
		}
		$this->parent_line = !empty($_POST['parent_line'])?$_POST['parent_line']:null;
		$this->dentity_id = !empty($_POST['dentity_id'])?$_POST['dentity_id']:null;
		$this->ddivision_id = !empty($_POST['ddivision_id'])?$_POST['ddivision_id']:null;
		$this->ddepartment_id = !empty($_POST['ddepartment_id'])?$_POST['ddepartment_id']:null;
		$this->customer_line = !empty($_POST['customer_line'])?$_POST['customer_line']:null;
		$this->edi_raw1 = !empty($_POST['edi_raw1'])?$_POST['edi_raw1']:null;
		$this->edi_raw2 = !empty($_POST['edi_raw2'])?$_POST['edi_raw2']:null;
		$this->item_id = !empty($_POST['item_id'])?$_POST['item_id']:null;
		$this->quantity_requested = !empty($_POST['quantity_requested'])?$_POST['quantity_requested']:0;
		$this->quantity_shipped = !empty($_POST['quantity_shipped'])?$_POST['quantity_shipped']:0;
		$this->quantity_returned = !empty($_POST['quantity_returned'])?$_POST['quantity_returned']:0;
		$this->quantity_backordered = !empty($_POST['quantity_backordered'])?$_POST['quantity_backordered']:0;
		$this->quantity_cancelled = !empty($_POST['quantity_cancelled'])?$_POST['quantity_cancelled']:0;
		$this->quantity_uom = !empty($_POST['quantity_uom'])?$_POST['quantity_uom']:null;
		$this->price = !empty($_POST['price'])?$_POST['price']:null;
		$this->discount_percent = !empty($_POST['discount_percent'])?$_POST['discount_percent']:0.00;
		$this->discount_amount = !empty($_POST['discount_amount'])?$_POST['discount_amount']:0.00;
		$this->retail_high = !empty($_POST['retail_high'])?$_POST['retail_high']:null;
		$this->retail_low = !empty($_POST['retail_low'])?$_POST['retail_low']:null;
		$this->dcredit_release_date = !empty($_POST['dcredit_release_date'])?$_POST['dcredit_release_date']:null;
		$this->dwave_date = !empty($_POST['dwave_date'])?$_POST['dwave_date']:null;
		$this->assigned_to = !empty($_POST['assigned_to'])?$_POST['assigned_to']:null;
		$this->dinventory_needed_by = (!empty($_POST['dinventory_needed_bydate']) && !empty($_POST['dinventory_needed_bytime']))?(new DateTime($_POST['dinventory_needed_bydate'].' '.$_POST['dinventory_needed_bytime'])):null;
		$this->dinventory_location = !empty($_POST['dinventory_location'])?$_POST['dinventory_location']:null;
		$this->dinventory_pulled = (!empty($_POST['dinventory_pulleddate']) && !empty($_POST['dinventory_pulledtime']))?(new DateTime($_POST['dinventory_pulleddate'].' '.$_POST['dinventory_pulledtime'])):null;
		$this->dinventory_pulled_by = !empty($_POST['dinventory_pulled_by'])?$_POST['dinventory_pulled_by']:null;
		$this->dinventory_packed = (!empty($_POST['dinventory_packeddate']) && !empty($_POST['dinventory_packedtime']))?(new DateTime($_POST['dinventory_packeddate'].' '.$_POST['dinventory_packedtime'])):null;
		$this->dinventory_packed_by = !empty($_POST['dinventory_packed_by'])?$_POST['dinventory_packed_by']:null;
		$this->dinventory_loaded = (!empty($_POST['dinventory_loadeddate']) && !empty($_POST['dinventory_loadedtime']))?(new DateTime($_POST['dinventory_loadeddate'].' '.$_POST['dinventory_loadedtime'])):null;
		$this->dinventory_loaded_by = !empty($_POST['dinventory_loaded_by'])?$_POST['dinventory_loaded_by']:null;
		$this->line_shipped_date = !empty($_POST['line_shipped_date'])?$_POST['line_shipped_date']:null;
		$this->line_invoiced_date = !empty($_POST['line_invoiced_date'])?$_POST['line_invoiced_date']:null;
		$this->line_cancelled_date = !empty($_POST['line_cancelled_date'])?$_POST['line_cancelled_date']:null;
		$this->dvisible = !empty($_POST['dvisible'])?$_POST['dvisible']:null;
		$this->drev_enabled = !empty($_POST['drev_enabled'])?$_POST['drev_enabled']:null;
		$this->drev_number = !empty($_POST['drev_number'])?$_POST['drev_number']:null;
//var_dump($this->arrayifyDetail());
		$q = 'INSERT INTO sales_detail ('.$this->column_list_detail.',created_by,creation_date,last_update_by,last_update_date) VALUES 
			(?,?,?,?,?,?,?,?,?,?,
			?,?,?,?,?,?,?,?,?,?,?,
			?,?,?,?,?,?,?,?,?,?,?,?,?,?,
			?,?,?,?,NOW(),?,NOW());';
		$stmt = $this->dbconn->prepare($q);
		$stmt->bind_param('iiiiiisssi'.'dddddsddddd'.'ssisisisisisss'.'ssiii',
			$p1,$p2,$p3,$p4,$p5,$p6,$p7,$p8,$p9,$p10,
			$p11,$p12,$p13,$p14,$p15,$p16,$p17,$p18,$p19,$p20,$p21,
			$p22,$p23,$p24,$p25,$p26,$p27,$p28,$p29,$p30,$p31,$p32,$p33,$p34,$p35,
			$p36,$p37,$p38,$p39,$p41);
		if ($this->sales_order_number==0) {
			$this->mb->addError("Details cannot be inserted when the sales order number is zero.");
			echo 'fail|Sales Order Number is zero.';
			$stmt->close();
			return;
		}
		$p1 = $this->sales_order_number;
		$p2 = $this->sales_order_line;
		$p3 = $this->parent_line;
		$p4 = $this->dentity_id;
		$p5 = $this->ddivision_id;
		$p6 = $this->ddepartment_id;
		$p7 = $this->customer_line;
		$p8 = $this->edi_raw1;
		$p9 = $this->edi_raw2;
		$p10 = $this->item_id;
		$p11 = $this->quantity_requested;
		$p12 = $this->quantity_shipped;
		$p13 = $this->quantity_returned;
		$p14 = $this->quantity_backordered;
		$p15 = $this->quantity_cancelled;
		$p16 = $this->quantity_uom;
		$p17 = $this->price;
		$p18 = $this->discount_percent;
		$p19 = $this->discount_amount;
		$p20 = $this->retail_high;
		$p21 = $this->retail_low;
		$d22 = is_null($this->dcredit_release_date)?null:new DateTime($this->dcredit_release_date);
		$p22 = is_null($d22)?null:$d22->format('Y-m-d');
		$d23 = is_null($this->dwave_date)?null:new DateTime($this->dwave_date);
		$p23 = is_null($d23)?null:$d23->format('Y-m-d');
		$p24 = $this->assigned_to;
		$p25 = is_null($this->dinventory_needed_by)?null:$this->dinventory_needed_by->format('Y-m-d H:i:s');
		$p26 = $this->dinventory_location;
		$p27 = is_null($this->dinventory_pulled)?null:$this->dinventory_pulled->format('Y-m-d H:i:s');
		$p28 = $this->dinventory_pulled_by;
		$p29 = is_null($this->dinventory_packed)?null:$this->dinventory_packed->format('Y-m-d H:i:s');
		$p30 = $this->dinventory_packed_by;
		$p31 = is_null($this->dinventory_loaded)?null:$this->dinventory_loaded->format('Y-m-d H:i:s');
		$p32 = $this->dinventory_loaded_by;
		$d33 = is_null($this->line_shipped_date)?null:new DateTime($this->line_shipped_date);
		$p33 = is_null($d33)?null:$d33->format('Y-m-d');
		$d34 = is_null($this->line_invoiced_date)?null:new DateTime($this->line_invoiced_date);
		$p34 = is_null($d34)?null:$d34->format('Y-m-d');
		$d35 = is_null($this->line_cancelled_date)?null:new DateTime($this->line_cancelled_date);
		$p35 = is_null($d35)?null:$d35->format('Y-m-d');
		$p36 = ($this->dvisible=='true')?'Y':'N';
		$p37 = ($this->drev_enabled=='true')?'Y':'N';
		if ($this->drev_number < 1) $this->drev_number = 1;
		$p38 = $this->drev_number;
		$p39 = $_SESSION['dbuserid'];
		$p41 = $_SESSION['dbuserid'];
		$result = $stmt->execute();
		if ($result!==false) {
			echo 'inserted|'.$this->sales_order_line;
			$inv = new InventoryManager($this->dbconn);
			if ($this->sales_order_status=='Q' || $this->sales_order_status=='H') {
				$inv->salesReserve(($this->sales_order_number*100)+$this->sales_order_line,$this->dentity_id,$this->item_id,$this->quantity_requested);
			} elseif ($this->sales_order_status=='S' || $this->sales_order_status=='I') {
				$inv->salesShip(($this->sales_order_number*100)+$this->sales_order_line,$this->dentity_id,$this->item_id,$this->quantity_shipped - $this->quantity_returned);
			} else {
				$inv->salesSold(($this->sales_order_number*100)+$this->sales_order_line,$this->dentity_id,$this->item_id,$this->quantity_requested - $this->quantity_shipped - 
					$this->quantity_returned - $this->quantity_cancelled);
				$inv->salesShip(($this->sales_order_number*100)+$this->sales_order_line,$this->dentity_id,$this->item_id,$this->quantity_shipped - $this->quantity_returned);
			}
			
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
		$id = $_POST['h1'];
		if ((!is_integer($id) && !ctype_digit($id)) || $id<1) {
			echo 'fail|Invalid sales order number for updating';
			return;
		}
		$this->display($id,'update'); // Display already has the logic for loading the record.  TODO: Refactor into separate function.
		if (is_null($this->sales_order_number)) {
			echo 'fail|Invalid sales order number for updating';
			return;
		}
		$update = array();
		if (isset($_POST['h2']) && $_POST['h2']!=$this->parent) $update['parent'] = array('i',$_POST['h2']);
		if (isset($_POST['h3']) && $_POST['h3']!=$this->sales_order_type) $update['sales_order_type'] = array('i',$_POST['h3']);
		if (isset($_POST['h4']) && $_POST['h4']!=$this->sales_order_status) $update['sales_order_status'] = array('s',$_POST['h4']);
		if (isset($_POST['h5']) && $_POST['h5']!=$this->customer_id) $update['customer_id'] = array('i',$_POST['h5']);
		if (isset($_POST['h6']) && $_POST['h6']!=$this->buyer) $update['buyer'] = array('i',$_POST['h6']);
		if (isset($_POST['h7']) && $_POST['h7']!=$this->seller) $update['seller'] = array('i',$_POST['h7']);
		if (isset($_POST['h8']) && $_POST['h8']!=$this->entity_id) $update['entity_id'] = array('i',$_POST['h8']);
		if (isset($_POST['h9']) && $_POST['h9']!=$this->division_id) $update['division_id'] = array('i',$_POST['h9']);
		if (isset($_POST['h10']) && $_POST['h10']!=$this->department_id) $update['department_id'] = array('i',$_POST['h10']);
		if (isset($_POST['h11']) && $_POST['h11']!=$this->inventory_entity) $update['inventory_entity'] = array('i',$_POST['h11']);
		if (isset($_POST['h12']) && $_POST['h12']!=$this->currency_code) $update['currency_code'] = array('s',$_POST['h12']);
		$visible = null;
		if (isset($_POST['h13'])) $visible = ($_POST['h13']=='true')?'Y':'N';
		if (!is_null($visible) && $visible!=$this->visible) $update['visible'] = array('s',$visible);
		$reven = null;
		if (isset($_POST['h14'])) $reven = ($_POST['h14']=='true')?'Y':'N';
		if (!is_null($reven) && $reven!=$this->rev_enabled) $update['rev_enabled'] = array('s',$reven);
		if ((!is_null($reven)) && $reven=='Y' && isset($_POST['h15']) && $_POST['h15']!=$this->rev_number) $update['rev_number'] = array('i',$_POST['h15']);
		if (isset($_POST['q1']) && $_POST['q1']!=$this->quote_number) $update['quote_number'] = array('s',$_POST['q1']);
		if (isset($_POST['q2']) && $_POST['q2']!=$this->quote_approved_by) $update['quote_approved_by'] = array('i',$_POST['q2']);
		if (isset($_POST['q3']) && $_POST['q3']!=substr($this->quote_given_date,0,10)) {
			$d = new DateTime($_POST['q3']);
			if (!is_null($d)) $update['quote_given_date'] = array('s',$d->format('Y-m-d'));
		}
		if (isset($_POST['q4']) && $_POST['q4']!=substr($this->quote_expires_date,0,10)) {
			$d = new DateTime($_POST['q4']);
			if (!is_null($d)) $update['quote_expires_date'] = array('s',$d->format('Y-m-d'));
		}
		if (isset($_POST['o1']) && $_POST['o1']!=$this->customer_purchase_order_number) $update['customer_purchase_order_number'] = array('s',$_POST['o1']);
		if (isset($_POST['o2']) && $_POST['o2']!=$this->customer_department) $update['customer_department'] = array('s',$_POST['o2']);
		if (isset($_POST['o3']) && $_POST['o3']!=$this->customer_product_group) $update['customer_product_group'] = array('s',$_POST['o3']);
		if (isset($_POST['o4']) && $_POST['o4']!=$this->store_code) $update['store_code'] = array('i',$_POST['o4']);
		if (isset($_POST['o5']) && $_POST['o5']!=$this->terms) $update['terms'] = array('i',$_POST['o5']);
		if (isset($_POST['o6']) && $_POST['o6']!=substr($this->order_date,0,10)) {
			$d = new DateTime($_POST['o6']);
			if (!is_null($d)) $update['order_date'] = array('s',$d->format('Y-m-d'));
		}
		if (isset($_POST['o7']) && $_POST['o7']!=substr($this->credit_release_date,0,10)) {
			$d = new DateTime($_POST['o7']);
			if (!is_null($d)) $update['credit_release_date'] = array('s',$d->format('Y-m-d'));
		}
		if (isset($_POST['o8']) && $_POST['o8']!=substr($this->ship_window_start,0,10)) {
			$d = new DateTime($_POST['o8']);
			if (!is_null($d)) $update['ship_window_start'] = array('s',$d->format('Y-m-d'));
		}
		if (isset($_POST['o9']) && $_POST['o9']!=substr($this->ship_window_end,0,10)) {
			$d = new DateTime($_POST['o9']);
			if (!is_null($d)) $update['ship_window_end'] = array('s',$d->format('Y-m-d'));
		}
		if (isset($_POST['o10']) && $_POST['o10']!=substr($this->must_route_by,0,10)) {
			$d = new DateTime($_POST['o10']);
			if (!is_null($d)) $update['must_route_by'] = array('s',$d->format('Y-m-d'));
		}
		if (isset($_POST['o11']) && $_POST['o11']!=substr($this->must_arrive_by,0,10)) {
			$d = new DateTime($_POST['o11']);
			if (!is_null($d)) $update['must_arrive_by'] = array('s',$d->format('Y-m-d'));
		}
		if (isset($_POST['o12']) && $_POST['o12']!=substr($this->order_cancelled_date,0,10)) {
			$d = new DateTime($_POST['o12']);
			if (!is_null($d)) $update['order_cancelled_date'] = array('s',$d->format('Y-m-d'));
		}
		if (isset($_POST['p1']) && $_POST['p1']!=$this->wave_number) $update['wave_number'] = array('s',$_POST['p1']);
		if (isset($_POST['p2']) && $_POST['p2']!=substr($this->wave_date,0,10)) {
			$d = new DateTime($_POST['p2']);
			if (!is_null($d)) $update['wave_date'] = array('s',$d->format('Y-m-d'));
		}
		if (isset($_POST['p3d']) && isset($_POST['p3t'])) {
			$d = new DateTime($_POST['p3d'].' '.$_POST['p3t']);
			if (!is_null($d)) $d = $d->format('Y-m-d H:i:s');
			if ($d!=substr($this->inventory_needed_by,0,19)) $update['inventory_needed_by'] = array('s',$d);
		}
		if (isset($_POST['p4d']) && isset($_POST['p4t'])) {
			$d = new DateTime($_POST['p4d'].' '.$_POST['p4t']);
			if (!is_null($d)) $d = $d->format('Y-m-d H:i:s');
			if ($d!=substr($this->inventory_pulled_complete,0,19)) $update['inventory_pulled_complete'] = array('s',$d);
		}
		if (isset($_POST['p5d']) && isset($_POST['p5t'])) {
			$d = new DateTime($_POST['p5d'].' '.$_POST['p5t']);
			if (!is_null($d)) $d = $d->format('Y-m-d H:i:s');
			if ($d!=substr($this->inventory_packed_complete,0,19)) $update['inventory_packed_complete'] = array('s',$d);
		}
		if (isset($_POST['s1']) && $_POST['s1']!=$this->fv_vendor_id) $update['fv_vendor_id'] = array('i',$_POST['s1']);
		if (isset($_POST['s2']) && $_POST['s2']!=$this->bill_of_lading) $update['bill_of_lading'] = array('s',$_POST['s2']);
		if (isset($_POST['s3']) && $_POST['s3']!=$this->rrc) $update['rrc'] = array('s',$_POST['s3']);
		if (isset($_POST['s4']) && $_POST['s4']!=$this->load_id) $update['load_id'] = array('s',$_POST['s4']);
		if (isset($_POST['s5d']) && isset($_POST['s5t'])) {
			$d = new DateTime($_POST['s5d'].' '.$_POST['s5t']);
			if (!is_null($d)) $d = $d->format('Y-m-d H:i:s');
			if ($d!=substr($this->routing_requested,0,19)) $update['routing_requested'] = array('s',$d);
		}
		if (isset($_POST['s6d']) && isset($_POST['s6t'])) {
			$d = new DateTime($_POST['s6d'].' '.$_POST['s6t']);
			if (!is_null($d)) $d = $d->format('Y-m-d H:i:s');
			if ($d!=substr($this->pickup_scheduled_for,0,19)) $update['pickup_scheduled_for'] = array('s',$d);
		}
		if (isset($_POST['s7d']) && isset($_POST['s7t'])) {
			$d = new DateTime($_POST['s7d'].' '.$_POST['s7t']);
			if (!is_null($d)) $d = $d->format('Y-m-d H:i:s');
			if ($d!=substr($this->inventory_loaded_complete,0,19)) $update['inventory_loaded_complete'] = array('s',$d);
		}
		if (isset($_POST['s8']) && $_POST['s8']!=substr($this->bol_date,0,10)) {
			$d = new DateTime($_POST['s8']);
			if (!is_null($d)) $update['bol_date'] = array('s',$d->format('Y-m-d'));
		}
		if (isset($_POST['s9']) && $_POST['s9']!=substr($this->order_shipped_date,0,10)) {
			$d = new DateTime($_POST['s9']);
			if (!is_null($d)) $update['order_shipped_date'] = array('s',$d->format('Y-m-d'));
		}
		if (isset($_POST['i1']) && $_POST['i1']!=$this->invoice_number) $update['invoice_number'] = array('i',$_POST['i1']);
		if (isset($_POST['i2']) && $_POST['i2']!=substr($this->order_invoiced_date,0,10)) {
			$d = new DateTime($_POST['i2']);
			if (!is_null($d)) $update['order_invoiced_date'] = array('s',$d->format('Y-m-d'));
		}
		if (isset($_POST['i3']) && $_POST['i3']!=substr($this->invoice_paid_complete,0,10)) {
			$d = new DateTime($_POST['i3']);
			if (!is_null($d)) $update['invoice_paid_complete'] = array('s',$d->format('Y-m-d'));
		}
		if (isset($_POST['o13']) && $_POST['o13']!=$this->shipping_from) $update['shipping_from'] = array('i',$_POST['o13']);
		if (isset($_POST['o14']) && $_POST['o14']!=$this->shipping_to) $update['shipping_to'] = array('i',$_POST['o14']);
		if (isset($_POST['i4']) && $_POST['i4']!=$this->remit_to) $update['remit_to'] = array('i',$_POST['i4']);
		$update['last_update_date'] = array('s',$now->format('Y-m-d H:i:s'));
		$update['last_update_by'] = array('i',$_SESSION['dbuserid']);
		
		// Create UPDATE String
		
		if (count($update)<=2) { // last update is always set
			echo 'fail|Nothing to update';
			return;
		}
		$q = 'UPDATE sales_header SET ';
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
		$q .= ' WHERE sales_order_number=?';
		$ctr++;
		$bp_types .= 'i';
		$bp_values[$ctr] = $this->sales_order_number;
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
		$this->resetHeader();
		$this->resetDetail();
		$now = new DateTime();
		$id = $_POST['sales_order_number'];
		if ((!is_integer($id) && !ctype_digit($id)) || $id<1) {
			echo 'fail|Invalid sales order number for updating';
			return;
		}
		$this->display($id,'update'); // Display already has the logic for loading the record.  TODO: Refactor into separate function.
		if (is_null($this->sales_order_number)) {
			echo 'fail|Invalid sales order number for updating';
			return;
		}
		if ((!isset($_POST['sales_order_line']) || (!is_integer($_POST['sales_order_line']) && !ctype_digit($_POST['sales_order_line']))) || 
			!isset($this->detail_array[$_POST['sales_order_line']])) {
			echo 'fail|Invalid sales order line for updating';
			return;
		}
		$result = $this->unarrayifyDetail($_POST['sales_order_line']);
		if (!$result) {
			echo 'fail|Cannot update detail line '.$_POST['sales_order_line'].'. There is a problem getting the detail record.';
			return;
		}
		$update = array();
		if (isset($_POST['parent_line']) && $_POST['parent_line']!=$this->parent_line) $update[] = array('i',$_POST['parent_line']);
		
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
} // class _template
?>