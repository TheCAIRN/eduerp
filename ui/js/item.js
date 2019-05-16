function gtinCheck() {

} // gtinCheck()
function gtinAssign() {

} // gtinAssign()
function getItemInputFields(prefix) {
	
} // getItemInputFields()
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