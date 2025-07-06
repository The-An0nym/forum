<?php
$path = $_SERVER['DOCUMENT_ROOT'];
include $path . '/functions/.connect.php' ;
include $path . '/functions/validateSession.php';
include $path . '/functions/moderation.php' ;
include $path . '/functions/statCount.php';

echo response();

function response() {
    // Get connection
    $conn = getConn();

    if(!session_id()) {
    session_start();
    } 

    if(validateSession()) {
        return getError("login");
    }

    $json_params = file_get_contents("php://input");

    if (strlen($json_params) === 0 || !json_validate($json_params)) {
        return getError("args");
    }

    $decoded_params = json_decode($json_params);

    $id = $decoded_params->i;
    $reason = (int)$decoded_params->r;

    $message = preg_replace('/^[\p{Z}\p{C}]+|[\p{Z}\p{C}]+$/u', '', htmlspecialchars($decoded_params->m));
    
    if(strlen($message) < 20 || strlen($message) > 200) {
        return getError("msgMinMax");
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

    if($result->num_rows !== 1) {
        return getError("404user");
    }

    $row = $result->fetch_assoc();
    $clearance = $row['clearance'];
    $creator_user_id = $row['user_id'];
    $user_id === $_SESSION["user_id"];            

    if($clearance >= 1) {
        countForThread($id, false);

        // Push onto history
        createHistory(1, 2, $id, $user_id, $reason, $message);

        deleteThread($id, 4, false);
    } else {
        return getError("auth");
    }
}