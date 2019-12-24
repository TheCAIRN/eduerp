<?php
/*
 * Setup section for all jquery functions
 */
session_name('eduerpcfg');
session_start();
spl_autoload_register(function ($class) {
    include 'classes/' . $class . '.php';
});
/*
 * End of setup section
 */
if (isset($_SESSION) && isset($_SESSION['barstatus']) && $_SESSION['barstatus']===0) {
	echo '0';
} elseif (isset($_POST['jquery'])) {
	if ($_POST['jquery']=='toolbar') {
		$tb = new Toolbar();
		$tb->render();
	}
	if ($_POST['jquery']=='messagebar') {
		
		$mb = new Messagebar();
		$mb->render();
	}
	if ($_POST['jquery']=='leftnav') {
		$nv = new Navbar();
	}
} else {
	echo '<BUTTON onClick="mainMenu();">HOME</BUTTON>';
}
?>