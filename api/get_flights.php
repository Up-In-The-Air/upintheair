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

  $sql = "SELECT DISTINCT flight_record.*, "
        ."a.name AS dep_airport_name, a.city AS dep_airport_city, a.country AS dep_airport_country, a.iata AS dep_airport_iata, "
        ."b.name AS arr_airport_name, b.city AS arr_airport_city, b.country AS arr_airport_country, b.iata AS arr_airport_iata, "
        ."airline.name AS airline_name, airline.iata AS airline_iata, "
        ."aircraft.name AS aircraft_name, aircraft.iata AS aircraft_iata "
        ."FROM flight_record, airport a, airport b, airline, aircraft "
        ."WHERE flight_record.user_id = '$user_id' "
        ."AND a.id = flight_record.dep_airport_id AND b.id = flight_record.arr_airport_id AND "
        ."(airline.id = flight_record.airline_id OR flight_record.airline_id IS NULL) AND "
        ."(aircraft.id = flight_record.aircraft_id OR flight_record.aircraft_id IS NULL) "
        ."ORDER BY date DESC";

  $result = $conn->query($sql);

  if ($result === false) {
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
