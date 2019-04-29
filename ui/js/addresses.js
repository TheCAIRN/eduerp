function embeddedAddressSearch(id) {
	$.post('jq.php',{jquery:'embedded',module:'addresses',mode:'lookup',id:id,data:$("#"+id).val()},function(data) {
		$("#"+id+"-div").html(data);
	});
} // embeddedAddressSearch()
function embeddedAddressNew(id) {

} // embeddedAddressNew()
function embeddedAddressList(id) {
	$.post('jq.php',{jquery:'embedded',module:'addresses',mode:'lookup',id:id,data:''},function(data) {
		$("#"+id+"-div").html(data);
	});
} // embeddedAddressList()
function embeddedAddressSelect(id) {
	var address_id = $("#"+id+"-select :selected").val();
	$.post('jq.php',{jquery:'embedded',module:'addresses',mode:'display',id:id,data:address_id},function(data) {
		$("#"+id+"-div").html(data);
	});
} // embeddedAddressSelect()
function embeddedAddressNewSearch(id) {
	
} // embeddedAddressNewSearch()