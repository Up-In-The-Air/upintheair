<?php
  header('Content-Type: text/event-stream');
  header('Cache-Control: no-cache');

  $time = date('r');
  $response = [
    'time' => $time
  ];
  echo "data:".json_encode($response)."\n\n";
  flush();
?>
