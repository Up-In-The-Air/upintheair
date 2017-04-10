<?php
  $server_name = '127.0.0.1';
  $username = 'upintheair_admin';
  $password = 'admin411';
  $db_name = 'upintheair_default';

  $conn = new mysqli($server_name, $username, $password, $db_name);
  if ($conn->connect_error) {
    die('Connection failed: ' . $conn->connect_error);
  }

  $iata = $_GET['iata'];

  if (!$iata) {
    $resp = [
      'status' => 'fail',
      'message' => 'Missing required fields'
    ];
    header('Content-Type: application/json');
    echo json_encode($resp);
    exit();
  }

  $sql = "SELECT * FROM airport WHERE iata='$iata'";
  $result = $conn->query($sql);
  $row = $result->fetch_assoc();
  $resp = [
    'status' => 'success',
    'data' => $row
  ];

  $conn->close();
  header('Content-Type: application/json');
  echo json_encode($resp);
?>
