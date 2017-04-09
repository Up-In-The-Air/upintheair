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
  $dep_airport_iata = $_POST['dep_airport_iata'];
  $arr_airport_iata = $_POST['arr_airport_iata'];

  if (!$user_id || !$date || !$dep_airport_iata || !$arr_airport_iata) {
    $resp = [
      'status' => 'fail',
      'message' => 'Missing required fields'
    ];
    header('Content-Type: application/json');
    echo json_encode($resp);
    exit();
  }

  // Optional
  $distance = $_POST['distance'];
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

  $sql1 = "SELECT id FROM airport WHERE iata = '$dep_airport_iata'";
  $row = $conn->query($sql1)->fetch_assoc();
  $dep_airport_id = $row['id'];
  array_push($schema, 'dep_airport_id');
  array_push($values, $dep_airport_id);
  $sql2 = "SELECT id FROM airport WHERE iata = '$arr_airport_iata'";
  $row = $conn->query($sql2)->fetch_assoc();
  $arr_airport_id = $row['id'];
  array_push($schema, 'arr_airport_id');
  array_push($values, $arr_airport_id);

  // Check duplicate records
  $sql_check_duplicate = "SELECT * FROM flight_record "
                        ."WHERE user_id = $user_id AND date = '$date' AND "
                        ."dep_airport_id = '$dep_airport_id' AND "
                        ."arr_airport_id = '$arr_airport_id'";
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

  array_push($schema, 'aircraft_id');
  if ($aircraft_iata != null) {
    $sql4 = "SELECT id FROM aircraft WHERE iata = '$aircraft_iata'";
    $row = $conn->query($sql4)->fetch_assoc();
    array_push($values, $row['id']);
  } else {
    array_push($values, 0);
  }

  array_push($schema, 'airline_id');
  if ($airline_iata != null) {
    $sql3 = "SELECT id FROM airline where iata = '$airline_iata'";
    $row = $conn->query($sql3)->fetch_assoc();
    $airline_id = $row['id'];
    array_push($values, $airline_id);
  } else {
    array_push($values, 0);
  }

  if ($distance != null) {
    array_push($schema, 'distance');
    array_push($values, $distance);
  }
  if ($flight_number != null) {
    array_push($schema, 'flight_number');
    array_push($values, $flight_number);
  }
  if ($dep_time != null) {
    array_push($schema, 'dep_time');
    array_push($values, $dep_time);
  }
  if ($arr_time != null) {
    array_push($schema, 'arr_time');
    array_push($values, $arr_time);
  }
  if ($class != null) {
    array_push($schema, 'class');
    array_push($values, $class);
  }
  if ($seat != null) {
    array_push($schema, 'seat');
    array_push($values, $seat);
  }
  if ($seat_num != null) {
    array_push($schema, 'seat_num');
    array_push($values, $seat_num);
  }
  if ($purpose != null) {
    array_push($schema, 'purpose');
    array_push($values, $purpose);
  }

  $schema_str = join(", ", $schema);
  $values_str = join("', '", $values);

  /* add flights */
  $sql = "INSERT INTO flight_record (".$schema_str.") VALUES ('".$values_str."')";

  if ($conn->query($sql)) {
    $last_id = $conn->insert_id;
  } else {
    $resp = [
      'status' => 'fail',
      'message' => 'Error: '.$conn->error
    ];
    header('Content-Type: application/json');
    echo json_encode($resp);
    $conn->close();
    die('Error: '.$conn->error);
  }

  /* add flight comments */
  $flight_record_comment = $_POST['flight_record_comment'];
  $flight_record_rate = $_POST['flight_record_rate'];

  $schema = array('user_id', 'flight_record_id');
  $values = array($user_id, $last_id);

  if (!is_null($flight_record_comment)) {
    array_push($schema, 'content');
    array_push($values, $flight_record_comment);
  }
  if (!is_null($flight_record_rate)) {
    array_push($schema, 'rate');
    array_push($values, $flight_record_rate);
  }
  if (!is_null($flight_record_comment) || !is_null($flight_record_rate)) {
    $schema_str = join(", ", $schema);
    $values_str = join("', '", $values);
    $sql = "INSERT INTO flight_comments (".$schema_str.") VALUES ('".$values_str."')";

    if (!$conn->query($sql)) {
      $resp = [
        'status' => 'fail',
        'message' => 'Error: '.$conn->error
      ];
      header('Content-Type: application/json');
      echo json_encode($resp);
      $conn->close();
      die('Error: '.$conn->error);
    }
  }

  /* add departure airport comments */
  $dep_airport_comment = $_POST['dep_airport_comment'];
  $dep_airport_rate = $_POST['dep_airport_rate'];

  $schema = array('user_id', 'flight_record_id');
  $values = array($user_id, $dep_airport_id);

  if (!is_null($dep_airport_comment)) {
    array_push($schema, 'content');
    array_push($values, $dep_airport_comment);
  }
  if (!is_null($dep_airport_rate)) {
    array_push($schema, 'rate');
    array_push($values, $dep_airport_rate);
  }
  if (!is_null($dep_airport_comment) || !is_null($dep_airport_rate)) {
    $schema_str = join(", ", $schema);
    $values_str = join("', '", $values);
    $sql = "INSERT INTO airport_comments (".$schema_str.") VALUES ('".$values_str."')";

    if (!$conn->query($sql)) {
      $resp = [
        'status' => 'fail',
        'message' => 'Error: '.$conn->error
      ];
      header('Content-Type: application/json');
      echo json_encode($resp);
      $conn->close();
      die('Error: '.$conn->error);
    }
  }

  /* add arrival airport comments */
  $arr_airport_comment = $_POST['arr_airport_comment'];
  $arr_airport_rate = $_POST['arr_airport_rate'];

  $schema = array('user_id', 'flight_record_id');
  $values = array($user_id, $arr_airport_id);

  if (!is_null($arr_airport_comment)) {
    array_push($schema, 'content');
    array_push($values, $arr_airport_comment);
  }
  if (!is_null($arr_airport_rate)) {
    array_push($schema, 'rate');
    array_push($values, $arr_airport_rate);
  }
  if (!is_null($arr_airport_comment) || !is_null($arr_airport_rate)) {
    $schema_str = join(", ", $schema);
    $values_str = join("', '", $values);
    $sql = "INSERT INTO airport_comments (".$schema_str.") VALUES ('".$values_str."')";

    if (!$conn->query($sql)) {
      $resp = [
        'status' => 'fail',
        'message' => 'Error: '.$conn->error
      ];
      header('Content-Type: application/json');
      echo json_encode($resp);
      $conn->close();
      die('Error: '.$conn->error);
    }
  }

  /* add airline comments */
  $airline_comment = $_POST['airline_comment'];
  $airline_rate = $_POST['airline_rate'];

  $schema = array('user_id', 'flight_record_id');
  $values = array($user_id, $airline_id);

  if (!is_null($airline_comment)) {
    array_push($schema, 'content');
    array_push($values, $airline_comment);
  }
  if (!is_null($airline_rate)) {
    array_push($schema, 'rate');
    array_push($values, $airline_rate);
  }
  if (!is_null($airline_comment) || !is_null($airline_rate)) {
    $schema_str = join(", ", $schema);
    $values_str = join("', '", $values);
    $sql = "INSERT INTO airline_comments (".$schema_str.") VALUES ('".$values_str."')";

    if (!$conn->query($sql)) {
      $resp = [
        'status' => 'fail',
        'message' => 'Error: '.$conn->error
      ];
      header('Content-Type: application/json');
      echo json_encode($resp);
      $conn->close();
      die('Error: '.$conn->error);
    }
  }

  $resp = [ 'status' => 'success' ];

  header('Content-Type: application/json');
  echo json_encode($resp);
  $conn->close();
?>
