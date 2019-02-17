function updateDiv(whichDiv) {
	$.post('barstatus.php',{jquery:whichDiv},function(data) {
		if (data!='0') $('#'+whichDiv).html(data);
	});
} // updateDiv()
function mainMenu() {
	$.post('jq.php',{jquery:'mainMenu'},function(data) {
		if (data.length > 0) $("#core").html(data);
		updateDiv('messagebar');
		updateDiv('toolbar');
	});
} // mainMenu()
function selectModule(whichModule) {
	var moduleName = whichModule.id.replace('ModuleIcon','');
	if (whichModule=="CoreLookups" || whichModule=="Items") {
		$.post('jq.php',{jquery:'moduleSubMenu',module:moduleName},function (data) {
			if (data.length > 0) $("#core").html(data);
			updateDiv('messagebar');
			updateDiv('toolbar');
		});
	} else {
		$.post('jq.php',{jquery:'moduleSearchSpace',module:moduleName},function (data) {
			if (data.length > 0) $("#core").html(data);
			updateDiv('messagebar');
			updateDiv('toolbar');
		});
	}
} // selectModule()
function clearMessages() {
	$.post('jq.php',{jquery:'clearMessages'},function (data) {
		$("#messagebar").html("");
	});
} // clearMessages()
function executeSearch(whichModule) {
	var kvp = [];
	$(".selectPage").children().each(function (index) {
		if ($(this).is("label")) return;
		if ($(this).is("input:text") && $(this).val()!="") {
			kvp[$(this).id] = $(this).val();
		}
		if ($(this).is("select")) {
			var key = $(this).id;
			$(this).find("option:selected").each(function (opt_index) {
				if ($(this).val()=="") return;
				if (kvp[key]=="undefined") kvp[key] = $(this).val();
				else if (typeof kvp[key]=="string") kvp[key] = [kvp[key],$(this).val()];
				else kvp[key].push($(this).val());
			});
		}
	});
	$.post('jq.php',{jquery:'executeSearch',module:whichModule,searchParameters:kvp},function (data) {
		if (data.length > 0) $("#core").html(data);
		updateDiv('messagebar');
		updateDiv('toolbar');
	});
} // executeSearch()
function viewRecord(whichModule,id) {
	$.post('jq.php',{jquery:'viewRecord',module:whichModule,id:id},function(data) {
		if (data.length > 0) $("#core").html(data);
		updateDiv('messagebar');
		updateDiv('toolbar');
	});
}
function returnToResultsList() {
	$.post('jq.php',{jquery:'listResultsAgain'},function(data) {
		if (data.length > 0) $("#core").html(data);
		updateDiv('messagebar');
		updateDiv('toolbar');
	});
}