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
  $receiver_id = $_GET['receiver_id'];
  $limit = $_GET['limit'];

  if (!$user_id || !$receiver_id) {
    $resp = [
      'status' => 'fail',
      'message' => 'Missing required fields'
    ];
    header('Content-Type: application/json');
    echo json_encode($resp);
    exit();
  }

  $sql = "SELECT DISTINCT sender_id, receiver_id, message, create_timestamp "
        ."FROM messages "
        ."WHERE (sender_id = $user_id AND receiver_id = $receiver_id) "
        ."OR (sender_id = $receiver_id AND receiver_id = $user_id) "
        ."ORDER BY create_timestamp DESC";

  if (!is_null($limit)) {
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
      array_push($resp['data'], $row);
    }
  }

  header('Content-Type: application/json');
  echo json_encode($resp);
  $conn->close();
?>
