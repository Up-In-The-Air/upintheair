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

  $sql_seat_first = "SELECT COUNT(*) as num_fs FROM user, Flight_record WHERE Flight_record.class LIKE 'F%' AND user.id = Flight_record.userid AND user.email = '".$email."'";
  $sql_seat_bus = "SELECT COUNT(*) as num_bs FROM user, Flight_record WHERE Flight_record.class LIKE 'B%' AND user.id = Flight_record.userid AND user.email = '".$email."'";
  $sql_seat_ecop = "SELECT COUNT(*) as num_ep FROM user, Flight_record WHERE Flight_record.class LIKE 'E%+' AND user.id = Flight_record.userid AND user.email = '".$email."'";
  $sql_seat_eco = "SELECT COUNT(*) as num_ec FROM user, Flight_record WHERE Flight_record.class = 'Economy' AND user.id = Flight_record.userid AND user.email = '".$email."'";

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

  	$result_first = $conn->query($sql_seat_first);
  	$result_bus = $conn->query($sql_seat_bus);
  	$result_ecop = $conn->query($sql_seat_ecop);
  	$result_eco = $conn->query($sql_seat_eco);

  	$num_seat_fs = mysql_fetch_assoc($result_first);
  	$num_seat_bus = mysql_fetch_assoc($result_bus);
  	$num_seat_ecop = mysql_fetch_assoc($result_ecop);
  	$num_seat_eco = mysql_fetch_assoc($result_eco);
  
  	$resp = [
  	  'status' => 'success',
  	  'table' => 'class',
  	  'data' => [
  	    'first_class' => $num_seat_fs['num_fs'],
  	    'business' => $num_seat_bus['num_bs'],
  	    'economy+' => $num_seat_ecop['num_ep'],
  	    'economy' => $num_seat_eco['num_ec']
  	  ]
    ];
  }

  $conn->close();
  header('Content-Type: application/json');
  echo json_encode($resp);
?>