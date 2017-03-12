<?php
  $server_name = '127.0.0.1';
  $username = 'upintheair_admin';
  $password = 'admin411';
  $db_name = 'upintheair_default';

  $conn = new mysqli($server_name, $username, $password, $db_name);
  if ($conn->connect_error) {
    die('Connection failed: ' . $conn->connect_error);
  }

  $id = $_POST['id'];

  $sql = "DELETE FROM flight_record WHERE id = $id ";

  if ($conn->query($sql) === TRUE) {
      $resp = [
        'status' => 'success',
        'message' => 'delete successfully'
      ];
  } else {
      $resp = [
        'status' => 'fail',
        'message' => 'delete fail(error)'
      ];
  }
  header('Content-Type: application/json');
  echo json_encode($resp);
  $conn->close();
?>
