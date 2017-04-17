<?php
  $server_name = '127.0.0.1';
  $username = 'upintheair_admin';
  $password = 'admin411';
  $db_name = 'upintheair_default';

  $conn = new mysqli($server_name, $username, $password, $db_name);
  if ($conn->connect_error) {
    die('Connection failed: ' . $conn->connect_error);
  }
  $dep_airport_iata = $_GET['dep_airport_iata'];
  $arr_airport_iata = $_GET['arr_airport_iata'];

  if (!$dep_airport_iata || !$arr_airport_iata) {
    $resp = [
      'status' => 'fail',
      'message' => 'Missing required fields'
    ];
    header('Content-Type: application/json');
    echo json_encode($resp);
    exit();
  }

  // Get comments
  $sql = "SELECT flight_comments.create_timestamp AS create_time, "
        ."flight_comments.content AS content, flight_comments.rate AS rate, "
        ."user.first_name AS first_name, user.last_name AS last_name "
        ."FROM flight_comments, flight_record, user "
        ."WHERE flight_record.dep_airport_id = (SELECT airport.id FROM airport WHERE iata = '$dep_airport_iata') "
        ."AND flight_record.arr_airport_id = (SELECT airport.id FROM airport WHERE iata = '$arr_airport_iata') "
        ."AND user.id = flight_record.user_id "
        ."AND flight_record.id = flight_comments.flight_record_id";

  $result = $conn->query($sql);

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
  $sql_avg = "SELECT AVG(flight_comments.rate) AS average_rate "
        ."FROM flight_comments, flight_record, user "
        ."WHERE flight_record.dep_airport_id = (SELECT airport.id FROM airport WHERE iata = '$dep_airport_iata') "
        ."AND flight_record.arr_airport_id = (SELECT airport.id FROM airport WHERE iata = '$arr_airport_iata') "
        ."AND user.id = flight_record.user_id "
        ."AND flight_record.id = flight_comments.flight_record_id";

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
