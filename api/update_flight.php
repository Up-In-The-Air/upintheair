<?php
  $server_name = '127.0.0.1';
  $username = 'upintheair_admin';
  $password = 'admin411';
  $db_name = 'upintheair_default';

  $conn = new mysqli($server_name, $username, $password, $db_name);
  if ($conn->connect_error) {
    die('Connection failed: ' . $conn->connect_error);
  }

  $id = $_POST['id'];

  $flight_number = $_POST['flight_number'];
  $date = $_POST['date'];
  $dep_time = $_POST['dep_time'];
  $arr_time = $_POST['arr_time'];
  $class = $_POST['class'];
  $seat = $_POST['seat'];
  $purpose = $_POST['purpose'];
  $note = $_POST['note'];
  $photourl = $_POST['photourl'];
  $userid = $_POST['userid'];

  $aircraft = $_POST['aircraft'];
  $source_airport = $_POST['source_airport'];
  $des_airport = $_POST['des_airport'];
  $airline = $_POST['airline'];

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

  // check for no flight

  $sql_test = "SELECT * FROM flight_record where id = $id";

  $result = $conn->query($sql_test);

  if ($result -> num_rows == 0){
      $resp = [
        'status' => 'fail',
        'message' => 'flight not exist'
      ];
      header('Content-Type: application/json');
      echo json_encode($resp);
      exit();
  }

  if($dep_time == null) $dep_time = 'no';
  if($arr_time == null) $arr_time = 'no';
  if($class == null) $class = 'no';
  if($seat == null) $seat = 'no';
  if($purpose == null) $purpose = 'no';
  if($note == null) $note = 'no';
  if($photourl == null) $photourl = 'no';
  if($source_airport_id == null) $source_airport_id = 0;
  if($des_airport_id == null) $des_airport_id = 0;
  if($aircraft_id == null) $aircraft_id = 0;
  if($airline_id == null) $airline_id = 0;

  $sql = "UPDATE flight_record SET flight_number = '$flight_number', date = '$date', dep_time = '$dep_time', arr_time = '$arr_time', class = '$class', seat = '$seat', purpose = '$purpose', note = '$note', photourl = '$photourl', source_airport_id = $source_airport_id, des_airport_id = $des_airport_id, userid = $userid, aircraft_id = $aircraft_id, airline_id = $airline_id WHERE id = $id";

  if ($conn->query($sql) === TRUE) {
      $resp = [
        'status' => 'success',
        'message' => 'record updated successfully'
      ];
  } else {
      $resp = [
        'status' => 'fail',
        'message' => 'update fail(error)'
      ];
  }
  header('Content-Type: application/json');
  echo json_encode($resp);
  $conn->close();
?>
