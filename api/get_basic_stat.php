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

  $sql = "SELECT COUNT(*) AS count, SUM(distance) AS distance FROM flight_record WHERE user_id = '$user_id'";
  $result = $conn->query($sql);

  if (!$result) {
    $resp = [
      'status' => 'fail',
      'message' => 'Error: '.$conn->error
    ];
  } else {
    $row = $result->fetch_assoc();
    $total_flight = $row['count'];
    $total_distance = $row['distance'];
    $resp = [
      'status' => 'success',
      'data' => [
        'total_flight' => $total_flight,
        'total_distance' => $total_distance
      ]
    ];
  }

  header('Content-Type: application/json');
  echo json_encode($resp);
  $conn->close();
?>
