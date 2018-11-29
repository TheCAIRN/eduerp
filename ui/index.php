<?php
session_name('eduerpcfg');
session_start();
// From php.net
spl_autoload_register(function ($class) {
    include 'classes/' . $class . '.php';
});
$messagebar = new Messagebar();
//if (!isset($_SESSION['link'])) {
	include('globals.php');
	$link = new mysqli($dbhost,$dbuser,$dbpass,$dbname);
	if ($link->connect_error) {
		$messagebar->addError($link->connect_error);
		unset($link);
//	} else {
//		$_SESSION['link'] = $link;
	}
//} else {
//	$link = $_SESSION['link'];
//}
$logobar = new Logobar();
//$toolbar = new Toolbar();
//$navbar = new Navbar();
//$footerbar = new Footerbar();
$workspace = new Workspace($link);
?>
<!DOCTYPE HTML>
<HTML>
<HEAD>
<TITLE>EduERP Client (Default)</TITLE>
<META charset="UTF-8" />
<META name="author" content="Prof. Michael Sabal" />
<LINK rel="icon" type="image/png" href="images/favicon-32x32.png" sizes="32x32" />
<LINK rel="stylesheet" type="text/css" href="css/main.css" />
<LINK rel="stylesheet" type="text/css" href="css/logobar.css" />
<LINK rel="stylesheet" type="text/css" href="css/messagebar.css" />
<LINK rel="stylesheet" type="text/css" href="css/core.css" />
<SCRIPT type="text/javascript" src="js/jquery-3.3.1.min.js"></SCRIPT>
<SCRIPT type="text/javascript" src="js/jquery-ui.min.js"></SCRIPT>
<SCRIPT type="text/javascript">
var currentScreen = 0;
function updateDiv(whichDiv) {
	
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
	$.post('jq.php',{jquery:'moduleSearchSpace',module:moduleName},function (data) {
		if (data.length > 0) $("#core").html(data);
		updateDiv('messagebar');
		updateDiv('toolbar');
	});
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
</SCRIPT>
</HEAD>
<BODY>
<DIV id="logobar"><?php $logobar->render(); ?></DIV>
<DIV id="toolbar">TOOLBAR<?php /*$toolbar->render();*/ ?></DIV>
<DIV id="messagebar"><?php $messagebar->render(); ?></DIV>
<DIV id="content">
<DIV id="leftnav">HOME<BR /><?php /*$navbar->render(); */?></DIV>
<DIV id="core"><?php $workspace->render(); ?></DIV>
</DIV>
<DIV id="footerbar">FOOTERBAR<?php /*$fotterbar->render();*/ ?></DIV>
</BODY>
</HTML>
<?php
$link->close();
?>