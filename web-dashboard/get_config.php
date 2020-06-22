<?php
//gets the config schedule from db
//just returns all (probably 3 only)

require "db-conn.php";

$query = "select * from config";

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $count);

$stmt->execute();

$result = $stmt->get_result();

if ($result->num_rows > 0) {
  // output data of each row
    $data_arr = array();
  while($row = $result->fetch_assoc()) {
    //decode the schedule first as its stored in encoded form in db
    $row["schedule"] = json_decode($row["schedule"]);
    
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
