<?php
	$baseurl = (isset($_SERVER['HTTPS']) && !empty($_SERVER['HTTPS']) ? 'https' : 'http').'://'.$_SERVER['HTTP_HOST'];
	echo '<SCRIPT type="text/javascript">function openHickoryEDI(mode) {var url="'.$baseurl.'/edi"; if (mode=="admin") url += "/admin"; var win=window.open(url,"_blank"); win.focus();}</SCRIPT>';
	echo "<DIV id=\"HickoryEDIProcessModuleIcon\" class=\"DashboardIcon\" onClick=\"openHickoryEDI();\">Process</DIV>\r\n";
	echo "<DIV id=\"HickoryEDIAdminModuleIcon\" class=\"DashboardIcon\" onClick=\"openHickoryEDI('admin');\">Admin</DIV>\r\n";
?>