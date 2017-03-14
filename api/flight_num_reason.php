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

  $userid = $_GET['user_id'];

  $sql_reason = "SELECT purpose, COUNT(purpose) AS count FROM flight_record WHERE user_id = '$userid' GROUP BY purpose ORDER BY count DESC";

  if(conn->query($sql_reason) == FALSE) {

    $resp = [
      'status' => 'fail',
      'table' => 'reason',
      'message' => 'Cannot retrieve reason data'
    ];

  } else {

    $result = $conn->query($sql_reason);
    $resp = [
      'status' => 'success',
      'table' => 'reason',
      'data' => []
    ];
    while ($row = $result->fetch_assoc()) {
      $resp['data'][$row['purpose']] = $row['count'];
    }
  }

  $conn->close();
  header('Content-Type: application/json');
  echo json_encode($resp);
  ?>