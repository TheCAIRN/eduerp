<?php
class Installer {
	private $currentPage;
	public function __construct() {
		$this->currentPage = 1;
	} // constructor
	private function commonHeader($runstart=false) {
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
<?php
if ($runstart) echo '<BODY onLoad="start();">';
else echo '<BODY>';
	} // commonHeader
	private function commonFooter() {
?>
<DIV id="footerbar">&copy; 2021. Cairn University School of Business.  Apache License 2.0<?php /*$footerbar->render();*/ ?>. Modules not provided in the open source project are separately licensed.</DIV>
</BODY>
</HTML>
<?php		
	} // commonFooter
	private function page1() {
?>
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
<INPUT type="radio" name="method" id="method" value="cPanel">cPanel</INPUT>
<INPUT type="radio" name="method" id="method" value="Installer">Installer</INPUT>
<BR />
<LABEL for="host">Server name</LABEL>
<INPUT type="text" name="host" id="host" value="localhost" /><BR />
<LABEL for="adminname">Admin name (only if not using cPanel)</LABEL>
<INPUT type="text" name="adminname" id="adminname" /><BR />
<LABEL for="adminpw">Admin password (only if not using cPanel)</LABEL>
<INPUT type="password" name="adminpw" id="adminpw" /><BR />
<LABEL for="dbname">Database name</LABEL>
<INPUT type="text" name="dbname" id="dbname" /><BR />
<LABEL for="dbuser">Database user</LABEL>
<INPUT type="text" name="dbuser" id="dbuser" /><BR />
<LABEL for="dbpw">Database password</LABEL>
<INPUT type="password" name="dbpw" id="dbpw" /><BR />
<INPUT type="submit" />
</FORM>
</p>
</DIV>
<?php
	} // page 1
	private function page2() {
		if (!isset($_POST['method'])) {
			echo '<DIV style="color: red;">Some of the form fields were not supplied.  Please answer all the questions.</DIV><BR />';
			$this->page1();
			return;
		}
		$method = $_POST['method'];
		if (!isset($_POST['host'])) {
			echo '<DIV style="color: red;">Some of the form fields were not supplied.  Please answer all the questions.</DIV><BR />';
			$this->page1();
			return;
		}
		$host = $_POST['host'];
		if (!isset($_POST['dbname'])) {
			echo '<DIV style="color: red;">Some of the form fields were not supplied.  Please answer all the questions.</DIV><BR />';
			$this->page1();
			return;
		}
		$dbname = $_POST['dbname'];
		if (!isset($_POST['dbuser'])) {
			echo '<DIV style="color: red;">Some of the form fields were not supplied.  Please answer all the questions.</DIV><BR />';
			$this->page1();
			return;
		}
		$dbuser = $_POST['dbuser'];
		if (!isset($_POST['dbpw'])) {
			echo '<DIV style="color: red;">Some of the form fields were not supplied.  Please answer all the questions.</DIV><BR />';
			$this->page1();
			return;
		}
		$dbpw = $_POST['dbpw'];
		if ($method=='cPanel') {
			$link = new mysqli($host,$dbuser,$dbpw,$dbname);
			if ($link->connect_error) {
				echo '<DIV style="color: red;">I could not connect to the database with the information you provided.
				When using cPanel, you must create the database and user first; as well as associate the user with the database.  
				You may also have typed some of the information wrong.  Please make sure the database has been created and the information is correct, and try again.</DIV><BR />';
				$this->page1();
				return;
			}
		} else {  // cPanel ^ | v installer
			if (!isset($_POST['adminname']) || !isset($_POST['adminpw'])) {
				echo '<DIV style="color: red;">When creating the database from the installer, an admin user and password must be supplied.</DIV><BR />';
				$this->page1();
				return;
			}
			$adminname = $_POST['adminname'];
			$adminpw = $_POST['adminpw'];
			$link = new mysqli($host,$adminname,$adminpw,'mysql');
			if ($link->connect_error) {
				echo '<DIV style="color: red;">The admin credentials you supplied were not correct.  Please double check and try again.</DIV><BR />';
				$this->page1();
				return;
			}
			$s1 = $link->query('CREATE DATABASE '.$link->real_escape_string($dbname));
			if ($s1===false) {
				echo '<DIV style="color: red;">'.$link->error.'</DIV><BR />';
				$link->close();
				$this->page1();
				return;
			}
			// GRANT commands cannot be prepared.
			$s2 = $link->query('GRANT ALL ON '.$link->real_escape_string($dbname).'.* TO '.$link->real_escape_string($dbuser).'@'."'".
				$host."' IDENTIFIED BY '".$link->real_escape_string($dbpw)."'");
			if ($s2===false) {
				echo '<DIV style="color: red;">'.$link->error.'</DIV><BR />';
				$link->close();
				$this->page1();
				return;
			}
			$link->close();
			$link = new mysqli($host,$dbuser,$dbpw,$dbname);
			if ($link->connect_error) {
				echo '<DIV style="color: red;">The new database was created successfully, but for some reason I still cannot connect to it.  
				Please check your server error logs and try again, using the cPanel method.</DIV><BR />';
				$this->page1();
				return;
			}
			$link->close();
			$fp = fopen('globals.php','w');
			if ($fp===false) {
				echo '<DIV style="color: red;">The new database was created successfully, but for some reason I cannot write the globals.php file.  
				Please check your server error logs, making any necessary permission changes, or create the file manually, and try again, using the cPanel method.</DIV><BR />';
				$this->page1();
				return;				
			}
			fputs($fp,"<?php\r\n");
			fputs($fp,'$dbhost='."'".$host."';\r\n");
			fputs($fp,'$dbuser='."'".$dbuser."';\r\n");
			fputs($fp,'$dbpass='."'".$dbpw."';\r\n");
			fputs($fp,'$dbname='."'".$dbname."';\r\n");
			fputs($fp,"?>\r\n");
			fclose($fp);
			
			/* Get list of setup files */
			$dirlist = dir('setup/0_mysql/');
			if (is_null($dirlist) || $dirlist===false) {
				echo '<DIV style="color: red;">The new database was created successfully, but for some reason I cannot read the database setup directory.   
				Please check your installation and try again, using the cPanel method.</DIV><BR />';
				$this->page1();
				return;
			}
			$filearray = array();
			while (false !== ($entry = $dirlist->read())) {
				if ($entry!='.' && $entry!='..') {
					$filearray[] = $entry;
				}
			}
			sort($filearray);	
			/* Create Javascript and HTML to load files and update progress bar */
?>
<SCRIPT type="text/javascript">
var filecount = <?php echo count($filearray); ?>;
var files = [
<?php
	for($i=0;$i<count($filearray);$i++)
		echo "'setup/0_mysql/".$filearray[$i]."',\r\n";
?>
	];
var processing=0;
function processFile(fname) {
	$("#status").html("Processing <b>"+fname+"</b>");
	var w = Math.floor(100*(processing/filecount));
	$("#pbarline").progressbar({value:w});
	$.post('index.php',{installerpage:3,filename:fname},function(data) {
		if (data!="success") $("#errors").html($("#errors").html()+'<B>'+fname+'</B><BR />'+data+'<BR />');
		processing++;
		// We must wait for one file to finish before proceeding to the next.
		if (processing < filecount) processFile(files[processing]);
		else {
			$("#errors").html($("#errors").html()+'<BR /><B>Processing is complete.  Click "OK" to continue. '+ 
				'The username is "admin" and there is no password.  Change this as your first priority</B><BR />'+
				"<BUTTON onClick=\"window.location = '.';\">OK</BUTTON>");
		}
	});
} // processFile()
function start() {
	processFile(files[processing]);
}
</SCRIPT>
<DIV>The database has been created and I can connect to it.  I will now begin loading the tables.</DIV>
<DIV id="pbar" style="height: 16px; width=200px; border: solid 2px black;"><DIV id="pbarline" style="height: 16px; width=1px; background: blue;"></DIV></DIV>
<DIV id="status"></DIV>
<DIV id="errors"></DIV>
<?php			
		} // if cPanel or installer
	} // page 2
	private function page3() {
		if (!isset($_POST['filename'])) {
			echo 'No file has been set to be loaded.<BR />';
			return;
		}
		$fname = $_POST['filename'];
		if (!file_exists($fname)) {
			echo $fname.' does not exist.<BR />';
			return;
		}
		$fp = fopen($fname,'r');
		if ($fp===false) {
			echo $fname.' could not be opened.<BR />';
			return;
		}
		require('globals.php');
		$link = new mysqli($dbhost,$dbuser,$dbpass,$dbname);
		if ($link->connect_error) {
			echo 'I could not establish a connection to the database.<BR />';
			return;
		}
		$buffer = '';
		$success = true;
		$incomment = false;
		while ($line=fgets($fp)) {
			if (strpos($line,'--')!==false) $line = substr($line,0,strpos($line,'--'));
			if (strpos($line,'/*')!==false && !$incomment) {
				$line = substr($line,0,strpos($line,'/*'));
				$incomment = true;
			} elseif ($incomment && strpos($line,'*/')!==false) {
				$line = substr($line,strpos($line,'*/')+2);
				$incomment = false;
			} elseif ($incomment) $line = ''; // Only do this if $incomment wasn't set during this fgets.
			$buffer .= $line;
			if (strpos($buffer,';')!==false) {
				$result = $link->query($buffer);
				$buffer = '';
				if ($result===false) {
					$success = false;
					echo $link->error.'<BR />';
				}
			}
		}
		if (strlen(trim($buffer))>0) {
			$result = $link->query($buffer);
			$buffer = '';
			if ($result===false) {
				$success = false;
				echo $link->error.'<BR />';
			}
		}
		$link->close();
		if ($success) echo 'success';
	}
	public function page($pagenum) {
		if ($pagenum==3) {
			$this->page3();
			return;
		}
		$this->commonHeader(true);
		if ($pagenum==1) $this->page1();
		if ($pagenum==2) $this->page2();
		$this->commonFooter();
	} // page()
} // class Installer
?>
