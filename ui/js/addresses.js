function embeddedAddressSearch(id) {
	$.post('jq.php',{jquery:'embedded',module:'addresses',mode:'lookup',id:id,data:$("#"+id).val()},function(data) {
		$("#"+id+"-div").html(data);
	});
} // embeddedAddressSearch()
function embeddedAddressNew(id) {

} // embeddedAddressNew()
function embeddedAddressList(id) {
	$.post('jq.php',{jquery:'embedded',module:'addresses',mode:'lookup',id:id,data:'',function(data) {
		$("#"+id+"-div").html(data);
	});
} // embeddedAddressList()
function embeddedAddressSelect(id) {
	
} // embeddedAddressSelect()
function embeddedAddressNewSearch(id) {
	
} // embeddedAddressNewSearch()