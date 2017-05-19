<?php
require_once('connection.php');
require_once('functions.php');
session_unset();
if (!$_SESSION['worldresidents_rgstrd']) {
    exit(header("Location: index.php"));
}