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

  $sql = "SELECT aircraft.name, COUNT(aircraft.name) AS count FROM aircraft, flight_record WHERE flight_record.user_id = '$user_id' AND flight_record.aircraft_id = aircraft.id GROUP BY aircraft.name ORDER BY count DESC";

  if ($limit) {
    $sql = $sql."LIMIT $limit";
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
      array_push($resp['data'], $row);
    }
  }

  header('Content-Type: application/json');
  echo json_encode($resp);
  $conn->close();
?>