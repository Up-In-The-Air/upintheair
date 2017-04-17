<?php
  $server_name = '127.0.0.1';
  $username = 'upintheair_admin';
  $password = 'admin411';
  $db_name = 'upintheair_default';

  $conn = new mysqli($server_name, $username, $password, $db_name);
  if ($conn->connect_error) {
    die('Connection failed: ' . $conn->connect_error);
  }

  $airport_iata = $_GET['airport_iata'];

  if (!$airport_iata) {
    $resp = [
      'status' => 'fail',
      'message' => 'Missing required fields'
    ];
    header('Content-Type: application/json');
    echo json_encode($resp);
    exit();
  }

  // Get comments
  $sql = "SELECT airport_comments.create_timestamp AS create_time, airport_comments.content AS content, airport_comments.rate AS rate, user.first_name AS first_name, user.last_name AS last_name "
        ."FROM airport_comments, user "
        ."WHERE airport_comments.airport_id = (SELECT airport.id FROM airport WHERE iata = '$airport_iata') "
        ."AND user.id = airport_comments.user_id";

  $result = $conn->query($sql);
  // check if existed
  if ($result -> num_rows == 0) {
    $resp = [
      'status' => 'fail',
      'message' => 'Airport does not exist'
    ];
    header('Content-Type: application/json');
    echo json_encode($resp);
    exit();
  }

  if (!$result) {
    $resp = [
      'status' => 'fail',
      'message' => 'Error: '.$conn->error
    ];
  } else {
    $resp = [
      'status' => 'success',
      'data' => [
        'comments' => []
      ]
    ];
    while ($row = $result->fetch_assoc()) {
      array_push($resp['data']['comments'], $row);
    }
  }

  // Get average rate
  $sql_avg = "SELECT AVG(airport_comments.rate) AS average_rate "
            ."FROM airport_comments, user "
            ."WHERE airport_comments.airport_id = (SELECT airport.id FROM airport WHERE iata = '$airport_iata') "
            ."AND user.id = airport_comments.user_id";

  $result_avg = $conn->query($sql_avg);

  if (!$result_avg) {
    $resp = [
      'status' => 'fail',
      'message' => 'Error: '.$conn->error
    ];
  } else {
    $row = $result_avg->fetch_assoc();
    $resp['data']['average_rate'] = $row['average_rate'];
  }

  header('Content-Type: application/json');
  echo json_encode($resp);
  $conn->close();
?>
