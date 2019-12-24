var prod_id;
function saveProductionHeader() {
	var prodkey = $("#prod_id").val();
	var entityid = $("#entity_id").val();
	var divisionid = $("#division_id").val();
	var departmentid = $("#department_id").val();
	var resultingproductid = $("#resulting_product_id-product_id").text();
	var maxqty = $("#maximum_quantity").val();
	var start_date = $("#prod_start-date").val();
	var start_time = $("#prod_start-time").val();
	var due_date = $("#prod_due-date").val();
	var due_time = $("#prod_due-time").val();
	var finished_date = $("#prod_finished-date").val();
	var finished_time = $("#prod_finished-time").val();
	var bomid = $("#bom_id").val();
	var rev_enabled = $("#rev_enabled").is(":checked");
	var rev_number = $("#rev_number").val();
	var mode;
	if (prodkey<=0) mode = "insertRecord";
	else mode = "updateRecord";
	$.post("jq.php",{jquery:mode,module:"production",level:"header",prod_id:prodkey,entity_id:entityid,division_id:divisionid,department_id:departmentid,
		resulting_product_id:resultingproductid,maximum_quantity:maxqty,prod_start_date:start_date,prod_start_time:start_time,prod_due_date:due_date,
		prod_due_time:due_time,prod_finished_date:finished_date,prod_finished_time:finished_time,bom_id:bomid,rev_enabled:rev_enabled,
		rev_number:rev_number},	function(data) {
		if (data.length > 8 && (data.substr(0,8)=='inserted' || data.substr(0,8)=='updated')) {
			var cpos = data.indexOf("\n");
			$("#core").html(data.substr(cpos+1));	
			updateDiv('messagebar');
			updateDiv('toolbar');
		} else if (data.length > 5 && data.substr(0,5)=='fail|') {
			$("#messagebar").html('<DIV class="errorMessage">'+data.substr(5)+'</DIV>');			
		} else {
			updateDiv('messagebar');
		}
	})
	.fail(function() {
		$("#messagebar").html('<DIV class="errorMessage">I could not contact the database. Your data has <B>NOT</B> been saved.</DIV>');
	});
}
function saveProductionDetail() {
	var prodid = $("#prod_id").val();
	var dtlid = $("#prod_detail_id").val();
	// Step number, bom_detail_id, item_consumed_id, item_generated_id, planned_consumed, and planned_generated are all read-only.
	var startdate = $("#step_started-date").val();
	var starttime = $("#step_started-time").val();
	var duedate = $("#step_due-date").val();
	var duetime = $("#step_due-time").val();
	var finisheddate = $("#step_finished-date").val();
	var finishedtime = $("#step_finished-time").val();
	var cost = $("#step_cost").val();
	var currency = $("#currency_code").val();
	var quantity_consumed = $("#quantity_consumed").val();
	var quantity_generated = $("#quantity_generated").val();
	var item_consumed_id = $("#row0-item_consumed_id #item_consumed_id-product_id").val();
	var item_generated_id = $("#row0-item_generated_id #item_generated_id-product_id").val();
	var rev_enabled = $("#row0-rev_enabled:first-child").is(":checked");
	var rev_number = $("#row0-rev_number").children("#rev_number").val();
	if (rev_number < 0) rev_number = 1;
	if (item_consumed_id=="None selected" && quantity_consumed!=0) {
		$("#messagebar").html('<DIV class="errorMessage">You cannot consume a quantity when no item is selected.</DIV>');
		return;
	}
	if (item_generated_id=="None selected" && quantity_generated!=0) {
		$("#messagebar").html('<DIV class="errorMessage">You cannot generate a quantity when no item is selected.</DIV>');
		return;
	}
	if (prodid=="" || prodid < 1) {
		$("#messagebar").html('<DIV class="errorMessage">It appears there was an issue getting the Production ID for the header.  The details require that number in order to save.</DIV>');		
		return;
	}
	if (dtlid=="" || dtlid < 1) {
		$("#messagebar").html('<DIV class="errorMessage">The production module does not allow new lines to be manually added.  A production detail ID must have already been generated to save this row.</DIV>');		
		return;
	}
	$.post("jq.php",{jquery:"updateRecord",module:"production",level:"detail",prod_id:prodid,prod_detail_id:dtlid,step_started_date:startdate,step_started_time:starttime,
		step_due_date:duedate,step_due_time:duetime,step_finished_date:finisheddate,step_finished_time:finishedtime,step_cost:cost,currency_code:currency,
		quantity_consumed:quantity_consumed,quantity_generated:quantity_generated,rev_enabled:rev_enabled,rev_number:rev_number},function(data) {
		var fields = data.split("|");
		if (fields[0]=="updated") {
			var updrow = $("#prod_detail_edit td:nth-child(1):contains("+dtlid+")").closest("tr").attr("id");
			if (!updrow) {
				alert("Cannot update the screen.  Please click the 'view' button in the toolbar, then 'edit' to refresh.");
			} else {
				$("#"+updrow+"-step_started").text(startdate+" "+starttime);
				$("#"+updrow+"-step_due").text(duedate+" "+duetime);
				$("#"+updrow+"-step_finished").text(finisheddate+" "+finishedtime);
				$("#"+updrow+"-step_cost").text(cost);
				$("#"+updrow+"-currency_code").text(currency);
				$("#"+updrow+"-quantity_consumed").text(quantity_consumed);
				$("#"+updrow+"-quantity_generated").text(quantity_generated);
				if (rev_enabled) $("#"+updrow+"-rev_enabled").text("Yes"); else $("#"+updrow+"-rev_enabled").text("No");
				$("#"+updrow+"-rev_number").text(rev_number);
				
				$("#prod_detail_id").val("");
				$("#prod_step_number").val("");
				$("#bom_detail_id").val("");
				embeddedItemSelectReadonly('item_consumed_id',"");
				embeddedItemSelectReadonly('item_generated_id',"");
				$("#step_started-date").val("");
				$("#step_started-time").val("");
				$("#step_due-date").val("");
				$("#step_due-time").val("");
				$("#step_finished-date").val("");
				$("#step_finished-time").val("");
				$("#step_cost").val("");
				$("#currency_code").val("");
				$("#planned_consumed").val("");
				$("#planned_generated").val("");
				$("#quantity_consumed").val("");
				$("#quantity_generated").val("");
				$("#row0-rev_number #rev_number").val("");	
			}
		} else {
			$("#messagebar").html('<DIV class="errorMessage">'+fields[1]+'</DIV>');
		}
	});
}
function editProductionDetailRow(rowid) {
	$("#prod_detail_id").val($("#"+rowid+"-prod_detail_id").text());
	$("#prod_step_number").val($("#"+rowid+"-prod_step_number").text());
	$("#bom_detail_id").val($("#"+rowid+"-bom_detail_id").text());
	embeddedItemSelectReadonly('item_consumed_id',$("#"+rowid+"-item_consumed_id-div #item_consumed_id-product_id").text());
	embeddedItemSelectReadonly('item_generated_id',$("#"+rowid+"-item_generated_id-div #item_generated_id-product_id").text());
	$("#step_started-date").val($("#"+rowid+"-step_started").text().substr(0,10));
	$("#step_started-time").val($("#"+rowid+"-step_started").text().substr(11));
	$("#step_due-date").val($("#"+rowid+"-step_due").text().substr(0,10));
	$("#step_due-time").val($("#"+rowid+"-step_due").text().substr(11));
	$("#step_finished-date").val($("#"+rowid+"-step_finished").text().substr(0,10));
	$("#step_finished-time").val($("#"+rowid+"-step_finished").text().substr(11));
	$("#step_cost").val($("#"+rowid+"-step_cost").text());
	$("#currency_code").val($("#"+rowid+"-currency_code").text());
	$("#planned_consumed").val($("#"+rowid+"-planned_consumed").text());
	$("#planned_generated").val($("#"+rowid+"-planned_generated").text());
	$("#quantity_consumed").val($("#"+rowid+"-quantity_consumed").text());
	$("#quantity_generated").val($("#"+rowid+"-quantity_generated").text());
	var rev_enabled = $("#"+rowid+"-rev_enabled").text();
	if (rev_enabled=="Y") $("#row0-rev_enabled #rev_enabled").prop("checked",true);
	else $("#row0-rev_enabled #rev_enabled").prop("checked",false);
	$("#row0-rev_number #rev_number").val($("#"+rowid+"-rev_number").text());	
}