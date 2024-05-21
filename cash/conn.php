<?php
$s_name = "localhost"; 
$r_name = "root"; 
$pass = "";
$db_name = "sales_inventory"; 


$conn = mysqli_connect($s_name, $r_name, $pass, $db_name);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

?>
