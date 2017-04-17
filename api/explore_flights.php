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
  
  $sql = "SELECT user_id, rate, content FROM flight_comments WHERE flight_record_id = (SELECT id FROM flight_record WHERE flight_number = '$flight_number')";

  $sql_avg = "SELECT AVG(rate) FROM flight_comments WHERE airline_id = (SELECT id FROM flight_record WHERE flight_number = '$flight_number')";

  $result_avg = $conn->query($sql_avg);
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
      'avg' => $result_avg -> fetch_assoc()
    ];
    while ($row = $result->fetch_assoc()) {
      array_push($resp['data'], $row);
    }
  }
  header('Content-Type: application/json');
  echo json_encode($resp);
  $conn->close();
?>