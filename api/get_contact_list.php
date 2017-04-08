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

  $sql = "SELECT DISTINCT user.id, user.first_name, user.last_name "
        ."FROM (("
        ."SELECT DISTINCT receiver_id as contact "
        ."FROM messages WHERE sender_id = $user_id"
        .") UNION ALL ("
        ."SELECT DISTINCT sender_id as contact "
        ."FROM messages WHERE receiver_id = $user_id"
        .")) AS contact_ids, user "
        ."WHERE contact_ids.contact = user.id";
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
