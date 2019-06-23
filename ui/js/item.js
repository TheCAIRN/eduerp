function gtinCheck() {

} // gtinCheck()
function gtinAssign() {
	var entity = $("#entity_id option:selected").val();
	var division = $("#division_id option:selected").val();
	var department = $("#department_id option:selected").val();
	var itemtype = $("#item_type_code option:selected").val();
	$.post('jq.php',{jquery:'itemjq',mode:'gtinAssign',entity_id:entity,division_id:division,department_id:department,item_type_code:itemtype},function(data) {
		var fields = data.split("|");
		$("#gtin").val(fields[1]);
	});
} // gtinAssign()
function getItemInputFields(prefix) {
	return {
		entity_id:$("#"+prefix+"entity_id option:selected").val()
		,division_id:$("#"+prefix+"division_id option:selected").val()
		,department_id:$("#"+prefix+"department_id option:selected").val()
		,item_type_code:$("#"+prefix+"item_type_code option:selected").val()
		,item_category_id:$("#"+prefix+"item_category_id option:selected").val()
		,product_id:$("#"+prefix+"product_id").val()
		,product_code:$("#"+prefix+"product_code").val()
		,product_description:$("#"+prefix+"product_description").val()
		,product_catalog_title:$("#"+prefix+"product_catalog_title").val()
		,product_uom:$("#"+prefix+"product_uom option:selected").val()
		,gtin:$("#"+prefix+"gtin").val()
		,standard_cost:$("#"+prefix+"standard_cost").val()
		,suggested_retail:$("#"+prefix+"suggested_retail").val()
		,wholesale_price:$("#"+prefix+"wholesale_price").val()
		,currency_code:$("#"+prefix+"currency_code option:selected").val()
		,length:$("#"+prefix+"length").val()
		,width:$("#"+prefix+"width").val()
		,height:$("#"+prefix+"height").val()
		,lwh_uom:$("#"+prefix+"lwh_uom option:selected").val()
		,weight:$("#"+prefix+"weight").val()
		,weight_uom:$("#"+prefix+"weight_uom option:selected").val()
		,harmonized_tariff_code:$("#"+prefix+"harmonized_tariff_code").val()
		,tariff_revision:$("#"+prefix+"tariff_revision").val()
		,promotion_start_dated:$("#"+prefix+"promotion_start_date-date").val()
		,promotion_start_datet:$("#"+prefix+"promotion_start_date-time").val()
		,promotion_end_dated:$("#"+prefix+"promotion_end_date-date").val()
		,promotion_end_datet:$("#"+prefix+"promotion_end_date-time").val()
		,product_launch_dated:$("#"+prefix+"product_launch_date-date").val()
		,product_sunset_dated:$("#"+prefix+"product_sunset_date-date").val()
		,product_end_of_support_dated:$("#"+prefix+"product_end_of_support_date-date").val()
		,product_end_of_extended_support_dated:$("#"+prefix+"product_end_of_support_date-date").val()
		,visible:$("#"+prefix+"visible").is("checked")
		,rev_enabled:$("#"+prefix+"rev_enabled").is("checked")
		,rev_number:$("#"+prefix+"rev_number").val()
	};
} // getItemInputFields()
function saveItemHeader(prefix) {
	var inputs = getItemInputFields(prefix);
	var mode;
	if (inputs.product_id==0 || inputs.product_id=='') mode='insertRecord';
	else mode='updateRecord';
	$.post("jq.php",{jquery:mode,module:'itemmanager',level:'header',data:inputs},function(data) {
		var fields = data.split("|");
		if (fields[0]=="inserted") {
			$("#"+prefix+"product_id").val(fields[1]);
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
function embeddedItemSearch(id) {
	$.post('jq.php',{jquery:'embedded',module:'ItemManager',mode:'lookup',id:id,data:$("#"+id).val()},function(data) {
		$("#"+id+"-div").html(data);
	});
} // embeddedItemSearch()
function embeddedItemNew(id) {
	$.post('jq.php',{jquery:'embedded',module:'ItemManager',mode:'new',id:id,data:''},function(data) {
		$("#"+id+"-div").html(data);
	});
} // embeddedItemNew()
function embeddedItemSave(id) {
	$.post('jq.php',{jquery:'embedded',module:'ItemManager',mode:'save',id:id,data:getItemInputFields(id)},function(data) {
		$("#"+id+"-div").html(data);
	});
} // embeddedItemSave()
function embeddedItemList(id) {
	$.post('jq.php',{jquery:'embedded',module:'ItemManager',mode:'lookup',id:id,data:''},function(data) {
		$("#"+id+"-div").html(data);
	});
} // embeddedItemList()
function embeddedItemSelect(id) {
	var Item_id = $("#"+id+"-select :selected").val();
	$.post('jq.php',{jquery:'embedded',module:'ItemManager',mode:'display',id:id,data:Item_id},function(data) {
		$("#"+id+"-div").html(data);
	});
} // embeddedItemSelect()
function embeddedItemNewSearch(id) {
	
} // embeddedItemNewSearch()