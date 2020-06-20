function getHumanInputFields(prefix) {

} // getHumanInputFields()
function saveHumansHeader(prefix) {

} // saveHumansHeader()
function embeddedHumansSearch(id) {
	$.post('jq.php',{jquery:'embedded',module:'humans',mode:'lookup',id:id,data:$("#"+id).val()},function(data) {
		$("#"+id+"-div").html(data);
	});
} // embeddedHumansSearch()
function embeddedHumansNew(id) {
	$.post('jq.php',{jquery:'embedded',module:'humans',mode:'new',id:id,data:''},function(data) {
		$("#"+id+"-div").html(data);
	});
} // embeddedHumansNew()
function embeddedHumansSave(id) {
	$.post('jq.php',{jquery:'embedded',module:'humans',mode:'save',id:id,data:getHumanInputFields(id)},function(data) {
		$("#"+id+"-div").html(data);
	});
} // embeddedHumansSave()
function embeddedHumansList(id) {
	$.post('jq.php',{jquery:'embedded',module:'humans',mode:'lookup',id:id,data:''},function(data) {
		$("#"+id+"-div").html(data);
	});
} // embeddedHumansList()
function embeddedHumansSelect(id) {
	var Humans_id = $("#"+id+"-select :selected").val();
	$.post('jq.php',{jquery:'embedded',module:'humans',mode:'display',id:id,data:human_id},function(data) {
		$("#"+id+"-div").html(data);
	});
} // embeddedHumansSelect()
function embeddedHumansNewSearch(id) {
	
} // embeddedHumansNewSearch()