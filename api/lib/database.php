<?php
include_once "config.php";
global $db;

$db = mysql_connect(MYSQL_HOSTNAME, MYSQL_USER, MYSQL_PASSWORD);
?>
