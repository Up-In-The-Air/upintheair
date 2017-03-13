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
  $user_id = $_POST['user_id'];
  $date = $_POST['date'];
  $source_airport_iata = $_POST['source_airport_iata'];
  $des_airport_iata = $_POST['des_airport_iata'];

  if (!$user_id || !$date || !$source_airport_iata || !$des_airport_iata) {
    $resp = [
      'status' => 'fail',
      'message' => 'Missing required fields'
    ];
    header('Content-Type: application/json');
    echo json_encode($resp);
    exit();
  }

  // Optional
  $flight_number = $_POST['flight_number'];
  $dep_time = $_POST['dep_time'];
  $arr_time = $_POST['arr_time'];
  $class = $_POST['class'];
  $seat = $_POST['seat'];
  $seat_num = $_POST['seat_num'];
  $purpose = $_POST['purpose'];
  $aircraft_iata = $_POST['aircraft_iata'];
  $airline_iata = $_POST['airline_iata'];

  $schema = array('user_id', 'date');
  $values = array($user_id, $date);

  $sql1 = "SELECT id FROM airport WHERE iata = '$source_airport_iata'";
  $row = $conn->query($sql1)->fetch_assoc();
  $source_airport_id = $row['id'];
  array_push($schema, 'source_airport_id');
  array_push($values, $source_airport_id);
  $sql2 = "SELECT id FROM airport WHERE iata = '$des_airport_iata'";
  $row = $conn->query($sql2)->fetch_assoc();
  $des_airport_id = $row['id'];
  array_push($schema, 'des_airport_id');
  array_push($values, $des_airport_id);

  // Check duplicate records
  $sql_check_duplicate = "SELECT * FROM flight_record "
                        ."WHERE user_id = $user_id AND date = '$date' AND "
                        ."source_airport_id = '$source_airport_id' AND "
                        ."des_airport_id = '$des_airport_id'";
  $result = $conn->query($sql_check_duplicate);
  if ($result -> num_rows != 0) {
    $resp = [
      'status' => 'fail',
      'message' => 'Flight already exists'
    ];
    header('Content-Type: application/json');
    echo json_encode($resp);
    exit();
  }

  if ($aircraft_iata) {
    $sql4 = "SELECT id FROM aircraft WHERE iata = '$aircraft_iata'";
    $row = $conn->query($sql4)->fetch_assoc();
    array_push($schema, 'aircraft_id');
    array_push($values, $row['id']);
  }

  if ($airline_iata) {
    $sql3 = "SELECT id FROM airline where iata = '$airline_iata'";
    $row = $conn->query($sql3)->fetch_assoc();
    array_push($schema, 'airline_id');
    array_push($values, $row['id']);
  }

  if ($flight_number) {
    array_push($schema, 'flight_number');
    array_push($values, $flight_number);
  }
  if ($dep_time) {
    array_push($schema, 'dep_time');
    array_push($values, $dep_time);
  }
  if ($arr_time) {
    array_push($schema, 'arr_time');
    array_push($values, $arr_time);
  }
  if ($class) {
    array_push($schema, 'class');
    array_push($values, $class);
  }
  if ($seat) {
    array_push($schema, 'seat');
    array_push($values, $seat);
  }
  if ($seat_num) {
    array_push($schema, 'seat_num');
    array_push($values, $seat_num);
  }
  if ($purpose) {
    array_push($schema, 'dep_time');
    array_push($values, $dep_time);
  }

  $schema_str = join(", ", $schema);
  $values_str = join("', '", $values);

  // add flights
  $sql = "INSERT INTO flight_record (".$schema_str.") VALUES ('".$values_str."')";
  if ($conn->query($sql) == TRUE) {
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
