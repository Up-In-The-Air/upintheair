<?php
//TodoList:
//1. Change * to not allow null value for all api
  $server_name = '127.0.0.1';
  $username = 'upintheair_admin';
  $password = 'admin411';
  $db_name = 'upintheair_default';

  $conn = new mysqli($server_name, $username, $password, $db_name);
  if ($conn->connect_error) {
    die('Connection failed: ' . $conn->connect_error);
  }

  $email = $_Post['email'];

  $sql_reason_leisure = "SELECT COUNT(Flight_record.purpose) as lei FROM user, Flight_record WHERE Flight_record.purpose = 'leisure' AND user.id = Flight_record.userid AND user.email = '".$email."'";
  $sql_reason_business = "SELECT COUNT(Flight_record.purpose) as bus FROM user, Flight_record WHERE Flight_record.purpose = 'business' AND user.id = Flight_record.userid AND user.email = '".$email."'";
  $sql_reason_crew = "SELECT COUNT(Flight_record.purpose) as crew FROM user, Flight_record WHERE Flight_record.purpose = 'crew' AND user.id = Flight_record.userid AND user.email = '".$email."'";

  if(($conn->query($sql_reason_leisure) == FALSE)
    || ($conn->query($sql_reason_business) == FALSE)
    || ($conn->query($sql_reason_crew) == FALSE)) {

    $resp = [
      'status' => 'fail',
      'table' => 'reason',
      'message' => 'Cannot retrieve reason data'
    ];
  } else {

    $result_leisure = $conn->query($sql_reason_leisure);
    $result_business = $conn->query($sql_reason_business);
    $result_crew = $conn->query($sql_reason_crew);

    $num_leisure = mysql_fetch_assoc($result_leisure);
    $num_business = mysql_fetch_assoc($result_business);
    $num_crew = mysql_fetch_assoc($result_crew);

    $resp = [
      'status' => 'success',
      'table' => 'reason',
      'data' => [
        'leisure' => $num_leisure['lei'],
        'business' => $num_business['bus'],
        'crew' => $num_crew['crew']
      ]
    ];
  }

  $conn->close();
  header('Content-Type: application/json');
  echo json_encode($resp);
  ?>