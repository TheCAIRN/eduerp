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
		if (data.length > 8 && data.substr(0,8)=='inserted') {
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

}
function editProductionDetailRow() {

}