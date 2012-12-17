<?php
global $db;

$db = mysql_connect(MYSQL_HOSTNAME, MYSQL_USERNAME, MYSQL_PASSWORD) or die(mysql_error());
mysql_select_db(MYSQL_DB_NAME, $db) or die(mysql_error());
?>
