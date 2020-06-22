<?php

$servername = "RDS_ENDPOINT";
$username = "DB_USER";
$password = "DB_PASS";
$dbname = "tenki";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

?>
