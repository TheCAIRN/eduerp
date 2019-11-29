<?php
class Security extends ERPBase {
	public static function makeToken($numchars) {
		$token = '';
		$charoptions = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";
		if (!is_integer($numchars)) return '';
		for ($n=0;$n<$numchars;$n++) {
			$token .= substr($charoptions,rand(0,61),1);
		}
		return $token;
	}
	public static function displayLoginScreen() {
?>
<!DOCTYPE HTML>
<HTML>
<HEAD>
<TITLE>EduERP Client (Default)</TITLE>
<META charset="UTF-8" />
<META name="author" content="Prof. Michael Sabal" />
<LINK rel="icon" type="image/png" href="images/favicon-32x32.png" sizes="32x32" />
<LINK rel="stylesheet" type="text/css" href="css/main.css" />
<STYLE>
body {
	background-color: #209820;
}
h3 {
	margin: auto;
	align: center;
	color: white;
	font-family: sans-serif;
	font-size: 2em;
	display: block;
	text-align: center;
}
form {
	margin-top: 15%;
	width: 98%;
}
form input {
	display: block;
	font-size: 2em;
	align: center;
	margin: auto;
	margin-top: 15px;
	border: solid 3px black;
}
</STYLE>
</HEAD>
<BODY>
<FORM method="POST" action="index.php" id="LoginForm">
<h3>Login</h3>
<INPUT type="text" id="username" name="username" placeholder="Username or email" />
<INPUT type="password" id="auth" name="auth" placeholder="Password" />
<INPUT type="submit" value="Login" />
</FORM>
</BODY>
</HTML>
<?php
	} // displayLoginScreen
	public static function processLoginScreen($link) {
		$success = false;
		$q = "SELECT u.user_id FROM sec_users u 
			JOIN cx_humans h ON u.human_id=h.human_id 
			LEFT OUTER JOIN cx_email_associations ea on ea.human_id=h.human_id
			LEFT OUTER JOIN cx_emails em on em.email_id=ea.email_id
			WHERE CAST(u.auth AS CHAR CHARACTER SET utf8mb4)=SHA2(CONCAT(salt2,?),256) AND
			(h.alias=? OR CONCAT(email_user,'@',email_domain)=?) AND u.user_status='A'";
		$stmt = $link->prepare($q);
		$stmt->bind_param('sss',$p1,$p2,$p3);
		$p1 = $_POST['auth'];
		$p2 = $p3 = $_POST['username'];
		$result = $stmt->execute();
		if ($result!==false) {
			$stmt->store_result();
			$stmt->bind_result($uid);
			$success = $stmt->num_rows > 0;
		}
		if (!$success) Security::displayLoginScreen();
		else {
			$stmt->fetch();
			$_SESSION['dbuserid'] = $uid;
			$token = Security::makeToken(128);
			$upd = 'UPDATE sec_users SET token=?,last_access=NOW() WHERE user_id=?';
			$stmt->close();
			$upds = $link->prepare($upd);
			$upds->bind_param('si',$u1,$u2);
			$u1 = $token;
			$u2 = $uid;
			$upds->execute();
			$_SESSION['usertoken'] = $token;
			header('Location: index.php');
		} // login Successful
	} // processLoginScreen
} // class Security
?>