<?php
$path = $_SERVER['DOCUMENT_ROOT'];
include $path . '/functions/.connect.php' ;
include $path . '/functions/moderation.php' ;
include $path . '/functions/statCount.php';

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

        $id = $decoded_params->i;

        if(isset($decoded_params->t)) {
            $del_threads = (bool)$decoded_params->t;
        } else {
            $del_threads = false;
        }

        $conn = getConn();
        $user_id = $_SESSION['user_id'];
        $sql = "SELECT u.clearance, b.clearance AS user_clearance 
                    FROM users u 
                JOIN users b 
                    ON b.user_id = '$id' 
                WHERE u.user_id = '$user_id'
                LIMIT 1";

        $result = $conn->query($sql);

        if($result->num_rows === 1) {
            $row = $result->fetch_assoc();
            $clearance = $row['clearance'];
            $user_clearance = $row['user_clearance'];

            if($post_user_id !== $user_id || $clearance >= 3) {
                if(isset($decoded_params->m, $decoded_params->r)) {
                    $reason = (int)$decoded_params->r;
                    $message = preg_replace('/^[\p{Z}\p{C}]+|[\p{Z}\p{C}]+$/u', '', htmlspecialchars($decoded_params->m));
                    if(strlen($message) < 20 || strlen($message) > 200) {
                        echo "Message needs to be between 20 to 200 chars";
                        die();
                    }
                } else {
                    echo "Message and reason required";
                    die();
                }
            }

            if(($clearance >= 3 && $user_clearance < $clearance) || $id === $user_id) {
                $type = 1; // Self-deleted
                if($id !== $user_id) {
                    // Push onto history
                    if($del_threads) {
                        createHistory(2, 2, $id, $user_id, $reason, $message);
                    } else {
                        createHistory(2, 3, $id, $user_id, $reason, $message);
                    }
                    $type = 8; // Banned
                } else {
                    $del_threads = false;
                }

                countForUser($id, false, $del_threads);

                deleteAccount($id, $type, $del_threads, false);
            } else {
                echo "Clearance level too low";
            }
        } else {
            echo "An error has occured BU8";
        }
    } else {
        echo "An error has occured BU9";
    }

} else {
    echo "Please login";
}