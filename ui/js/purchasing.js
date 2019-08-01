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
		if (fields[0]=="fail") {
			updateDiv('messagebar');
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
	var itemid = $("#item_id-product_id").text();
	var quantity = $("#quantity").val();
	var quantity_uom = $("#quantity_uom option:selected").val();
	var price = $("#price").val();
	var gl_account = $("#gl_account_id option:selected").val();
	var rev_enabled = $("#"+row+"-rev_enabled:first-child").is(":checked");
	var rev_number = $("#"+row+"-rev_number:first-child").val();
	var entityid = $("#entity_id option:selected").val();
	var divisionid = $("#division_id option:selected").val();
	var departmentid = $("#department_id option:selected").val();
	var fv_vendor_id = $("#fv_vendor_id option:selected").val();
	var quantity_shipped = $("#quantity_shipped").val();
	var date_shipped_date = $("#date_shipped-date").val();
	var date_shipped_time = $("#date_shipped-time").val();
	var tracking_number = $("#tracking_number").val();
	var quantity_received = $("#quantity_received").val();
	var date_received_date = $("#date_received-date").val();
	var date_received_time = $("#date_received-time").val();
	var received_by = $("#received_by option:selected").val();
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
		quantity:quantity,quantity_uom:quantity_uom,price:price,gl_account_id:gl_account,
		fv_vendor_id:fv_vendor_id,quantity_shipped:quantity_shipped,date_shipped_date:date_shipped_date,date_shipped_time:date_shipped_time,tracking_number:tracking_number,
		quantity_received:quantity_received,date_received_date:date_received_date,date_received_time:date_received_time,
		rev_enabled:rev_enabled,rev_number:rev_number,entityid:entityid,
		divisionid:divisionid,departmentid:departmentid},function(data) {
		var fields = data.split("|");
		if (fields[0]=="inserted") {
			$("#pur_detail_id").val(fields[1]);
			$("#messagebar").html('<DIV class="successMessage">Data saved.</DIV>');
		} else if (fields[0]=='updated') {
			var updrow = $("#pur_detail_edit td:nth-child(1):contains("+orderlinekey+")").closest("tr").attr("id");
			if (!updrow) {
				alert("Cannot update the screen.  Please click the 'view' button in the toolbar, then 'edit' to refresh.");
			} else {
				$("#"+updrow+"-po_line").text($("#po_line").val());
				$("#po_line").val("");
				$("#"+updrow+"-parent_line").text($("#parent_line").val());
				$("#parent_line").val("");
				$("#"+updrow+"-item_id").text($("#item_id-product_id").text()+" "+$("#item_id-product_code").text());
				embeddedItemNewSearch("item_id");
				$("#"+updrow+"-quantity").text($("#quantity").val());
				$("#quantity").val("");
				$("#"+updrow+"-quantity_uom").text($("#quantity_uom option:selected").val());
				$("#quantity_uom").val("");
				$("#"+updrow+"-price").text($("#price").val());
				$("#price").val("");
				$("#"+updrow+"-gl_account_id").text($("#gl_account_id option:selected").val());
				$("#gl_account_id").val("");
				$("#"+updrow+"-fv_vendor_id").text($("#fv_vendor_id option:selected").val());
				$("#fv_vendor_id").val("");
				$("#"+updrow+"-quantity_shipped").text($("#quantity_shipped").val());
				$("#quantity_shipped").val("");
				$("#"+updrow+"-date_shipped").text($("#date_shipped-date").val()+" "+$("#date_shipped-time").val());
				$("#date_shipped-date").val("");
				$("#date_shipped-time").val("");
				$("#"+updrow+"-tracking_number").text($("#tracking_number").val());
				$("#tracking_number").val("");
				$("#"+updrow+"-quantity_received").text($("#quantity_received").val());
				$("#quantity_received").val("");
				$("#"+updrow+"-date_received").text($("#date_received-date").val()+" "+$("#date_received-time").val());
				$("#date_received-date").val("");
				$("#date_received-time").val("");
				$("#"+updrow+"-received_by").text($("#received_by").val());
				$("#received_by").val("");
				if (rev_enabled) $("#"+updrow+"-rev_enabled").text("Yes"); else $("#"+updrow+"-rev_enabled").text("No");
				$("#"+updrow+"-rev_number").text(rev_number);
				$("#"+row+"-rev_number:first-child").val("");

			}
		}
		if (fields[0]=="fail") {
			updateDiv('messagebar');
		}
	})
	.fail(function() {
		$("#messagebar").html('<DIV class="errorMessage">I could not contact the database. Your data has <B>NOT</B> been saved.</DIV>');
	});
} // savePurchasingDetail()
function newPurchasingDetailRow() {
	var orderid = $("#purchase_order_number").val()
	if (orderid<1 || orderid=="") 
		savePurchasingHeader();
	else
		savePurchasingDetail();
}
function editPurchasingDetailRow(rowid) {
	$("#pur_detail_id").val($("#"+rowid+"-pur_detail_id").text());
	$("#po_line").val($("#"+rowid+"-po_line").text());
	$("#parent_line").val($("#"+rowid+"-parent_line").text());
	// Saving or updating a detail row puts both the ID and the code in the table.  EmbeddedItemSelect requires only the ID.
	var itemid = $("#"+rowid+"-item_id").text().split(" ");
	itemid = itemid[0];
	embeddedItemSelect('item_id',itemid);
	$("#quantity").val($("#"+rowid+"-quantity").text());
	$("#quantity_uom").val($("#"+rowid+"-quantity_uom").text());
	$("#price").val($("#"+rowid+"-price").text());
	$("#gl_account_id").val($("#"+rowid+"-gl_account_id").text());
	$("#fv_vendor_id").val($("#"+rowid+"-fv_vendor_id").text());
	$("#quantity_shipped").val($("#"+rowid+"-quantity_shipped").text());
	$("#tracking_number").val($("#"+rowid+"-tracking_number").text());
	$("#date_shipped-date").val($("#"+rowid+"-date_shipped").text().substring(0,10));
	$("#date_shipped-time").val($("#"+rowid+"-date_shipped").text().substring(11));
	$("#date_received-date").val($("#"+rowid+"-date_received").text().substring(0,10));
	$("#date_received-time").val($("#"+rowid+"-date_received").text().substring(11));
	$("#quantity_received").val($("#"+rowid+"-quantity_received").text());
	$("#received_by").val($("#"+rowid+"-received_by").text());
	var rev_enabled = $("#"+rowid+"-rev_enabled").text();
	if (rev_enabled=="Y") $("#row0-rev_enabled #rev_enabled").prop("checked",true);
	else $("#row0-rev_enabled #rev_enabled").prop("checked",false);
	$("#row0-rev_number #rev_number").val($("#"+rowid+"-rev_number").text());
}
