<?php
session_name('eduerpcfg');
session_start();
spl_autoload_register(function ($class) {
    include 'classes/' . $class . '.php';
});
include('globals.php');
$link = new mysqli($dbhost,$dbuser,$dbpass,$dbname);
if ($link->connect_error) {
	echo $link->connect_error;
	unset($link);
	die();
}
// Add security check here

// Retrieve attachment
$att_id = $_REQUEST['id'];
if (!is_integer($att_id) && !ctype_digit($att_id)) {
	echo 'Invalid attachment id.';
	unset($link);
	die();
}
$q = 'SELECT file_name,uri,data,attachment_type_code FROM aa_attachments a JOIN aa_attachment_types t ON a.attachment_type_id=t.attachment_type_id WHERE attachment_id=?;';
$stmt = $link->prepare($q);
$stmt->bind_param('i',$i1);
$i1 = $att_id;
$result = $stmt->execute();
$stmt->store_result();
$stmt->bind_result($filename,$uri,$data,$att_type);
$stmt->fetch();
header("Content-disposition: attachment; filename=".basename($filename));
if ($att_type=='image') {
	header("Content-type: image");
} elseif ($att_type=='pdf') {
	header("Content-type: application/pdf");
}
if (empty($data)) readfile($filename);
else echo $data;
$stmt->close();
$link->close();
?>