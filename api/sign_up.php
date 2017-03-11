<?php
  // TODO: 1. Session should be used to check login status
  // TODO: 2. Database munipulations should be implemented in a separate class.
  $server_name = '127.0.0.1';
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
    $sql = "SELECT * FROM user WHERE email = '".$email."'";

    if ($conn->query($sql)->num_rows !== 0) {
      $resp = [
        'status' => 'fail',
        'message' => $email.' has already been registered.'
      ];
    } else {
      // Session for Login Status
      $session_key = md5($email);

      $value_str = join("', '", [$first_name, $last_name, $email, $password, $session_key]);
      $sql = "INSERT INTO user (first_name, last_name, email, password, session_key, session_expire) VALUES ('".$value_str."', DATE_ADD(CURRENT_TIMESTAMP(), INTERVAL 30 DAY))";

      if ($conn->query($sql) === TRUE) {
        $sql = "SELECT * FROM user WHERE email = '".$email."'";
        $result = $conn->query($sql);
        $row = $result->fetch_assoc();
        $resp = [
          'status' => 'success',
          'data' => [
            'first_name' => $first_name,
            'last_name' => $last_name,
            'email' => $email,
            'id' => $row['id'],
            'cookie' => [
              'id' => $row['id'],
              'key' => $session_key,
              'expires' => $row['session_expire']
            ]
          ]
        ];
      } else {
        $resp = [
          'status' => 'fail',
          'message' => 'Error: '. $conn->error
        ];
      }
    }
  }
  $conn->close();
  header('Content-Type: application/json');
  echo json_encode($resp);
?>
