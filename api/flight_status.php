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

  $sql_seat_first = "SELECT COUNT(*) as fs FROM user, Flight_record WHERE "
?>