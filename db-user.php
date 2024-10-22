<?php
define('DSN', 'mysql:host=localhost;dbname=admin');
define('DBUSER', 'root');
define('DBPASS', '');

// 1. Connect to DB
$dbu = new PDO(DSN, DBUSER, DBPASS);
?>