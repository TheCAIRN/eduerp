<?php
session_name('eduerpcfg');
session_start();
if (isset($_SESSION) && isset($_SESSION['barstatus'])) {
	echo $_SESSION['barstatus'];
} else {
	echo '0';
}
?>