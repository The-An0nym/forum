<?php
$path = $_SERVER['DOCUMENT_ROOT'];
include $path . '/functions/.connect.php' ;
include $path . '/functions/moderation.php' ;
include $path . '/functions/validateSession.php';

echo response()

function reponse() {
    // Get connection
    $conn = getConn();

    if(!session_id()) {
    session_start();
    } 

    if(!validateSession()) {
        return "Please login";
    }
    $json_params = file_get_contents("php://input");

    if (strlen($json_params) === 0 || !json_validate($json_params)) {
        return "Invalid or missing argument(s)";
    }
    $decoded_params = json_decode($json_params);

    $type = (int)$decoded_params->t;
    $id = $decoded_params->i;
    $reason = (int)$decoded_params->r;

    $message = preg_replace('/^[\p{Z}\p{C}]+|[\p{Z}\p{C}]+$/u', '', htmlspecialchars($decoded_params->m));
    if(strlen($message) < 20 || strlen($message) > 200) {
        return "Message needs to be between 20 to 200 chars";
    }

    $user_id = $_SESSION['user_id'];

    // Check if already reported
    createReport($type, $id, $user_id, $reason, $message);
}