<?php
//update schedule
//usage: POST update_config.php
//params: id, config

if (isset($_POST["id"]) && isset($_POST["config"])){
  $config_id = htmlspecialchars($_POST["id"]);
  $config = json_decode($_POST["config"], true); //true to conv to assoc array

  //db connect
  require "db-conn.php";

  //update query
  $query = "update config set schedule = ?, duration = ? where id = ?";

  $stmt = $conn->prepare($query);
  $stmt->bind_param("sii", json_encode($config["schedule"]), $config["duration"], $config_id);

  //run it
  $stmt->execute();

  $conn->close();
}
?> 

