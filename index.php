<?php
$done = false;
// https://stackoverflow.com/questions/5106313/redirecting-from-http-to-https-with-php
if (empty($_SERVER['HTTPS']) || $_SERVER['HTTPS'] === "off") {
    $location = 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    header('HTTP/1.1 301 Moved Permanently');
    header('Location: ' . $location);
        $done = true;
	exit(); // using if is better, but somewhat too complicated for this application
}
session_name('eduerpcfg');
session_start();
// From php.net
spl_autoload_register(function ($class) {
    include 'classes/' . $class . '.php';
});
$messagebar = new Messagebar();
$needInstaller = false;
if (isset($_POST['installerpage'])) $needInstaller = true;
if (file_exists('globals.php')) include('globals.php');
else $needInstaller = true;
$link = null;
if (!isset($dbname)) {
	$needInstaller = true;
} else {
	$link = new mysqli($dbhost,$dbuser,$dbpass,$dbname);
	if ($link->connect_error) {
		$messagebar->addError($link->connect_error);
		unset($link);
		$needInstaller = true;
	}
}
Options::LoadSessionOptions($link);
$openLogin = false;
if (isset($_SESSION['Options']) && isset($_SESSION['Options']['OPEN_LOGIN'])) {
	if ($_SESSION['Options']['OPEN_LOGIN']=='TRUE') $openLogin = true;
}
if (!$openLogin && !isset($_SESSION['dbuserid'])) {
	$_SESSION['dbuserid'] = -1;
}
/* TODO: Add security module */
if ($needInstaller) {
	$installer = new Installer();
	if (isset($_POST['installerpage'])) $installer->page($_POST['installerpage']);
	else $installer->page(1);
} elseif ($openLogin || (isset($_SESSION['dbuserid']) && $_SESSION['dbuserid']>0)) {
	$_SESSION['sitename'] = $sitename;
	$logobar = new Logobar();
	$toolbar = new Toolbar();
	$workspace = new Workspace($link);
	if (!isset($_SESSION['dbuserid'])) $_SESSION['dbuserid'] = 1; // Needed in the event $openLogin is true and the dbuserid hasn't been set.
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
<SCRIPT type="text/javascript" src="js/jquery-3.4.1.min.js"></SCRIPT>
<SCRIPT type="text/javascript" src="js/jquery-ui.min.js"></SCRIPT>
<SCRIPT type="text/javascript" src="js/events.js"></SCRIPT>
<SCRIPT type="text/javascript" src="js/coreLookups.js"></SCRIPT>
<SCRIPT type="text/javascript" src="js/humans.js"></SCRIPT>
<SCRIPT type="text/javascript" src="js/addresses.js"></SCRIPT>
<SCRIPT type="text/javascript" src="js/item.js"></SCRIPT>
<SCRIPT type="text/javascript" src="js/purchasing.js"></SCRIPT>
<SCRIPT type="text/javascript" src="js/production.js"></SCRIPT>
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
<LI>Add functionality to Entities: <UL><LI>production capacity</LI><LI>create new entity</LI><LI>Modify entity</LI></UL></LI>
<LI>Modify vendors</LI>
<LI>Update sales orders for shipping</LI>
<LI>Create shipment</LI>
<LI>Create invoice</LI>
<LI>Manage freight vendors</LI>
<LI>Dashboards</LI>
<LI>Reports</LI>
</UL>
</DIV>
<DIV id="core"><?php $workspace->render(); ?></DIV>
</DIV>
<DIV id="footerbar">&copy; 2021. Cairn University School of Business.  Apache License 2.0<?php /*$footerbar->render();*/ ?>. Modules not provided in the open source project are separately licensed.</DIV>
</BODY>
</HTML>
<?php
	// end openLogin
} else {
	if (isset($_POST['username']) && isset($_POST['auth']))
		Security::processLoginScreen($link);
	else
		Security::displayLoginScreen();
}
if (!is_null($link)) $link->close();
?>
