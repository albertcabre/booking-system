<?php
session_start();

require_once('functions.php');

$hostname='';
$username='';
$password='';
$dbname='';

$conexion = mysql_connect($hostname, $username, $password);
if (!$conexion) {
	die(mysql_error());
}

$handle_db=mysql_select_db($dbname, $conexion);
if (!$handle_db) {
	die (mysql_error());
}
