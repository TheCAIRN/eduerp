var pur_orderkey;
function savePurchasingHeader() {
	var orderkey = document.getElementById("purchase_order_number").value;
	var vendorid = $("#vendor_id option:selected").val();
	var orderdate_date = document.getElementById("order_date-date").value;
	var orderdate_time = document.getElementById("order_date-time").value;
	var orderreference = document.getElementById("purchase_order_reference").value;
	var entityid = $("#entity_id option:selected").val();
	var divisionid = $("#division_id option:selected").val();
	var departmentid = $("#department_id option:selected").val();
	var termsid = $("#terms option:selected").val();
	var rev_enabled = $("#rev_enabled").is(":checked");
	var rev_number = $("#rev_number").val();
	// Perform validation
	console.log("Order date: "+orderdate_date+" "+orderdate_time);
	console.log("Rev enabled: "+rev_enabled);
	if (rev_number < 0) rev_number = 1;
	// Submit to server
	var mode;
	if (orderkey<=0) mode = "insertRecord";
	else mode = "updateRecord";
	$.post("jq.php",{jquery:mode,module:"purchasing",level:"header",
		orderkey:orderkey,vendorid:vendorid,orderdate_date:orderdate_date,orderdate_time:orderdate_time,orderreference:orderreference,
		entityid:entityid,divisionid:divisionid,departmentid:departmentid,termsid:termsid,rev_enabled:rev_enabled,rev_number:rev_number},function (data) {
		var fields = data.split("|");
		if (fields[0]=="inserted") {
			$("#purchase_order_number").val(fields[1]);
			$("#messagebar").html('<DIV class="successMessage">Data saved.</DIV>');
			pur_orderkey = fields[1];
			if (fields.length>2) {
				// fields[2] = order date when the submission is blank.
				var od = fields[2].split(" ");
				$("#order_date-date").val(od[0]);
				$("#order_date-time").val(od[1].substr(0,5));
			}
		}
		if (fields[1]=="updated") {
			$("#messagebar").html('<DIV class="successMessage">Data saved.</DIV>');
			pur_orderkey = orderkey;
		}
		savePurchasingDetail();
	})
	.fail(function() {
		$("#messagebar").html('<DIV class="errorMessage">I could not contact the database. Your data has <B>NOT</B> been saved.</DIV>');
	});
}
function savePurchasingDetail() {
	var row = $("#pur_detail_table tr:last").attr("id");
	var orderkey = $("#purchase_order_number").val();
	var orderlinekey = $("#pur_detail_id").val();
	var orderlinenum = $("#po_line").val();
	var parentlinenum = $("#parent_line").val();
	var itemid = $("#item_id option:selected").val();
	var quantity = $("#quantity").val();
	var quantity_uom = $("#quantity_uom option:selected").val();
	var price = $("#price").val();
	var gl_account = $("#gl_account_id option:selected").val();
	var rev_enabled = $("#"+row+"-rev_enabled:first-child").is(":checked");
	var rev_number = $("#"+row+"-rev_number:first-child").val();
	var entityid = $("#entity_id option:selected").val();
	var divisionid = $("#division_id option:selected").val();
	var departmentid = $("#department_id option:selected").val();
	// Perform validation
	if (orderkey=="" || orderkey < 1) orderkey = pur_orderkey;
	if (orderkey=="" || orderkey < 1) {
		$("#messagebar").html('<DIV class="errorMessage">It appears there was an issue getting the purchase order number for the header.  The details require that number in order to save.</DIV>');
	}
	if (rev_number < 0) rev_number = 1;	
	// Submit to server
	var mode;
	if (orderlinekey<=0) mode = "insertRecord";
	else mode = "updateRecord";
	$.post("jq.php",{jquery:mode,module:"purchasing",level:"detail",orderkey:orderkey,orderlinekey:orderlinekey,orderlinenum:orderlinenum,parentlinenum:parentlinenum,itemid:itemid,
		quantity:quantity,quantity_uom:quantity_uom,price:price,gl_account_id:gl_account,rev_enabled:rev_enabled,rev_number:rev_number,entityid:entityid,
		divisionid:divisionid,departmentid:departmentid},function(data) {
		var fields = data.split("|");
		if (fields[0]=="inserted") {
			$("#pur_detail_id").val(fields[1]);
			$("#messagebar").html('<DIV class="successMessage">Data saved.</DIV>');
		}
	})
	.fail(function() {
		$("#messagebar").html('<DIV class="errorMessage">I could not contact the database. Your data has <B>NOT</B> been saved.</DIV>');
	});
}