<?php
  $server_name = '127.0.0.1';
  $username = 'upintheair_admin';
  $password = 'admin411';
  $db_name = 'upintheair_default';

  $conn = new mysqli($server_name, $username, $password, $db_name);
  if ($conn->connect_error) {
    die('Connection failed: ' . $conn->connect_error);
  }

  $flight_number = $_POST['flight_number'];
  $date = date ("Y-m-d", $_POST['date'];
  $dep_time = date ("Y-m-d H:i:s", $_POST['dep_time'];
  $arr_time = date ("Y-m-d H:i:s", $_POST['arr_time'];
  $class = $_POST['class'];
  $seat = $_POST['seat'];
  $purpose = $_POST['purpose'];
  $note = $_POST['note']
  $photourl = $_POST['photourl'];
  $stops = $_POST['userid'];
  $userid = $_POST['userid'];

  $aircraft = $_POST['aircraft'];
  $source_airport = $_POST['source_airport']
  $des_airport = $_POST['des_airport']
  $airline = $_POST['airline']
// source airport
  $sql1 = "SELECT id FROM Airport where name = '$source_airport'";
  $row = $conn->query($sql1)->fetch_assoc();
  $source_airport_id = $row['id'];
// des airport
  $sql2 = "SELECT id FROM Airport where name = '$des_airport'";
  $row = $conn->query($sql2)->fetch_assoc();
  $des_airport_id = $row['id'];
//airline
  $sql3 = "SELECT id FROM Airline where name = '$airline'";
  $row = $conn->query($sql3)->fetch_assoc();
  $airline_id= $row['id'];
//aircraft
  $sql4 = "SELECT id FROM Aircraft where name = '$aircraft'";
  $row = $conn->query($sql4)->fetch_assoc();
  $aircraft_id= $row['id'];
  // check for duplicate adds
  $sql_test = "SELECT * FROM flight_records where userid = $userid and flight_number = '$flight_number' and date = '$date'";

  result = $conn->query($sql_test);

  if (result -> row != 0){
    echo "flight already existed"
    exit();
  }
  // add flights

  $sql = "INSERT INTO flight_records (flight_number, date, dep_time, arr_time, class, seat, purpose, note, photourl, source_airport_id, des_airport_id, stops, userid, aircraft_id, airline_id) VALUES ('$flight_number', '$date', '$dep_time', '$arr_time', '$class', '$seat', '$purpose', '$note', '$photourl', '$source_airport_id', '$des_airport_id', '$stops', '$userid', '$aircraft_id', '$airline_id')";

  if ($conn->query($sql) === TRUE) {
      echo "New flight record created successfully";
  } else {
      echo "Error: " . $sql . "<br>" . $conn->error;
      exit();
  }

  $conn->close();
?>
