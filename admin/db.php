<?php
$host = "sql201.infinityfree.com";
$user = "if0_39101196";
$password = "Zn52UD2BQYNaThD";
$database = "if0_39101196_d_m";

$conn = new mysqli($host, $user, $password, $database);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$conn->set_charset("utf8");
?>
