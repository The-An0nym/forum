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
        $reason = (int)$decoded_params->r;

        $message = preg_replace('/^[\p{Z}\p{C}]+|[\p{Z}\p{C}]+$/u', '', htmlspecialchars($decoded_params->m));
        if(strlen($message) < 20 || strlen($message) > 200) {
            echo "Message needs to be between 20 to 200 chars";
            die();
        }

        $conn = getConn();
        $user_id = $_SESSION['user_id'];
        $sql = "SELECT u.clearance, t.user_id 
                    FROM users u 
                JOIN threads t 
                    ON t.id = '$id' 
                WHERE u.user_id = '$user_id'
                LIMIT 1";

        $result = $conn->query($sql);

        if($result->num_rows === 1) {
            $row = $result->fetch_assoc();
            $clearance = $row['clearance'];
            $creator_user_id = $row['user_id'];
            $user_id === $_SESSION["user_id"];            

            if($clearance >= 1) {
                countForThread($id, false);

                // Push onto history
                createHistory($conn, 1, 2, $id, $user_id, $reason, $message);
                
                // (Soft) delete thread
                $dtime = date('Y-m-d H:i:s');
                $sql = "UPDATE threads SET deleted = deleted | 2, deleted_datetime = '$dtime' WHERE id = '$id'";
                if ($conn->query($sql) === FALSE) {
                    echo "ERROR: Please try again later [DT4]";
                }

                // (Soft) delete posts
                $dtime = date('Y-m-d H:i:s');
                $sql = "UPDATE posts SET deleted = deleted | 4, deleted_datetime = '$dtime' WHERE thread_id = '$id'";
                if ($conn->query($sql) === FALSE) {
                    echo "ERROR: Please try again later [DT4]";
                }
            } else {
                echo "Clearance level too low";
            }
        } else {
            echo "An error has occured DT5";
        }
    } else {
        echo "An error has occured DT6";
    }

} else {
    echo "Please login";
}