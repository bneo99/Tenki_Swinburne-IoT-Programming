<?php
$servername = "tenki-db.cluster-cw42wmt5nblr.us-east-1.rds.amazonaws.com";
$username = "admin";
$password = "tenkigenki";
$dbname = "tenki";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

?>
