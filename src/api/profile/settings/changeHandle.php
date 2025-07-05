<?php
$path = $_SERVER['DOCUMENT_ROOT'];
include $path . '/functions/.connect.php' ;
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

        $handle = preg_replace('/^[\p{Z}\p{C}]+|[\p{Z}\p{C}]+$/u', '', htmlspecialchars($decoded_params->h));
        
        if(strlen($handle) <= 16 && strlen($handle) >= 4 && preg_match('/^[A-z0-9.\-_]*$/i', $handle) === 1) {

            $sql = "SELECT * FROM users WHERE handle='$handle' LIMIT 1";
            $result = $conn->query($sql);

            if ($result->num_rows === 0) {
                $user_id = $_SESSION["user_id"];

                $sql = "UPDATE users SET handle = '$handle' WHERE user_id = '$user_id'";

                if ($conn->query($sql) === FALSE) {
                    echo "ERROR: Please try again later [CU0]";
                }
            } else {
                echo "Handle is already taken!";
            }
        } else if(preg_match('/^[A-z0-9.\-+]*$/i', $handle) !== 1) {
            echo "Only characters <b>a-Z 0-9 + - _ .</b> are allowed";
        } else if(strlen($handle) > 16) {
            echo "Max 16. chars allowed for the handle";
        } else if(strlen($handle) < 4) {
            echo "Min. 4 chars needed for the handle";
        } else {
            echo "No input";
        }
    } else {
        echo "ERROR: Please try again later [CU1]";
    }
} else {
    echo "Please log in to continue";
}