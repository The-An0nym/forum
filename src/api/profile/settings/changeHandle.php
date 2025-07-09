<?php
$path = $_SERVER['DOCUMENT_ROOT'];
include $path . '/functions/.connect.php' ;
include $path . '/functions/validateSession.php';
include $path . '/functions/errors.php' ;

echo response();

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

    if (strlen($json_params) === 0 && !json_validate($json_params)) {
        return jsonErr("args");
    }
        
    $json_obj = json_decode($json_params);

    if(!isset($json_obj->h)) {
        return jsonErr("args");
    }

    $handle = preg_replace('/^[\p{Z}\p{C}]+|[\p{Z}\p{C}]+$/u', '', htmlspecialchars($json_obj->h));
    
    if(preg_match('/^[A-z0-9.\-+]*$/i', $handle) !== 1) {
        return jsonErr("handReg");
    } else if(strlen($handle) > 16) {
        return jsonErr("handMax");
    } else if(strlen($handle) < 4) {
        return jsonErr("handMin");
    }

    $sql = "SELECT * FROM users WHERE handle='$handle' LIMIT 1";
    $result = $conn->query($sql);

    if ($result->num_rows !== 0) {
        return jsonErr("tHand");
    }
    
    $user_id = $_SESSION["user_id"];

    $sql = "UPDATE users SET handle = '$handle' WHERE user_id = '$user_id'";

    if ($conn->query($sql) === FALSE) {
        return jsonErr("", "[CH0]");
    }
    
    return pass();
}