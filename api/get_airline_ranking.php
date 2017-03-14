<?php
  $server_name = '127.0.0.1';
  $username = 'upintheair_admin';
  $password = 'admin411';
  $db_name = 'upintheair_default';

  $conn = new mysqli($server_name, $username, $password, $db_name);
  if ($conn->connect_error) {
    die('Connection failed: ' . $conn->connect_error);
  }

  $user_id = $_GET['user_id'];
  $limit = $_GET['limit'];

  $sql = "SELECT airline.*, COUNT(airline.name) AS frequency FROM airline, flight_record WHERE flight_record.user_id = '$user_id' AND flight_record.airline_id = airline.id GROUP BY airline.id ORDER BY frequency DESC";

  if ($limit) {
    $sql = $sql." LIMIT $limit";
  }

  $result = $conn->query($sql);

  if (!$result) {
    $resp = [
      'status' => 'fail',
      'message' => 'Error: '.$conn->error
    ];
  } else {
    $resp = [
      'status' => 'success',
      'data' => []
    ];
    while ($row = $result->fetch_assoc()) {
      if ($row['id'] != 0) {
        array_push($resp['data'], $row);
      }
    }
  }

  header('Content-Type: application/json');
  echo json_encode($resp);
  $conn->close();
?>
