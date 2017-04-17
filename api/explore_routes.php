<?php
  $server_name = '127.0.0.1';
  $username = 'upintheair_admin';
  $password = 'admin411';
  $db_name = 'upintheair_default';

  $conn = new mysqli($server_name, $username, $password, $db_name);
  if ($conn->connect_error) {
    die('Connection failed: ' . $conn->connect_error);
  }
  $dep_airport_iata = $_POST['dep_airport_iata'];
  $arr_airport_iata = $_POST['arr_airport_iata'];

  $sql = "SELECT user_id, rate, content, flight_record.flight_number FROM flight_comments, flight_record WHERE flight_record_id = (SELECT id FROM flight_record WHERE dep_airport_id = (SELECT id FROM airport WHERE iata = '$dep_airport_iata') and arr_airport_id = (SELECT id FROM airport WHERE iata = '$arr_airport_iata'))";

  $result = $conn->query($sql);
  // check if existed
  if ($result -> num_rows == 0) {
    $resp = [
      'status' => 'fail',
      'message' => 'Airline does not exist'
    ];
    header('Content-Type: application/json');
    echo json_encode($resp);
    exit();
  }

  if (!$result) {
    $resp = [
      'status' => 'fail',
      'message' => 'Error: '.$conn->error
    ];
  } else {
    $resp = [
      'status' => 'success',
      'data' => [],
    ];
    while ($row = $result->fetch_assoc()) {
      array_push($resp['data'], $row);
    }
  }

  header('Content-Type: application/json');
  echo json_encode($resp);
  $conn->close();
?>