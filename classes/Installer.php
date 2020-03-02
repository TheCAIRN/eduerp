<?php
class Installer {
	private $currentPage;
	public function __construct() {
		$this->currentPage = 1;
	} // constructor
	private function page1() {
?>
<!DOCTYPE HTML>
<HTML>
<HEAD>
<TITLE>EduERP Client (Default)</TITLE>
<META charset="UTF-8" />
<META name="author" content="Prof. Michael Sabal" />
<LINK rel="icon" type="image/png" href="images/favicon-32x32.png" sizes="32x32" />
<LINK rel="stylesheet" type="text/css" href="css/main.css" />
<LINK rel="stylesheet" type="text/css" href="css/installer.css" />
<LINK rel="stylesheet" type="text/css" href="css/core.css" />
<SCRIPT type="text/javascript" src="js/jquery-3.3.1.min.js"></SCRIPT>
<SCRIPT type="text/javascript" src="js/jquery-ui.min.js"></SCRIPT>
<SCRIPT type="text/javascript">
var currentScreen = 0;
</SCRIPT>
</HEAD>
<BODY>
<DIV id="installGreeting">
<p>
Welcome to eduERP.  Whether you are installing this system for classroom use or for business use, we hope you find it ready to meet your needs.
Note that those portions of the system available via <a href="https://github.com/TheCAIRN/eduerp">our Github repository</a> are licensed with the 
Open Source <a href="https://opensource.org/licenses/Apache-2.0">Apache License 2.0</a>.  You are free to redistribute, modify, and extend the system 
with both open source and proprietary code.  However, this notice regarding the open source licensing of the core must remain intact, and those 
changes to the core system not returned to the main repository must be noted in the code, even if those modifications continue to be distributed under an open source license.
</p><p>
If you are installing eduERP with a hosting provider using cPanel, you must create your database and database user with their tools.  <BR />
<FORM action="index.php" method="POST">
<INPUT type="hidden" name="installer" value="true" />
<INPUT type="hidden" name="installerpage" value="2" />
<LABEL for="method">Which method will you be using to create the database</LABEL>
<INPUT type="radio" name="method" id="method" value="cPanel" />
<INPUT type="radio" name="method" id="method" value="Installer" />
<BR />
<LABEL for="host">Server name</LABEL>
<INPUT type="text" name="host" id="host" value="localhost" />
<LABEL for="adminname">Admin name (only if not using cPanel)</LABEL>
<INPUT type="text" name="adminname" id="adminname" />
<LABEL for="adminpw">Admin password (only if not using cPanel)</LABEL>
<INPUT type="password" name="adminpw" id="adminpw" />
<LABEL for="dbname">Database name</LABEL>
<INPUT type="text" name="dbname" id="dbname" />
<LABEL for="dbuser">Database user</LABEL>
<INPUT type="text" name="dbuser" id="dbuser" />
<LABEL for="dbpw">Database password</LABEL>
<INPUT type="password" name="dbpw" id="dbpw" />
<INPUT type="submit" />
</FORM>
</p>
</DIV>
<DIV id="footerbar">&copy; 2020. Cairn University School of Business.  Apache License 2.0<?php /*$footerbar->render();*/ ?>. Modules not provided in the open source project are separately licensed.</DIV>
</BODY>
</HTML>
<?php
		
	} // page 1
	public function page($pagenum) {
	
	} // page()
} // class Installer
?>