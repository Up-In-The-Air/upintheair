<?php
  $server_name = 'localhost';
  $username = 'upintheair_admin';
  $password = 'admin411';
  $db_name = 'upintheair_default';

  $conn = new mysqli($server_name, $username, $password, $db_name);
  if ($conn->connect_error) {
    die('Connection failed: ' . $conn->connect_error);
  }

  $id = $_POST['id'];
  $session_key = $_POST['key'];

  $sql = "SELECT * FROM user WHERE id = ".$id;

  $result = $conn->query($sql);

  if ($result->num_rows == 1) {
    $row = $result->fetch_assoc();
    if ($session_key = $row['session_key']) {
      $resp = [
        'status' => 'success',
        'data' => [
          'first_name' => $row['first_name'],
          'last_name' => $row['last_name'],
          'email' => $row['email']
        ]
      ];
    } else {
      $resp = [
        'status' => 'fail',
        'message' => ''
      ];
    }
  } else {
    $resp = [
      'status' => 'fail',
      'message' => ''
    ];
  }

  $conn->close();
  header('Content-Type: application/json');
  echo json_encode($resp);
?>
