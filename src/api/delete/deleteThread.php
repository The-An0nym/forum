<?php
$path = $_SERVER['DOCUMENT_ROOT'];
require_once $path . '/functions/.connect.php' ;
require_once $path . '/functions/validateSession.php';
require_once $path . '/functions/moderation.php' ;
require_once $path . '/functions/statCount.php';

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

    if (strlen($json_params) === 0 || !json_validate($json_params)) {
        return jsonErr("args");
    }

    $json_obj = json_decode($json_params);
    
    if(!isset($json_obj->i, $json_obj->r, $json_obj->m)) {
        return jsonErr("args");
    }

    $id = $json_obj->i;
    $reason = (int)$json_obj->r;

    $message = preg_replace('/^[\p{Z}\p{C}]+|[\p{Z}\p{C}]+$/u', '', htmlspecialchars($json_obj->m));
    
    if(strlen($message) < 20 || strlen($message) > 200) {
        return jsonErr("msgMinMax");
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
        return jsonErr("404user");
    }

    $row = $result->fetch_assoc();
    $clearance = $row['clearance'];
    $creator_user_id = $row['user_id'];
    $user_id === $_SESSION["user_id"];            

    if($clearance < 1) {
        return jsonErr("auth");
    }

    $err = jsonEncodeErrors(countForThread($id, false));
    if($err !== "") {
        return $err;
    }

    // Push onto history
    $err = jsonEncodeErrors(createHistory(1, 2, $id, $user_id, $reason, $message));
    if($err !== "") {
        return $err;
    }

    $err = jsonEncodeErrors(deleteThread($id, 4, false));
    if($err !== "") {
        return $err;
    }

    return pass();
}