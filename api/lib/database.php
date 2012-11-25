<?php
global $db;

$db = mysql_connect(MYSQL_HOSTNAME, MYSQL_USERNAME, MYSQL_PASSWORD);
mysql_select_db(MYSQL_DB_NAME, $db);
?>
