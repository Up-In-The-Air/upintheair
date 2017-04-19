<?php
  $server_name = '127.0.0.1';
  $username = 'upintheair_admin';
  $password = 'admin411';
  $db_name = 'upintheair_default';

  $conn = new mysqli($server_name, $username, $password, $db_name);
  if ($conn->connect_error) {
    die('Connection failed: ' . $conn->connect_error);
  }

  $post_email = $_POST['email'];
  $post_password = $_POST['password'];
  // $autologin = $_POST['auto_login'];
  $autologin = true;

  $sql = "SELECT * FROM user WHERE email = '".$post_email."'";

  $result = $conn->query($sql);

  if ($result->num_rows == 1) {
    $row = $result->fetch_assoc();
    $password = $row['password'];
    if ($post_password === $password) {
      $resp = [
        'status' => 'success',
        'data' => [
          'first_name' => $row['first_name'],
          'last_name' => $row['last_name'],
          'email' => $row['email'],
          'id' => $row['id'],
        ]
      ];

      // Session for auto login
      if ($autologin) {
        $session_key = md5($row['email']);
        $expire_time = time() + 3600 * 24 * 30;

        // Create and insert session for this user
        $sql = "UPDATE user SET session_key='".$session_key."', session_expire=DATE_ADD(CURRENT_TIMESTAMP(), INTERVAL 30 DAY) WHERE id=".$row['id'];
        if ($conn->query($sql) === TRUE) {
          $resp['data']['cookie'] = [
            'id' => $row['id'],
            'key' => $session_key,
            'expires' => $expire_time
          ];
        }
      }
    } else {
      $resp = [
        'status' => 'fail',
        'message' => 'Incorrect email/password.'
      ];
    }
  } else {
    $resp = [
      'status' => 'fail',
      'message' => 'Cannot find '.$post_email
    ];
  }

  $conn->close();
  header('Content-Type: application/json');
  echo json_encode($resp);
?>
