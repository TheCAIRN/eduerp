function getAddressInputFields(prefix) {
	return {
		id:$("#"+prefix+"id").val()
		,building_number:$("#"+prefix+"building_number").val()
		,street:$("#"+prefix+"street").val()
		,attention:$("#"+prefix+"attention").val()
		,apartment:$("#"+prefix+"apartment").val()
		,postal_box:$("#"+prefix+"postal_box").val()
		,line2:$("#"+prefix+"line2").val()
		,line3:$("#"+prefix+"line3").val()
		,city:$("#"+prefix+"city").val()
		,spc_abbrev:$("#"+prefix+"spc_abbrev option:selected").val()
		,postal_code:$("#"+prefix+"postal_code").val()
		,country:$("#"+prefix+"country option:selected").val()
		,county:$("#"+prefix+"county").val()
		,maidenhead:$("#"+prefix+"maidenhead").val()
		,latitude:$("#"+prefix+"latitude").val()
		,longitude:$("#"+prefix+"longitude").val()
		,osm_id:$("#"+prefix+"osm_id").val()
	};
}
function embeddedAddressSearch(id) {
	$.post('jq.php',{jquery:'embedded',module:'addresses',mode:'lookup',id:id,data:$("#"+id).val()},function(data) {
		$("#"+id+"-div").html(data);
	});
} // embeddedAddressSearch()
function embeddedAddressNew(id) {
	$.post('jq.php',{jquery:'embedded',module:'addresses',mode:'new',id:id,data:''},function(data) {
		$("#"+id+"-div").html(data);
	});
} // embeddedAddressNew()
function embeddedAddressSave(id) {
	$.post('jq.php',{jquery:'embedded',module:'addresses',mode:'save',id:id,data:getAddressInputFields(id)},function(data) {
		$("#"+id+"-div").html(data);
	});
} // embeddedAddressSave()
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