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
$toolbar = new Toolbar();
//$navbar = new Navbar();
//$footerbar = new Footerbar();
$workspace = new Workspace($link);
/* TODO: Add security module */
$_SESSION['dbuserid'] = 1;
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
<SCRIPT type="text/javascript" src="js/events.js"></SCRIPT>
<SCRIPT type="text/javascript" src="js/addresses.js"></SCRIPT>
<SCRIPT type="text/javascript" src="js/item.js"></SCRIPT>
<SCRIPT type="text/javascript" src="js/purchasing.js"></SCRIPT>
<SCRIPT type="text/javascript" src="js/BOM.js"></SCRIPT>
<SCRIPT type="text/javascript" src="js/customers.js"></SCRIPT>
<SCRIPT type="text/javascript" src="js/salesorders.js"></SCRIPT>
<SCRIPT type="text/javascript">
var currentScreen = 0;
</SCRIPT>
</HEAD>
<BODY>
<DIV id="logobar"><?php $logobar->render(); ?></DIV>
<DIV id="toolbar"><?php $toolbar->render(); ?></DIV>
<DIV id="messagebar"><?php $messagebar->render(); ?></DIV>
<DIV id="content">
<DIV id="leftnav">Project Roadmap<BR /><?php /*$navbar->render(); */?>
<UL>
<LI>&#x2714; Lookup entities</LI>
<LI>&#x2714; Lookup items</LI>
<LI>&#x2714; Lookup vendors</LI>
<LI>&#x2714; Lookup vendor catalog</LI>
<LI>&#x2714; Create new purchase order</LI>
<LI>&#x2714; Create new bill of materials</LI>
<LI>&#x2714; Embedded item search</LI>
<LI>Create new production</LI>
<LI>Add functionality to Entities: <UL><LI>production capacity</LI><LI>create new entity</LI><LI>Modify entity</LI></UL></LI>
<LI>&#x2714; Lookup customers</LI>
<LI>&#x2714; Lookup / add addresses</LI>
<LI>Create sales orders</LI>
<LI>Update sales orders for shipping</LI>
<LI>Create shipment</LI>
<LI>Create invoice</LI>
<LI>Update purchase order with tracking and receiving</LI>
</UL>
</DIV>
<DIV id="core"><?php $workspace->render(); ?></DIV>
</DIV>
<DIV id="footerbar">&copy; 2019. Cairn University School of Business.  Apache License 2.0<?php /*$fotterbar->render();*/ ?></DIV>
</BODY>
</HTML>
<?php
$link->close();
?>