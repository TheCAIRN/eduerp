var currentBOM = 0;
function saveBOMHeader() {
	var bomid = $("#bom_id").val();
	var endresult = $("#resulting_product_id option:selected").val();
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
		if (fields[1]=="updated") {
			$("#messagebar").html('<DIV class="successMessage">Data saved.</DIV>');
			currentBOM = orderkey;
		}
		updateDiv('messagebar');
		saveBOMDetail();
	})
	.fail(function() {
		$("#messagebar").html('<DIV class="errorMessage">I could not contact the database. Your data has <B>NOT</B> been saved.</DIV>');
	});	
}
function saveBOMDetail() {
	var row = $("#bom_detail_table tr:last").attr("id");
	var bomid = $("#bom_id").val();
	var bomdetail = $("#bom_detail_id").val();
	var stepnumber = $("#step_number").val();
	var steptype = $("#step_type option:selected").val();
	var component = $("#component_product_id option:selected").val();
	var componentqty = $("#component_quantity_used").val();
	var process = $("#bom_step_id").val();
	var processtime = $("#seconds_to_process").val();
	var subbom = $("#sub_bom_id option:selected").val();
	var instructions = $("#"+row+"-description:first-child").val();
	var rev_enabled = $("#"+row+"-rev_enabled:first-child").is(":checked");
	var rev_number = $("#"+row+"-rev_number:first-child").val();
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
			$("#bom_detail_id").val(fields[1]);
			$("#messagebar").html('<DIV class="successMessage">Data saved.</DIV>');
			// Create new detail line
			var detailtable = document.getElementById("bom_detail_table");
			var oldtr = $("#bom_detail_table tr:last");
			var newtr = detailtable.insertRow(1);
			var newrow = "row"+(Number(row.substr(3))+1);
			newtr.id=newrow;
			var newcell = newtr.insertCell(0);
			newcell.id = newrow+"-bom_detail_id";
			newcell.innerText = bomdetailid;
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
			newcell.innerText = $("#component_product_id option:selected").text();
			$("#component_product_id").val("");
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
			newcell.innerHTML = "<BUTTON onClick=\"editBOMDetailRow("+newrow+");\">Edit</BUTTON>";
		}
		updateDiv('messagebar');
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
function editBOMDetailRow(rownum) {
	
}