<?php
$path = $_SERVER['DOCUMENT_ROOT'];
include $path . '/functions/.connect.php' ;
include $path . '/functions/moderation.php' ;
include $path . '/functions/validateSession.php';

// Get connection
$conn = getConn();

if(!session_id()) {
  session_start();
}

if(validateSession()) {
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

            if($clearance >= 4 && $user_clearance < $clearance) {
                // Push onto history
                createHistory(2, 6, $id, $user_id, $reason, $message);
                
                // Demote user
                $sql = "UPDATE users SET clearance = clearance - 1 WHERE user_id = '$id'";
                if ($conn->query($sql) === FALSE) {
                    echo "ERROR: Please try again later [DU1]";
                }
            } else {
                echo "Clearance level too low";
            }
        } else {
            echo "An error has occured DU2";
        }
    } else {
        echo "An error has occured DU3";
    }

} else {
    echo "Please login";
}