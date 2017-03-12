<?php
  $server_name = '127.0.0.1';
  $username = 'upintheair_admin';
  $password = 'admin411';
  $db_name = 'upintheair_default';

  $conn = new mysqli($server_name, $username, $password, $db_name);
  if ($conn->connect_error) {
    die('Connection failed: ' . $conn->connect_error);
  }

  $flight_number = $_POST['flight_number'];
  $date = date ("Y-m-d", $_POST['date'];
  $userid = $_POST['userid'];

  if($flight_number == null){
    $sql = "SELECT * FROM flight_records where date = $date and userid = $userid";
  }
  if($date == null){
    $sql = "SELECT * FROM flight_records where flight_number = '$flight_number' and userid = $userid";
  }
  if($flight_number != null && $date != null){
    $sql = "SELECT * FROM flight_records where date = $date and userid = $userid and flight_number = '$flight_number'";
  }

  $result = $conn->query($sql);

   if ($result->num_rows == 0) {
      $resp = [
        'status' => 'fail',
        'message' => 'no match'
      ];
  }else{
      $resp = [
        'status' => 'success',
        'data' => [
          'id' => $row['id'];
          'flight_number' => $row['flight_number'],
          'date' => $row['date'],
          'class' => $row['class'],
          'seat' => $row['seat'],
          'note' => $row['note']
          'purpose' => $row['purpose'],
          'photourl' => $row['photourl'],
          'source_airport_id' => $row['source_airport_id'],
          'des_airport_id' => $row['des_airport_id'],
          'stops' => $row['stops'],
          'userid' => $row['userid'],
          'aircraft_id' => $row['aircraft_id'],
          'airline_id' => $row['airline_id'],
        ]
      ]
  }
  header('Content-Type: application/json');
  echo json_encode($resp);
  $conn->close();
?>
