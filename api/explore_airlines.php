<?php
  $server_name = '127.0.0.1';
  $username = 'upintheair_admin';
  $password = 'admin411';
  $db_name = 'upintheair_default';

  $conn = new mysqli($server_name, $username, $password, $db_name);
  if ($conn->connect_error) {
    die('Connection failed: ' . $conn->connect_error);
  }
  $airline_iata = $_POST['airline_iata'];
  
  $sql = "SELECT user_id, rate, content FROM airline_comments WHERE airline_id = (SELECT id FROM airline WHERE iata = '$airline_iata')";

  $sql_avg = "SELECT AVG(rate) FROM airline_comments WHERE airline_id = (SELECT id FROM airline WHERE iata = '$airline_iata') GROUP BY airline_id";

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
