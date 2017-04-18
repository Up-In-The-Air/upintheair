<?php
  //connect to db
  $server_name = '127.0.0.1';
  $username = 'upintheair_admin';
  $password = 'admin411';
  $db_name = 'upintheair_default';

  $conn = new mysqli($server_name, $username, $password, $db_name);
  if ($conn->connect_error) {
    die('Connection failed: ' . $conn->connect_error);
  }

  $user_id = $_GET['user_id'];
  $limit = $_GET['limit'];

  if (!$user_id) {
    $resp = [
      'status' => 'fail',
      'message' => 'Missing required fields'
    ];
    header('Content-Type: application/json');
    echo json_encode($resp);
    exit();
  }

  $sql = "SELECT count(*) AS count, DATE_FORMAT(date, '%Y') AS year FROM flight_record WHERE user_id = '$user_id' GROUP BY DATE_FORMAT(date, '%Y') ORDER BY year ASC";

  if ($limit) {
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
      $info = [
        'category' => $row['year'],
        'count' => $row['count']
      ];
      array_push($resp['data'], $info);
    }
  }

  $conn->close();
  header('Content-Type: application/json');
  echo json_encode($resp);
?>
