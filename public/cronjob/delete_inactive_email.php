<?php
ini_set('display_errors',1);  error_reporting(E_ALL);

$db_server        = '127.0.0.1';
$db_user          = 'root';
$db_password      = 'Mag1clean@888';
$db_name          = 'db_magiclean';
$conn 			  = new mysqli($db_server,$db_user,$db_password,$db_name) or die (mysqli_error($conn));

$s1 = "DELETE FROM tbl_user WHERE active_status = 0";
mysqli_query($conn, $s1) or die (mysqli_error($conn));
?>