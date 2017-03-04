<?php
  $server_name = 'localhost';
  $username = 'upintheair_admin';
  $password = 'admin411';
  $db_name = 'upintheair_default';

  $conn = new mysqli($server_name, $username, $password, $db_name);
  if ($conn->connect_error) {
    die('Connection failed: ' . $conn->connect_error);
  }

  $first_name = $_POST['first_name'];
  $last_name = $_POST['last_name'];
  $email = $_POST['email'];
  $password = $_POST['password'];
  $confirmed_password = $_POST['confirmed_password'];

  if ($password !== $confirmed_password) {
    $resp = [
      'status' => 'fail',
      'message' => 'Passwords do not match!'
    ];
  } else {
    $value_str = join("', '", [$first_name, $last_name, $email, $password]);
    $sql = "INSERT INTO user (first_name, last_name, email, password) VALUES ('" . $value_str . "')";
    echo $sql;
    if ($conn->query($sql) === TRUE) {
      $resp = [
        'status' => 'success',
        'data' => [
          'first_name' => $first_name,
          'last_name' => $last_name,
          'email' => $email
        ]
      ];
    } else {
      $resp = [
        'status' => 'fail',
        'message' => 'Error: '. $conn->error
      ];
    }
  }
  $conn->close();
  header('Content-Type: application/json');
  echo json_encode($resp);
?>
