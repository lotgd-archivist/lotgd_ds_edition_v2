<?php
$DB_USER="ds_lotgd"; //Database Username
$DB_PASS="ds_lotgd"; //Database Password
$DB_HOST="localhost"; //Database Hostname
$DB_NAME="dragonslayer_lotgd"; //Database Databasename

if ($DB_USER.$DB_PASS.$DB_HOST.$DB_NAME == ""){
	echo "You must edit the dbconnect.php file to set it up for your database.";
}
?>
