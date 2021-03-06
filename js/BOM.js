var currentBOM = 0;
function saveBOMHeader() {
	var bomid = $("#bom_id").val();
	var endresult = $("#resulting_product_id-product_id").text();
	var quantity = $("#resulting_quantity").val();
	var description = $("#description").val();
	var rev_enabled = $("#rev_enabled").is(":checked");
	var rev_number = $("#rev_number").val();
	// Perform validation
	if (rev_number < 0) rev_number = 1;
	
	// Submit to server
	var mode;
	if (bomid<=0) mode = "insertRecord";
	else mode = "updateRecord";
	$.post("jq.php",{jquery:mode,module:"bom",level:"header",
		bomid:bomid,resultingproductid:endresult,resultingquantity:quantity,description:description,rev_enabled:rev_enabled,rev_number:rev_number},function(data) {
		var fields = data.split("|");
		if (fields[0]=="inserted") {
			$("#bom_id").val(fields[1]);
			$("#messagebar").html('<DIV class="successMessage">Data saved.</DIV>');
			currentBOM = fields[1];
		}
		if (fields[0]=="updated") {
			$("#messagebar").html('<DIV class="successMessage">Data saved.</DIV>');
			currentBOM = orderkey;
		}
		if (fields[0]=="fail") {
			updateDiv('messagebar');
		}
		saveBOMDetail();
	})
	.fail(function() {
		$("#messagebar").html('<DIV class="errorMessage">I could not contact the database. Your data has <B>NOT</B> been saved.</DIV>');
	});	
}
function saveBOMDetail() {
	//var row = $("#bom_detail_table tr:last").attr("id");
	var row = "row0";
	var bomid = $("#bom_id").val();
	var bomdetail = $("#bom_detail_id").val();
	var stepnumber = $("#step_number").val();
	var steptype = $("#step_type option:selected").val();
	var component = $("#component_product_id-product_id").text();
	var componentqty = $("#component_quantity_used").val();
	var process = $("#bom_step_id").val();
	var processtime = $("#seconds_to_process").val();
	var subbom = $("#sub_bom_id option:selected").val();
	var instructions = $("#"+row+"-description").children("#description").val();
	var rev_enabled = $("#"+row+"-rev_enabled:first-child").is(":checked");
	var rev_number = $("#"+row+"-rev_number").children("#rev_number").val();
	// Perform Validation
	if (rev_number < 0) rev_number = 1;
	if (bomid=="" || bomid<0) bomid=currentBOM;
	if (bomid=="" || bomid < 1) {
		$("#messagebar").html('<DIV class="errorMessage">It appears there was an issue getting the BOM ID for the header.  The details require that number in order to save.</DIV>');
	}
	
	// Submit to server
	var mode;
	if (bomdetail<=0) mode = "insertRecord";
	else mode = "updateRecord";
	$.post("jq.php",{jquery:mode,module:"bom",level:"detail",
		bomid:bomid,bomdetailid:bomdetail,stepnumber:stepnumber,steptype:steptype,component:component,componentqty:componentqty,bom_step_id:process,
		processtime:processtime,sub_bom_id:subbom,description:instructions,rev_enabled:rev_enabled,rev_number:rev_number},function(data) {
		var fields = data.split("|");
		if (fields[0]=="inserted") {
			$("#bom_detail_edit td:nth-child(4), #bom_detail_edit th:nth-child(4)").show();
			$("#bom_detail_edit td:nth-child(5), #bom_detail_edit th:nth-child(5)").show();
			$("#bom_detail_edit td:nth-child(6), #bom_detail_edit th:nth-child(6)").show();
			$("#bom_detail_edit td:nth-child(7), #bom_detail_edit th:nth-child(7)").show();
			$("#bom_detail_edit td:nth-child(8), #bom_detail_edit th:nth-child(8)").show();
			$("#bom_detail_id").val(fields[1]);
			$("#messagebar").html('<DIV class="successMessage">Data saved.</DIV>');
			// Create new detail line
			var detailtable = document.getElementById("bom_detail_table");
			var oldtr = $("#bom_detail_table tr:last");
			var newtr = detailtable.insertRow(1);
			//var lastrow = $("#bom_detail_table tr").eq(1).attr("id");
			//var newrow = "row"+(Number(lastrow.substr(3))+1);
			var newrow = "row"+(Number($("#bom_detail_table tr").length-2)); // Headers count as a row, but don't take a number, and the input row starts with 0.
			newtr.id=newrow;
			var newcell = newtr.insertCell(0);
			newcell.id = newrow+"-bom_detail_id";
			newcell.innerText = fields[1];
			$("#bom_detail_id").val("");
			newcell = newtr.insertCell(1);
			newcell.id = newrow+"-step_number";
			newcell.innerText = stepnumber;
			$("#step_number").val("");
			newcell = newtr.insertCell(2);
			newcell.id = newrow+"-step_type";
			newcell.innerText = $("#step_type option:selected").text();
			$("#step_type").val("");
			newcell = newtr.insertCell(3);
			newcell.id = newrow+"-component_product_id";
			newcell.innerText = $("#component_product_id-product_id").text()+" "+$("#component_product_id-product_code").text();
			//$("#component_product_id").val("");
			newcell = newtr.insertCell(4);
			newcell.id = newrow+"-component_quantity_used";
			newcell.innerText = componentqty;
			$("#component_quantity_used").val("");
			newcell = newtr.insertCell(5);
			newcell.id = newrow+"-bom_step_id";
			newcell.innerText = $("#bom_step_id option:selected").text();
			$("#bom_step_id").val("");
			newcell = newtr.insertCell(6);
			newcell.id = newrow+"-seconds_to_process";
			newcell.innerText = processtime;
			$("#seconds_to_process").val("");
			newcell = newtr.insertCell(7);
			newcell.id = newrow+"-sub_bom_id";
			newcell.innerText = $("#sub_bom_id option:selected").text();
			$("#sub_bom_id").val("");
			newcell = newtr.insertCell(8);
			newcell.id = newrow+"-description";
			newcell.innerText = instructions;
			$("#description").val("");
			newcell = newtr.insertCell(9);
			newcell.id = newrow+"-rev_enabled";
			if (rev_enabled) newcell.innerText = "Yes"; else newcell.innerText = "No";
			newcell = newtr.insertCell(10);
			newcell.id = newrow+"-rev_number";
			newcell.innerText = rev_number;
			$("#"+row+"-rev_number:first-child").val("");
			newcell = newtr.insertCell(11);
			newcell.innerHTML = "<BUTTON onClick=\"editBOMDetailRow('"+newrow+"');\">Edit</BUTTON>";
			$("#bom_detail_edit td:nth-child(4), #bom_detail_edit th:nth-child(4)").hide();
			$("#bom_detail_edit td:nth-child(5), #bom_detail_edit th:nth-child(5)").hide();
			$("#bom_detail_edit td:nth-child(6), #bom_detail_edit th:nth-child(6)").hide();
			$("#bom_detail_edit td:nth-child(7), #bom_detail_edit th:nth-child(7)").hide();
			$("#bom_detail_edit td:nth-child(8), #bom_detail_edit th:nth-child(8)").hide();
		} else if (fields[0]=='updated') {
			var updrow = $("#bom_detail_edit td:nth-child(1):contains("+bomdetail+")").closest("tr").attr("id");
			if (!updrow) {
				alert("Cannot update the screen.  Please click the 'view' button in the toolbar, then 'edit' to refresh.");
			} else {
				$("#"+updrow+"-step_number").text(stepnumber);
				$("#step_number").val("");
				$("#"+updrow+"-step_type").text($("#step_type option:selected").text());
				$("#step_type").val("");
				$("#"+updrow+"-component_product_id").text($("#component_product_id-product_id").text()+" "+$("#component_product_id-product_code").text());
				embeddedItemNewSearch("component_product_id");
				$("#"+updrow+"-component_quantity_used").text(componentqty);
				$("#component_quantity_used").val("");
				$("#"+updrow+"-bom_step_id").text($("#bom_step_id option:selected").text());
				$("#bom_step_id").val("");
				$("#"+updrow+"-seconds_to_process").text(processtime);
				$("#seconds_to_process").val("");
				$("#"+updrow+"-sub_bom_id").text($("#sub_bom_id option:selected").text());
				$("#sub_bom_id").val("");
				$("#"+updrow+"-description").text(instructions);
				$("#updrow0 #description").text("");
				if (rev_enabled) $("#"+updrow+"-rev_enabled").text("Yes"); else $("#"+updrow+"-rev_enabled").text("No");
				$("#"+updrow+"-rev_number").text(rev_number);
				$("#"+row+"-rev_number:first-child").val("");
			}
		}
		updateDiv('messagebar');
	})
	.fail(function() {
		$("#messagebar").html('<DIV class="errorMessage">I could not contact the database. Your data has <B>NOT</B> been saved.</DIV>');
	});		
}
function saveBOMSteps() {
	var bom_step_id = $("#bom_step_id").val();
	var bom_step_name = $("#bom_step_name").val();
	var bom_step_description = $("#description").val();
	// Submit to server
	var mode;
	if (bom_step_id<=0) mode = "insertRecord";
	else mode = "updateRecord";
	$.post("jq.php",{jquery:mode,module:"bomsteps",level:"header",
		bom_step_id:bom_step_id,bom_step_name:bom_step_name,description:bom_step_description},function(data) {
		var fields = data.split("|");
		if (fields[0]=="inserted") {
			$("#bom_step_id").val(fields[1]);
			$("#messagebar").html('<DIV class="successMessage">Data saved.</DIV>');
		}
		if (fields[0]=="updated") {
			$("#messagebar").html('<DIV class="successMessage">Data saved.</DIV>');
		}
		if (fields[0]=="fail") {
			$("#messagebar").html('<DIV class="errorMessage">'+fields[1]+'</DIV>');
		}
	})
	.fail(function() {
		$("#messagebar").html('<DIV class="errorMessage">I could not contact the database. Your data has <B>NOT</B> been saved.</DIV>');
	});		
}
function newBOMDetailRow() {
	var bomid = $("#bom_id").val();
	if (bomid<1 || bomid=="") {
		saveBOMHeader();
	} else {
		saveBOMDetail();
	}
}
function editBOMDetailRow(rowid) {
	$("#bom_detail_id").val($("#"+rowid+"-bom_detail_id").text());
	$("#step_number").val($("#"+rowid+"-step_number").text());
	$("#step_type").val($("#"+rowid+"-step_type").text());
	$("#component_quantity_used").val($("#"+rowid+"-component_quantity_used").text());
	$("#bom_step_id").val($("#"+rowid+"-bom_step_id").text());
	$("#seconds_to_process").val($("#"+rowid+"-seconds_to_process").text());
	$("#sub_bom_id").val($("#"+rowid+"-sub_bom_id").text());
	$("#row0-description #description").text($("#"+rowid+"-description").text());
	var rev_enabled = $("#"+rowid+"-rev_enabled").text();
	if (rev_enabled=="Y") $("#row0-rev_enabled #rev_enabled").prop("checked",true);
	else $("#row0-rev_enabled #rev_enabled").prop("checked",false);
	$("#row0-rev_number #rev_number").val($("#"+rowid+"-rev_number").text());
	embeddedItemSelect('component_product_id',$("#"+rowid+"-component_product_id").text());
}