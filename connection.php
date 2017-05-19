<?php
session_start();

require_once('functions.php');

$hostname='';
$username='';
$password='';
$dbname='';

$link = mysqli_connect($hostname, $username, $password, $dbname);
if (!$link) {
	die(mysqli_error());
}
