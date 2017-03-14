<?php
  //deliever basic flight record for user
  
  //connect to db
  $server_name = '127.0.0.1';
  $username = 'upintheair_admin';
  $password = 'admin411';
  $db_name = 'upintheair_default';

  $conn = new mysqli($server_name, $username, $password, $db_name);
  if ($conn->connect_error) {
    die('Connection failed: ' . $conn->connect_error);
  }

  $email = $_Post['email'];

  $sql_seat_0 = "SELECT COUNT(flight_record.class) as num_fs FROM user, flight_record WHERE flight_record.class = 0 AND user.id = flight_record.userid AND user.email = '".$email."'";
  $sql_seat_1 = "SELECT COUNT(flight_record.class) as num_bs FROM user, flight_record WHERE flight_record.class = 1 AND user.id = flight_record.userid AND user.email = '".$email."'";
  $sql_seat_2 = "SELECT COUNT(flight_record.class) as num_ep FROM user, flight_record WHERE flight_record.class = 2 AND user.id = flight_record.userid AND user.email = '".$email."'";
  $sql_seat_3 = "SELECT COUNT(flight_record.class) as num_ec FROM user, flight_record WHERE flight_record.class = 3 AND user.id = flight_record.userid AND user.email = '".$email."'";

  if (($conn->query($sql_seat_first) == FALSE) 
  	|| ($conn->query($sql_seat_bus) == FALSE) 
  	|| ($conn->query($sql_seat_ecop) == FALSE)
  	|| ($conn->query($sql_seat_eco) == FALSE)) {

  	$resp = [
  	  'status' => 'fail',
  	  'table' => 'class',
  	  'message' => 'Cannot retrieve class data'
    ];
  } else {

  	$result_first = $conn->query($sql_seat_0);
  	$result_bus = $conn->query($sql_seat_1);
  	$result_ecop = $conn->query($sql_seat_2);
  	$result_eco = $conn->query($sql_seat_3);

  	$num_seat_fs = mysql_fetch_assoc($result_first);
  	$num_seat_bus = mysql_fetch_assoc($result_bus);
  	$num_seat_ecop = mysql_fetch_assoc($result_ecop);
  	$num_seat_eco = mysql_fetch_assoc($result_eco);
  
  	$resp = [
  	  'status' => 'success',
  	  'table' => 'class',
  	  'data' => [
  	    '0' => $num_seat_fs['num_fs'],
  	    '1' => $num_seat_bus['num_bs'],
  	    '2' => $num_seat_ecop['num_ep'],
  	    '3' => $num_seat_eco['num_ec']
  	  ]
    ];
  }

  $conn->close();
  header('Content-Type: application/json');
  echo json_encode($resp);
?>