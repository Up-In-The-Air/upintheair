<?php
  $server_name = '127.0.0.1';
  $username = 'upintheair_admin';
  $password = 'admin411';
  $db_name = 'upintheair_default';

  $conn = new mysqli($server_name, $username, $password, $db_name);
  if ($conn->connect_error) {
    die('Connection failed: ' . $conn->connect_error);
  }

  $email = $_POST['email'];

  $sql = "UPDATE user SET session_key=NULL, session_expire=NULL  WHERE email='".$email."'";

  if ($conn->query($sql) === TRUE) {
    $resp = [ 'status' => 'success' ];
  } else {
    $resp = [
      'status' => 'fail',
      'message' => 'Fail to log out!'
    ];
  }

  $conn->close();
  header('Content-Type: application/json');
  echo json_encode($resp);
?>
