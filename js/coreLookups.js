function saveTerms(prefix) {
	var inputs = {
			terms_id:$("#terms_id").val()
			,terms_code:$("#terms_code").val()
			,terms_name:$("#terms_name").val()
			,terms_type:$("#terms_type").val()
			,terms_basis:$("#terms_basis").val()
			,tier1_type:$("#tier1_type option:selected").val()
			,discount_percent_1:$("#discount_percent_1").val()
			,discount_days_1:$("#discount_days_1").val()
			,tier2_type:$("#tier2_type option:selected").val()
			,discount_percent_2:$("#discount_percent_2").val()
			,discount_days_2:$("#discount_days_2").val()
			,tier3_type:$("#tier3_type option:selected").val()
			,discount_percent_3:$("#discount_percent_3").val()
			,discount_days_3:$("#discount_days_3").val()
			,status:$("#status option:selected").val()
	};
	var mode;
	if (inputs.terms_id==0 || inputs.terms_id=='') mode='insertRecord';
	else mode='updateRecord';
	$.post("jq.php",{jquery:mode,module:'terms',level:'header',data:inputs},function(data) {
		var fields = data.split("|");
		if (fields[0]=="inserted") {
			$("#"+prefix+"terms_id").val(fields[1]);
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
} // saveTerms
function saveSystemOptions(prefix) {
	var inputs = {
		option_id:$("#option_id").val()
		,option_value:$("#option_value").val()	
	};
	var mode;
	if (inputs.option_id==0 || inputs.option_id=='') mode='insertRecord';
	else mode='updateRecord';
	$.post("jq.php",{jquery:mode,module:'terms',level:'header',data:inputs},function(data) {
		var fields = data.split("|");
		if (fields[0]=="inserted") {
			$("#"+prefix+"terms_id").val(fields[1]);
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
} // saveTerms
