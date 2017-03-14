<?php
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

  $sql = "SELECT * "
        ."FROM ( "
        ."SELECT SUM(count) AS frequency, airport_id "
        ."FROM ( "
        ."SELECT COUNT(dep_airport_id) AS count, dep_airport_id AS airport_id FROM flight_record WHERE user_id = '$user_id' GROUP BY dep_airport_id "
        ."UNION ALL "
        ."SELECT COUNT(arr_airport_id) AS count, arr_airport_id AS airport_id FROM flight_record WHERE user_id = '$user_id' GROUP BY arr_airport_id "
        .") AS airport_count "
        ."GROUP BY airport_id "
        .") AS airport_count_sum, airport "
        ."WHERE airport.id = airport_count_sum.airport_id "
        ."ORDER BY frequency DESC ";

  if ($limit) {
    $sql = $sql."LIMIT $limit";
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
      array_push($resp['data'], $row);
    }
  }

  header('Content-Type: application/json');
  echo json_encode($resp);
  $conn->close();
?>
