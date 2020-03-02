<?php
/*
 * eduERP is currently written to use the MySQL database engine, or any forked equivalent.
 *
 * Copy this file to globals.php, and update the fields for your own environment.
 * $dbhost will usually be localhost, unless your web server and MySQL database are
 * on different machines.
 * $dbuser is the database user assigned to the database with the GRANT directive.
 * $dbpass is the plain text password associated with $dbuser
 * $dbname is the database name used with CREATE DATABASE.
 *
 * IMPORTANT:
 * In most environments, the globals.php file should be placed outside the web server environment.
 * A separate globals.php file in the eduerp directory should include only the line 
 * require_once('TruePath/eduerp_globals.php');
 * Where this configuration cannot be used, ensure that the globals.php file is owned by
 * the web server user (often www-run), and has a chmod of 500.  This will prevent any
 * unauthorized access to the file.
 */
$dbhost='localhost';
$dbuser='root';
$dbpass='';
$dbname='eduerp';
$sitename = 'Eden from the Rock';
?>
