<?php
include 'lib/websocket.class.php';

class ChatServer extends WebSocket {
  /**
   * Dictionary for in-connection clients. Key is the user_id, value is the socket
   * @var array
   */
  var $sockets_dict = array();

  /**
   * Override WebSocket process method.
   * @param  User   $user user object with socket
   * @param  String $msg  received message, json encoded
   */
  function process($user, $msg) {
    // Decode messages
    $json = json_decode($msg);
    if (is_null($json)) { return; }

    $sender_id = $json->{'user_id'};
    $receiver_id = $json->{'receiver_id'};
    $message = $json->{'msg'};
    // Initialization message
    if (strpos($msg, '[INITIALIZATION_CONFIG]') !== false) {
      $this->sockets_dict[$sender_id] = $user->socket;
    } else {
      $this->write_to_database($sender_id, $receiver_id, $message);

      if (array_key_exists($receiver_id, $this->sockets_dict)) {
        $receiver_socket = $this->sockets_dict[$receiver_id];
        $message_obj = [
          'sender_id' => $sender_id,
          'message' => $message
        ];
        $this->send($receiver_socket, json_encode($message_obj));
      }
    }
  }

  /**
   * Write message to database
   * @param  String $sender_id
   * @param  String $receiver_id
   * @param  String $message
   */
  function write_to_database($sender_id, $receiver_id, $message) {
    $server_name = '127.0.0.1';
    $username = 'upintheair_admin';
    $password = 'admin411';
    $db_name = 'upintheair_default';

    $conn = new mysqli($server_name, $username, $password, $db_name);
    if ($conn->connect_error) {
      die('Connection failed: ' . $conn->connect_error);
    }

    $sql = "INSERT INTO messages (sender_id, receiver_id, message) VALUES ('$sender_id', '$receiver_id', '$message')";

    if (!$conn->query($sql)) {
      die('Database write failed: ' . $conn->connect_error);
    }
  }
}

// Change to localhost when debugging
$master = new ChatServer("192.17.90.133", 12345);
// $master = new ChatServer('localhost', 12345);
