<?php
//gets you the latest x number of data from the database
//usage: GET fetch_sensor.php?count=n

if (isset($_GET["count"])){
  $count = htmlspecialchars($_GET["count"]);
}
//default count is 1hr's worth of data
//10 sec per data so 360 data is 1hr
else $count = 360;

require "db-conn.php";

$query = "select * from sensor_data order by id desc limit ?";

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $count);

$stmt->execute();

$result = $stmt->get_result();

if ($result->num_rows > 0) {
  // output data of each row
    $data_arr = array();
  while($row = $result->fetch_assoc()) {
    $data_arr[] = $row;
  }
  
  //set header as json data
  header('Content-Type: application/json');
  echo json_encode($data_arr);

} else {
  echo "[]"; //return nothing if no data (impossible but whatever)
}

$conn->close();
?> 
