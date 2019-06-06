var sales_orderkey;
function saveSalesOrdersHeader() {
	var ordernum = $("#sales_order_number").val();
	var parent = $("#parent").val();
	var ordertype = $("#sales_order_type option:selected").val();
	var orderstatus = $("#sales_order_status option:selected").val();
	var customerid = $("#customer_id option:selected").val();
	var buyer = $("#buyer option:selected").val();
	var seller = $("#seller option:selected").val();
	var entity = $("#entity_id option:selected").val();
	var division = $("#division_id option:selected").val();
	var dept = $("#department_id option:selected").val();
	var invent = $("#inventory_entity option:selected").val();
	var currency = $("#currency_code option:selected").val();
	var visible = $("#visible").is("checked");
	var revenabled = $("#rev_enabled").is("checked");
	var revnumber = $("#rev_number").val();
	
	var quotenum = $("#quote_number").val();
	var quoteapproved = $("#quote_approved_by option:selected").val();
	var quotegiven = $("#quote_given_date-date").val();
	var quoteexpires = $("#quote_expires_date-date").val();
	
	var customerpo = $("#customer_purchase_order_number").val();
	var customerdept = $("#customer_department").val();
	var customerpg = $("#customer_product_group").val();
	var store = $("#store_code").val();
	var terms = $("#terms option:selected").val();
	var orderdate = $("#order_date-date").val();
	var creditrelease = $("#credit_release_date-date").val();
	var startship = $("#ship_window_start-date").val();
	var endship = $("#ship_window_end-date").val();
	var routeby = $("#must_route_by-date").val();
	var mustarrive = $("#must_arrive_by-date").val();
	var cancelleddate = $("#order_cancelled_date-date").val();
	
	var wavenum = $("#wave_number").val();
	var wavedate = $("#wave_date-date").val();
	var invneeded_d = $("#inventory_needed_by-date").val();
	var invneeded_t = $("#inventory_needed_by-time").val();
	var invpulled_d = $("#inventory_pulled_complete-date").val();
	var invpulled_t = $("#inventory_pulled_complete-time").val();
	var invpacked_d = $("#inventory_packed_complete-date").val();
	var invpacked_t = $("#inventory_packed_complete-time").val();
	
	var shipper = $("#fv_vendor_id option:selected").val();
	var bol = $("#bill_of_lading").val();
	var rrc = $("#rrc").val();
	var loadid = $("load_id").val();
	var routed_d = $("#routing_requested-date").val();
	var routed_t = $("#routing_requested-time").val();
	var pickup_d = $("#pickup_scheduled_for-date").val();
	var pickup_t = $("#pickup_scheduled_for-time").val();
	var invloaded_d = $("#inventory_loaded_complete-date").val();
	var invloaded_t = $("#inventory_loaded_complete-time").val();
	var boldate = $("#bol_date-date").val();
	var ordershipped = $("#order_shipped_date-date").val();
	
	var invoicenum = $("#invoice_number").val();
	var invoicedate = $("#order_invoiced_date-date").val();
	var paiddate = $("#invoice_paid_complete-date").val();
	
	var shipfrom = $("#shipping_from-address_id").text();
	var shipto = $("#shipping_to-address_id").text();
	var remitto = $("#remit_to-address_id").text();
	
	// TODO: Add validations
	var message;
	
	
	var mode;
	if (ordernum<=0) mode = "insertRecord";
	else mode = "updateRecord";
	var mode;
	if (ordernum<=0) mode = "insertRecord";
	else mode = "updateRecord";
	$.post("jq.php",{jquery:mode,module:"salesorders",level:"header",
		h1:ordernum,h2:parent,h3:ordertype,h4:orderstatus,h5:customerid,h6:buyer,h7:seller,h8:entity,h9:division,h10:dept,
		h11:invent,h12:currency,h13:visible,h14:revenabled,h15:revnumber,
		q1:quotenum,q2:quoteapproved,q3:quotegiven,q4:quoteexpires,
		o1:customerpo,o2:customerdept,o3:customerpg,o4:store,o5:terms,o6:orderdate,o7:creditrelease,o8:startship,o9:endship,o10:routeby,
		o11:mustarrive,o12:cancelleddate,
		p1:wavenum,p2:wavedate,p3d:invneeded_d,p3t:invneeded_t,p4d:invpulled_d,p4t:invpulled_t,p5d:invpacked_d,p5t:invpacked_t,
		s1:shipper,s2:bol,s3:rrc,s4:loadid,s5d:routed_d,s5t:routed_t,s6d:pickup_d,s6t:pickup_t,s7d:invloaded_d,s7t:invloaded_t,
		s8:boldate,s9:ordershipped,
		i1:invoicenum,i2:invoicedate,i3:paiddate,
		o13:shipfrom,o14:shipto,i4:remitto
	},
	function(data) {
		var fields = data.split("|");
		if (fields[0]=="inserted") {
			$("#sales_order_number").val(fields[1]);
			$("#messagebar").html('<DIV class="successMessage">Data saved.</DIV>');
			sales_orderkey = fields[1];
			if (fields.length>2) {
				// fields[2] = order date when the submission is blank.
				if (orderstatus=='Q') $("#quote_given_date-date").val(fields[1]);
				if (orderstatus=='O') $("#order_date-date").val(fields[1]);
			}
		}
		if (fields[0]=="updated") {
			$("#messagebar").html('<DIV class="successMessage">Data saved.</DIV>');
			sales_orderkey = ordernum;
		}	
		if (fields[0]=="fail") {
			updateDiv('messagebar');
		}
		saveSalesOrdersDetail();
	})
	.fail(function() {
		$("#messagebar").html('<DIV class="errorMessage">I could not contact the database. Your data has <B>NOT</B> been saved.</DIV>');
	});
} // saveSalesOrdersHeader()
function createNewSalesOrdersDetailLine() {
	
} // createNewDetailLine()
function saveSalesOrdersDetail() {
	// In some browsers, the value of the control doesn't become persistent until after this function completes.
	var ordernum = sales_orderkey;
	if (ordernum==null || ordernum==0)
		ordernum = $("#sales_order_number").val();
	var row = $("#sales_detail_table tr:last").attr("id");
	var linenum = $("#sales_order_line").val();
	var parentline = $("#parent_line").val();
	var entity = $("#dentity_id option:selected").val();
	var division = $("#ddivision_id option:selected").val();
	var department = $("#ddepartment_id option:selected").val();
	var custline = $("#customer_line").val();
	var itemid = $("#item_id-product_id").text();
	var qty1 = $("#quantity_requested").val();
	var qty2 = $("#quantity_shipped").val();
	var qty3 = $("#quantity_returned").val();
	var qty4 = $("#quantity_backordered").val();
	var qty5 = $("#quantity_cancelled").val();
	var uom = $("#quantity_uom option:selected").val();
	var price = $("#price").val();
	var discpct = $("#discount_percent").val();
	var discamt = $("#discount_amount").val();
	var rtlh = $("#retail_high").val();
	var rtll = $("#retail_low").val();
	var cred_date = $("#dcredit_release_date-date").val();
	var wave_date = $("#dwave_date-date").val();
	var assigned_to = $("#assigned_to option:selected").val();
	var inv_needed_date = $("#dinventory_needed_by-date").val();
	var inv_needed_time = $("#dinventory_needed_by-time").val();
	var inv_location = $("#inventory_location option:selected").val();
	var inv_pulled_date = $("#dinventory_pulled_complete-date").val();
	var inv_pulled_time = $("#dinventory_pulled_complete-time").val();
	var inv_pulled_by = $("#dinventory_pulled_by option:selected").val();
	var inv_packed_date = $("#dinventory_packed_complete-date").val();
	var inv_packed_time = $("#dinventory_packed_complete-time").val();
	var inv_packed_by = $("#dinventory_packed_by option:selected").val();
	var inv_loaded_date = $("#dinventory_loaded_complete-date").val();
	var inv_loaded_time = $("#dinventory_loaded_complete-time").val();
	var inv_loaded_by = $("#dinventorY_loaded_by option:selected").val();
	var line_shipped = $("#line_shipped_date-date").val();
	var line_invoiced = $("#line_invoiced_date-date").val();
	var line_cancelled = $("#line_cancelled_date-date").val();
	var visible = $("#dvisible").is("checked");
	var rev_enabled = $("#drev_enabled").is("checked");
	var rev_number = $("#drev_number").val();
	if (itemid=="") {
		$("#messagebar").html('<DIV class="errorMessage">You must select an item to save this order. Your data has <B>NOT</B> been saved.</DIV>');
		return;
	}
	// Submit to server
	var mode;
	if (linenum==0) mode="insertRecord";
	else mode="updateRecord";
	$.post("jq.php",{jquery:mode,module:"salesorders",level:"detail",
		sales_order_number:ordernum,sales_order_line:linenum,parent_line:parentline,dentity_id:entity,
		ddivision_id:division,ddepartment_id:department,customer_line:custline,item_id:itemid,
		quantity_requested:qty1,quantity_shipped:qty2,quantity_returned:qty3,quantity_backordered:qty4,
		quantity_cancelled:qty5,quantity_uom:uom,price:price,discount_percent:discpct,discount_amount:discamt,
		dcredit_release_date:cred_date,dwave_date:wave_date,assigned_to:assigned_to,
		dinventory_needed_bydate:inv_needed_date,dinventory_needed_bytime:inv_needed_time,
		inventory_location:inv_location,dinventory_pulled_completedate:inv_pulled_date,dinventory_pulled_completetime:inv_pulled_time,
		dinventory_pulled_by:inv_pulled_by,dinventory_packed_completedate:inv_packed_date,
		dinventory_packed_completetime:inv_packed_time,dinventory_packed_by:inv_packed_by,
		dinventory_loaded_completedate:inv_loaded_date,dinventory_loaded_completetime:inv_loaded_time,
		dinventory_loaded_by:inv_loaded_by,line_shipped_date:line_shipped,line_invoiced_date:line_invoiced,
		line_cancelled_date:line_cancelled,dvisible:visible,drev_enabled:rev_enabled,drev_number:rev_number,
		},function(data) {
		var fields = data.split("|");
		if (fields[0]=="inserted") {
			$("#sales_order_line").val(fields[1]);
			$("#messagebar").html('<DIV class="successMessage">Data saved.</DIV>');
			createNewSalesOrdersDetailLine();
		} // if inserted
	})
	.fail(function() {
		$("#messagebar").html('<DIV class="errorMessage">I could not contact the database. Your data has <B>NOT</B> been saved.</DIV>');
	});
	
}
function newSalesOrdersDetailRow() {
	
}
function onChange_SalesOrderStatus() {
	var orderstatus = $("#salesOrderStatus option:selected").val();
	$("#sales_header-quote_edit legend").siblings().hide(); 
	$("#sales_header-ordered_edit legend").siblings().hide(); 
	$("#sales_header-processing_edit legend").siblings().hide(); 
	$("#sales_header-shipping_edit legend").siblings().hide();
	$("#sales_header-invoicing_edit legend").siblings().hide();
	if (orderstatus=='Q') $("#sales_header-quote_edit legend").siblings().show(); 
	if (orderstatus=='O') $("#sales_header-ordered_edit legend").siblings().show(); 
	if (orderstatus=='P') $("#sales_header-processing_edit legend").siblings().show(); 
	if (orderstatus=='S') $("#sales_header-shipping_edit legend").siblings().show();
	if (orderstatus=='I') $("#sales_header-invoicing_edit legend").siblings().show();
}