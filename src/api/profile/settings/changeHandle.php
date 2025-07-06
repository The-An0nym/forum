<?php
$path = $_SERVER['DOCUMENT_ROOT'];
include $path . '/functions/.connect.php' ;
include $path . '/functions/validateSession.php';

echo response();

function response() {
    // Get connection
    $conn = getConn();

    if(!session_id()) {
        session_start();
    }

    if(!validateSession()) {
        return "Please login to continue";
    }

    $json_params = file_get_contents("php://input");

    if (strlen($json_params) === 0 && !json_validate($json_params)) {
        return "Invalid or missing argument(s)";
    }
        
    $decoded_params = json_decode($json_params);

    $handle = preg_replace('/^[\p{Z}\p{C}]+|[\p{Z}\p{C}]+$/u', '', htmlspecialchars($decoded_params->h));
    
    if(preg_match('/^[A-z0-9.\-+]*$/i', $handle) !== 1) {
        return "Only characters <b>a-Z 0-9 + - _ .</b> are allowed";
    } else if(strlen($handle) > 16) {
        return "Max 16. chars allowed for the handle";
    } else if(strlen($handle) < 4) {
        return "Min. 4 chars needed for the handle";
    }

    $sql = "SELECT * FROM users WHERE handle='$handle' LIMIT 1";
    $result = $conn->query($sql);

    if ($result->num_rows !== 0) {
        return "This handle is already taken!";
    }
    
    $user_id = $_SESSION["user_id"];

    $sql = "UPDATE users SET handle = '$handle' WHERE user_id = '$user_id'";

    if ($conn->query($sql) === FALSE) {
        return "Failed to update handle";
    }
}