<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require "db-conn.php";

// queries
$query_latest = "select * from sensor_data order by id desc limit 1";
$query_latest1k = "select * from sensor_data order by id desc limit 1000";


$result = $conn->query($query_latest1k);

//$stmt->bind_param("sss", $firstname, $lastname, $email);

if ($result->num_rows > 0) {
  // output data of each row
    $sensor_1k = array();
  while($row = $result->fetch_assoc()) {
    $sensor_1k[] = $row;
  }
  
  header('Content-Type: application/json');

  echo json_encode($sensor_1k);

} else {
  echo "";
}

$conn->close();
?> 
