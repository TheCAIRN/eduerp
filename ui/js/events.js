var NewRecordAttachmentPane = '';
function logout() {
	$.post('jq.php',{jquery:'logoff'},function(data) {
		location.reload(true);
	});
} // logout()
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
	if (whichModule=="CoreLookups" || whichModule=="Items" || whichModule=="Insights" || whichModule=="Accounting") {
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
function newSearch(whichModule) {
	var moduleName = whichModule;
	if (whichModule=='ItemManager') moduleName='ItemSetup';
	$.post('jq.php',{jquery:'moduleSearchSpace',module:moduleName},function (data) {
		if (data.length > 0) $("#core").html(data);
		updateDiv('messagebar');
		updateDiv('toolbar');
	});
} // newSearch()
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
	//console.log(kvp);
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
} // viewRecord()
function returnToResultsList(screen) {
	$.post('jq.php',{jquery:'listResultsAgain',cs:screen},function(data) {
		if (data.length > 0) $("#core").html(data);
		updateDiv('messagebar');
		updateDiv('toolbar');
	});
} // returnToResultsList()
function newRecord() {
	$.post('jq.php',{jquery:'newRecord'},function(data) {
		if (data.length > 0) $("#core").html(data);
		updateDiv('messagebar');
		updateDiv('toolbar');
	});
} // newRecord()
function editRecord(whichModule,id) {
	$.post('jq.php',{jquery:'editRecord',module:whichModule,id:id},function(data) {
		if (data.length > 0) $("#core").html(data);
		updateDiv('messagebar');
		updateDiv('toolbar');
	});
} // editRecord()
function saveRecord(whichModule) {
	if (whichModule=="Purchasing") {
		savePurchasingHeader();
	}
	if (whichModule=="Addresses") {
		saveAddressesHeader('');
	}
	if (whichModule=="ItemManager") {
		saveItemHeader('');
	}
	if (whichModule=="BOM") {
		saveBOMHeader();
	}
	if (whichModule=="EntityResource") {
		saveEntityResourceHeader();
	}
	if (whichModule=="CustomerTypes") {
		saveCustomerTypesHeader();
	}
	if (whichModule=="Customer") {
		saveCustomerHeader();
	}
	if (whichModule=="CustomerDC") {
		saveCustomerDCHeader();
	}
	if (whichModule=="CustomerStoreTypes") {
		saveCustomerStoreTypesHeader();
	}
	if (whichModule=="CustomerStores") {
		saveCustomerStoresHeader();
	}
	if (whichModule=="Consumers") {
		saveConsumersHeader();
	}
	if (whichModule=="SalesOrders") {
		saveSalesOrdersHeader();
	}
} // saveRecord()
function addDetailRow(whichModule) {
	if (whichModule=="Purchasing") {
		newPurchasingDetailRow();
	}
	if (whichModule=="BOM") {
		newBOMDetailRow();
	}
	if (whichModule=="SalesOrders") {
		newSalesOrdersDetailRow();
	}
} // addDetailRow()
function showNewRecordAttachmentPane() {
	
} // showNewRecordAttachmentPane()
function onClick_addFile() {
	// TODO: Change to Drag & Drop: https://makitweb.com/drag-and-drop-file-upload-with-jquery-and-ajax/
	var attachmentPrimaryKey = $("#attachmentPrimaryKey").val();
	var supportsAttachments = $("#supportsAttachments").val();
	var attachmentCurrentRecord = $("#attachmentCurrentRecord").val();
	var attachmentDescription = $("#attachmentDescription").val();
	var attachmentType = $("#AttachmentTypeSelect option:selected").val();
	var fd = new FormData();
	fd.append('file',$("#attachmentAddFile")[0].files[0]);
	fd.append('jquery','attachFile');
	fd.append('primaryKey',attachmentPrimaryKey);
	fd.append('currentRecord',attachmentCurrentRecord);
	fd.append('tablename',supportsAttachments);
	fd.append('attachmentType',attachmentType);
	fd.append('description',attachmentDescription);
	$.ajax({
		url: 'jq.php'
		,type: 'post'
		,data: fd
		,contentType: false
		,processData: false
		,dataType: 'json'
		,success: function (data) {
			var fields = data.split("|");
			if (fields[0]=='success') {
				// TODO: Add DIV above entry fields in attachment fieldset.
			}
			if (fields[0]=='fail') {
				$("#messagebar").html('<DIV class="errorMessage">'+fields[1]+'</DIV>');
			}
			console.log(data);
		}
	});
	
} // onClick_addFile()
function onClick_addNote() {
	var supportsNotes = $("#supportsNotes").val();
	var notePrimaryKey = $("#notePrimaryKey").val();
	var noteCurrentRecord = $("#noteCurrentRecord").val();
	var seq = $("#seq").val();
	var noteText = $("#noteText").val();
	$.post("jq.php",{jquery:"addNote",supportsNotes:supportsNotes,notePrimaryKey:notePrimaryKey,noteCurrentRecord:noteCurrentRecord,seq:seq,noteText:noteText},
		function (data) {
			var fields = data.split("|");
			if (fields[0]=='success') {
				// TODO: Add DIV above entry fields in notes fieldset.
			}
			if (fields[0]=='fail') {
				$("#messagebar").html('<DIV class="errorMessage">'+fields[1]+'</DIV>');
			}
			console.log(data);
	});
}