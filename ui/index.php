<?php
session_name('eduerpcfg');
session_start();
// From php.net
spl_autoload_register(function ($class) {
    include 'classes/' . $class . '.php';
});
$logobar = new Logobar();
$toolbar = new Toolbar();
$messagebar = new Messagebar();
$navbar = new Navbar();
$footerbar = new Footerbar();
$workspace = new Workspace();
?>
<!DOCTYPE HTML>
<HTML>
<HEAD>
<TITLE>EduERP Client (Default)</TITLE>
<META charset="UTF-8" />
<META name="author" content="Prof. Michael Sabal" />
<SCRIPT type="text/javascript" src="js/jquery-3.3.1.min.js"></SCRIPT>
<SCRIPT type="text/javascript" src="js/jquery-ui.min.js"></SCRIPT>
<SCRIPT type="text/javascript">

</SCRIPT>
</HEAD>
<BODY>
<DIV id="logobar"><?php $logobar->render(); ?></DIV>
<DIV id="toolbar"><?php $toolbar->render(); ?></DIV>
<DIV id="messagebar"><?php $messagebar->render(); ?></DIV>
<DIV id="content">
<DIV id="leftnav"><?php $navbar->render(); ?></DIV>
<DIV id="core"><?php $workspace->render(); ?></DIV>
</DIV>
<DIV id="footerbar"><?php $fotterbar->render(); ?></DIV>
</BODY>
</HTML>