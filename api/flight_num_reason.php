<?php
//TodoList:
//1. Change * to not allow null value for all api
  $server_name = '127.0.0.1';
  $username = 'upintheair_admin';
  $password = 'admin411';
  $db_name = 'upintheair_default';

  $conn = new mysqli($server_name, $username, $password, $db_name);
  if ($conn->connect_error) {
    die('Connection failed: ' . $conn->connect_error);
  }

  $user_id = $_GET['user_id'];

  $sql_reason = "SELECT purpose, COUNT(purpose) AS count FROM flight_record WHERE user_id = '$user_id' AND purpose IS NOT NULL GROUP BY purpose ORDER BY count DESC";

  if (!$conn->query($sql_reason)) {

    $resp = [
      'status' => 'fail',
      'message' => 'Cannot retrieve reason data'
    ];

  } else {

    $result = $conn->query($sql_reason);
    $resp = [
      'status' => 'success',
      'data' => []
    ];
    while ($row = $result->fetch_assoc()) {
      $info = [
        'reason' => $row['purpose'],
        'count' => $row['count']
      ];
      array_push($resp['data'], $info);
    }
  }

  $conn->close();
  header('Content-Type: application/json');
  echo json_encode($resp);
  ?>
