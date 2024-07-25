<?php

$db_server        = '127.0.0.1';
$db_user          = 'root';
$db_password      = '';
$db_name          = '';

$conn 			  = new mysqli($db_server,$db_user,$db_password,$db_name) or die (mysqli_error($conn));
$password = hash("sha256", "Magiclean@888");
$s = "INSERT INTO tbl_user_internal SET username = 'admin', password = '".$password."', created_at = now()";
echo $s;
mysqli_query($conn, $s);