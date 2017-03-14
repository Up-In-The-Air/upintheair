<?php
  $server_name = '127.0.0.1';
  $username = 'upintheair_admin';
  $password = 'admin411';
  $db_name = 'upintheair_default';

  $conn = new mysqli($server_name, $username, $password, $db_name);
  if ($conn->connect_error) {
    die('Connection failed: ' . $conn->connect_error);
  }

  $userid = $_GET['user_id'];

  $sql_seat = "SELECT seat, COUNT(seat) AS count FROM flight_record WHERE user_id = '$userid' GROUP BY seat ORDER BY count DESC";

  if ($conn->query($sql_seat) == FALSE) {

  	$resp = [
  	  'status' => 'fail',
  	  'message' => 'Cannot retrieve seat data'
    ];

  } else {

  	$result = $conn->query($sql_seat);
  	$resp = [
  	  'status' => 'success',
  	  'data' => []
  	];
    while ($row = $result->fetch_assoc()) {
      $info = [
        'seat' => $row['seat'],
        'count' => $row['count']
      ];
      array_push($resp, $info);
    }
  }

  $conn->close();
  header('Content-Type: application/json');
  echo json_encode($resp);
 ?>