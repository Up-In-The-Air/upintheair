<?php
  $server_name = '127.0.0.1';
  $username = 'upintheair_admin';
  $password = 'admin411';
  $db_name = 'upintheair_default';

  $conn = new mysqli($server_name, $username, $password, $db_name);
  if ($conn->connect_error) {
    die('Connection failed: ' . $conn->connect_error);
  }

  $airline_iata = $_GET['airline_iata'];

  if (!$airline_iata) {
    $resp = [
      'status' => 'fail',
      'message' => 'Missing required fields'
    ];
    header('Content-Type: application/json');
    echo json_encode($resp);
    exit();
  }

  // Get comments
  $sql = "SELECT airline_comments.create_timestamp AS create_time, airline_comments.content AS content, airline_comments.rate AS rate, user.first_name AS first_name, user.last_name AS last_name "
        ."FROM airline_comments, user "
        ."WHERE airline_comments.airline_id = (SELECT airline.id FROM airline WHERE iata = '$airline_iata') "
        ."AND user.id = airline_comments.user_id";

  $result = $conn->query($sql);
  // check if existed
  if ($result -> num_rows == 0) {
    $resp = [
      'status' => 'fail',
      'message' => 'Airline does not exist'
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
  $sql_avg = "SELECT AVG(airline_comments.rate) AS average_rate "
            ."FROM airline_comments, user "
            ."WHERE airline_comments.airline_id = (SELECT airline.id FROM airline WHERE iata = '$airline_iata') "
            ."AND user.id = airline_comments.user_id";

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
