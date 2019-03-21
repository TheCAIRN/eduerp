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
		saveBOMDetail();
	})
	.fail(function() {
		$("#messagebar").html('<DIV class="errorMessage">I could not contact the database. Your data has <B>NOT</B> been saved.</DIV>');
	});	
}
function saveBOMDetail() {
	var row = $("#pur_detail_table tr:last").attr("id");
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
			
		}
	})
	.fail(function() {
		$("#messagebar").html('<DIV class="errorMessage">I could not contact the database. Your data has <B>NOT</B> been saved.</DIV>');
	});		
}
function newBOMDetailRow() {
	var bomid = $("#bom_id").val();
	if (bomid<1) {
		saveBOMHeader();
	} else {
		saveBOMDetail();
	}
}