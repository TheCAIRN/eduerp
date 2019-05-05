var cust_key;
function saveCustomerTypesHeader() {
	
}
function saveCustomerHeader() {
	var custid = document.getElementById("customer_id").value;
	var custcode = document.getElementById("customer_code").value;
	var custname = document.getElementById("customer_name").value;
	var custtypecode = $("#cust_type_code option:selected").val();
	var parent = $("#parent option:selected").val();
	var custgroup = document.getElementById("customer_group").value;
	var supplier = document.getElementById("supplier_code").value;
	var glAccount = $("#gl_account_id option:selected").val();
	var terms = $("#default_terms option:selected").val();
	var status = $("#status option:selected").val();
	var rev_enabled = $("#rev_enabled").is(":checked");
	var rev_number = $("#rev_number").val();
	if (rev_number < 0) rev_number = 1;
	var primary_addr = $("#primary_address-address_id").text();
	var billing_addr = $("#billing_address-address_id").text();
	var message = "";
	if (custcode=="") {
		message = message + "Customer code is required.  ";
	}
	if (custname=="") {
		message = message + "Customer name is required.  ";
	}
	if (glAccount=="") {
		message = message + "GL Account is required.  ";
	}
	if (status=="") {
		message = message + "Status is required.  ";
	}
	if (primary_addr=="" || primary_addr=="[new]") {
		message = message + "Please select and save the primary address before continuing.  ";
	}
	if (billing_addr=="" || billing_addr=="[new]") {
		message = message + "Please select and save the billing address before continuing.  ";
	}
	if (message!="") {
		$("#messagebar").html('<DIV class="errorMessage">'+message+'</DIV>');
		return;
	}
	var mode;
	if (custid<=0) mode = "insertRecord";
	else mode = "updateRecord";
	$.post("jq.php",{jquery:mode,module:"customer",level:"header",customer_id:custid,customer_code:custcode,customer_name:custname,
		cust_type_code:custtypecode,parent:parent,customer_group:custgroup,supplier_code:supplier,gl_account_id:glAccount,default_terms:terms,
		status:status,rev_enabled:rev_enabled,rev_number:rev_number,primary_address:primary_addr,billing_address:billing_addr},
	function(data) {
		var fields = data.split("|");
		if (fields[0]=="inserted") {
			$("#customer_id").val(fields[1]);
			$("#messagebar").html('<DIV class="successMessage">Data saved.</DIV>');
			custkey = fields[1];
		}
		if (fields[1]=="updated") {
			$("#messagebar").html('<DIV class="successMessage">Data saved.</DIV>');
			custkey = custid;
		}			
	})
	.fail(function() {
		$("#messagebar").html('<DIV class="errorMessage">I could not contact the database. Your data has <B>NOT</B> been saved.</DIV>');
	});
}
function saveCustomerDCHeader() {
	
}
function saveCustomerStoreTypesHeader() {
	
}
function saveCustomerStoresHeader() {
	
}
function saveConsumersHeader() {
	
}