<?php
$path = $_SERVER['DOCUMENT_ROOT'];
include $path . '/functions/.connect.php' ;

// Get connection
$conn = getConn();

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
} 

if(!session_id()) {
  session_start();
} 

if(include($path . "/functions/validateSession.php")) {
    $json_params = file_get_contents("php://input");

    if (strlen($json_params) > 0 && json_validate($json_params)) {
        $decoded_params = json_decode($json_params);

        $type = (int)$decoded_params->t;
        $id = $decoded_params->i;
        $reason = (int)$decoded_params->r;

        $message = preg_replace('/^[\p{Z}\p{C}]+|[\p{Z}\p{C}]+$/u', '', htmlspecialchars($decoded_params->m));
        if(strlen($message) < 20 || strlen($message) > 200) {
            echo "Message needs to be between 20 to 200 chars";
            die();
        }

        $user_id = $_SESSION['user_id'];

        // Check if already reported
        $sql = "SELECT sender_id FROM reports WHERE sender_id = '$user_id' AND type = $type AND id = '$id'";
        $result = $conn->query($sql);
        if($result->num_rows === 0) {
            // Report
            $sql = "INSERT INTO reports (type, id, sender_id, reason, message)
            VALUES ($type, '$id', '$user_id', $reason, '$message')";
            if ($conn->query($sql) === FALSE) {
                echo "ERROR: Please try again later [R0]";
            }
        } else {
            echo "You have already reported this post";

        }
    } else {
        echo "An error has occured R1";
    }

} else {
    echo "Please login";
}