<?php
  $server_name = '127.0.0.1';
  $username = 'upintheair_admin';
  $password = 'admin411';
  $db_name = 'upintheair_default';

  $conn = new mysqli($server_name, $username, $password, $db_name);
  if ($conn->connect_error) {
    die('Connection failed: ' . $conn->connect_error);
  }

  // Required
  $id = $_POST['flight_id'];

  if (!$id) {
    $resp = [
      'status' => 'fail',
      'message' => 'Missing required fields: flight_id'
    ];
    header('Content-Type: application/json');
    echo json_encode($resp);
    exit();
  }

  $sql_check_exist = "SELECT * FROM flight_record WHERE id='$id'";
  if ($conn->query($sql_check_exist)->num_rows === 0) {
    $resp = [
      'status' => 'fail',
      'message' => 'Can not find flight_id = '.$id
    ];
    header('Content-Type: application/json');
    echo json_encode($resp);
    exit();
  }

  $date = $_POST['date'];
  $dep_airport_iata = $_POST['dep_airport_iata'];
  $arr_airport_iata = $_POST['arr_airport_iata'];
  $flight_number = $_POST['flight_number'];
  $dep_time = $_POST['dep_time'];
  $arr_time = $_POST['arr_time'];
  $class = $_POST['class'];
  $seat = $_POST['seat'];
  $purpose = $_POST['purpose'];
  $aircraft_iata = $_POST['aircraft_iata'];
  $airline_iata = $_POST['airline_iata'];

  $schema_values = array();

  if ($dep_airport_iata) {
    $sql1 = "SELECT id FROM airport WHERE iata = '$dep_airport_iata'";
    $row = $conn->query($sql1)->fetch_assoc();
    $dep_airport_id = $row['id'];
    array_push($schema_values, "dep_airport_id='".$dep_airport_id."'");
  }

  if ($arr_airport_iata) {
    $sql2 = "SELECT id FROM airport WHERE iata = '$arr_airport_iata'";
    $row = $conn->query($sql2)->fetch_assoc();
    $arr_airport_id = $row['id'];
    array_push($schema_values, "arr_airport_id='".$arr_airport_id."'");
  }

  if ($aircraft_iata) {
    $sql4 = "SELECT id FROM aircraft WHERE iata = '$aircraft_iata'";
    $row = $conn->query($sql4)->fetch_assoc();
    array_push($schema_values, "aircraft_id='".$row['id']."'");
  }

  if ($airline_iata) {
    $sql3 = "SELECT id FROM airline where iata = '$airline_iata'";
    $row = $conn->query($sql3)->fetch_assoc();
    array_push($schema_values, "airline_id='".$row['id']."'");
  }

  if ($date) { array_push($schema_values, "date='".$date."'"); }
  if ($flight_number) { array_push($schema_values, "flight_number='".$flight_number."'"); }
  if ($dep_time) { array_push($schema_values, "dep_time='".$dep_time."'"); }
  if ($arr_time) { array_push($schema_values, "arr_time='".$arr_time."'"); }
  if ($class) { array_push($schema_values, "class='".$class."'"); }
  if ($seat) { array_push($schema_values, "seat='".$seat."'"); }
  if ($seat_num) { array_push($schema, "seat_num='".$seat_num."'"); }
  if ($purpose) { array_push($schema_values, "purpose='".$purpose."'"); }

  $schema_values_str = join(", ", $schema_values);

  // update flights
  $sql = "UPDATE flight_record SET ". $schema_values_str ." WHERE id='$id'";

  if ($conn->query($sql)) {
    $resp = [ 'status' => 'success' ];
  } else {
    $resp = [
      'status' => 'fail',
      'message' => 'Error: '.$conn->error
    ];
  }
  header('Content-Type: application/json');
  echo json_encode($resp);
  $conn->close();
?>
