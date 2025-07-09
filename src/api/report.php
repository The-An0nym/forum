<?php
$path = $_SERVER['DOCUMENT_ROOT'];
include $path . '/functions/.connect.php' ;
include $path . '/functions/moderation.php' ;
include $path . '/functions/validateSession.php';
include $path . '/functions/errors.php' ;

echo response()

function response() {
    // Get connection
    $conn = getConn();

    if(!session_id()) {
        session_start();
    } 

    if(!validateSession()) {
        return jsonErr("login");
    }
    $json_params = file_get_contents("php://input");

    if (strlen($json_params) === 0 || !json_validate($json_params)) {
        return jsonErr("args");
    }

    $json_obj = json_decode($json_params);

    if(!isset($json_obj->t, $json_obj->i, $json_obj->r, $json_obj->m)) {
        return jsonErr("args");
    }

    $type = (int)$json_obj->t;
    $id = $json_obj->i;
    $reason = (int)$json_obj->r;

    $message = preg_replace('/^[\p{Z}\p{C}]+|[\p{Z}\p{C}]+$/u', '', htmlspecialchars($json_obj->m));
    if(strlen($message) < 20 || strlen($message) > 200) {
        return jsonErr("msgMinMax");
    }

    $user_id = $_SESSION['user_id'];

    // Check if already reported
    
    createReport($type, $id, $user_id, $reason, $message); // HANDLE ERROR

    return pass();
}