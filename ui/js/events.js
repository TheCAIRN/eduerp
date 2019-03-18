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
	$(".searchPage").children().each(function (index) {
		var obj = $(this);
		if (obj.is("div")) obj = obj.children().eq(1);
		if (obj.is("label")) return true;
		if (obj.is("input:text") && obj.val()!="") {
			//kvp[obj.attr('id')] = obj.val();
			kvp.push([obj.attr('id'),obj.val()]);
			console.log(obj.attr('id')+": "+obj.val());
		}
		if (obj.is("select")) {
			var key = obj.id;
			obj.find("option:selected").each(function (opt_index) {
				if ($(this).val()=="") return false;
				if (kvp[key]=="undefined") kvp[key] = $(this).val();
				else if (typeof kvp[key]=="string") kvp[key] = [kvp[key],$(this).val()];
				else kvp[key].push($(this).val());
				return true;
			});
		}
		return true;
	});
	console.log(kvp);
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
function newRecord() {
	$.post('jq.php',{jquery:'newRecord'},function(data) {
		if (data.length > 0) $("#core").html(data);
		updateDiv('messagebar');
		updateDiv('toolbar');
	});
}
function saveRecord(whichModule) {
	if (whichModule=="Purchasing") {
		savePurchasingHeader();
	}
}
function addDetailRow(whichModule) {
	if (whichModule=="Purchasing") {
		
	}
}