<?php
  $server_name = '127.0.0.1';
  $username = 'upintheair_admin';
  $password = 'admin411';
  $db_name = 'upintheair_default';

  $conn = new mysqli($server_name, $username, $password, $db_name);
  if ($conn->connect_error) {
    die('Connection failed: ' . $conn->connect_error);
  }

  $email = $_Post['email'];

  $sql_seat_window = "SELECT COUNT(*) as num_win FROM user, Flight_record WHERE Flight_record.seat = 'window' AND user.id = Flight_record.userid AND user.email = '".$email."'";
  $sql_seat_middle = "SELECT COUNT(*) as num_mid FROM user, Flight_record WHERE Flight_record.seat = 'middle' AND user.id = Flight_record.userid AND user.email = '".$email."'";
  $sql_seat_aisle = "SELECT COUNT(*) as num_ais FROM user, Flight_record WHERE Flight_record.seat = 'aisle' AND user.id = Flight_record.userid AND user.email = '".$email."'";

  if (($conn->query($sql_seat_window) == FALSE)
  	|| ($conn->query($sql_seat_middle) == FALSE)
  	|| ($conn->query($sql_seat_aisle) == FALSE)) {

  	$resp = [
  	  'status' => 'fail',
  	  'table' => 'seat',
  	  'message' => 'Cannot retrieve seat data'
    ];
  } else {

  	$result_window = $conn->query($sql_seat_window);
  	$result_middle = $conn->query($sql_seat_middle);
  	$result_aisle = $conn->query($sql_seat_aisle);

  	$num_window = mysql_fetch_assoc($result_window);
  	$num_middle = mysql_fetch_assoc($result_middle);
  	$num_aisle = mysql_fetch_assoc($result_aisle);

  	$resp = [
  	  'status' => 'success',
  	  'table' => 'seat',
  	  'data' => [
  	    'window' => $num_window['num_win'],
  	    'middle' => $num_middle['num_mid'],
  	    'aisle' => $num_aisle['num_ais']
  	  ]
  	];

  }

  $conn->close();
  header('Content-Type: application/json');
  echo json_encode($resp);
 ?>