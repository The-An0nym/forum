<?php
$path = $_SERVER['DOCUMENT_ROOT'];
require_once $path . '/functions/.connect.php' ;
require_once $path . '/functions/statCount.php';
require_once $path . '/functions/validateSession.php';
require_once $path . '/functions/errors.php' ;

echo response();

function response() : string {

    // Get connection
    $conn = getConn(); 

    if(!session_id()) {
        session_start();
    } 

    if(!validateSession()) {
        return jsonErr("login");
    }

    $conn = getConn();
    $user_id = $_SESSION['user_id'];
    $sql = "SELECT `clearance` FROM `users` WHERE `user_id` = '$user_id' LIMIT 1";

    $result = $conn->query($sql);

    if($result->num_rows !== 1) {
        return jsonErr("404user");
    }
        
    $row = $result->fetch_assoc();
    $clearance = $row['clearance'];

    if($clearance != 5) {
        return jsonErr("auth");
    }

    $err = jsonEncodeErrors(syncAll());
    if($err !== "") {
        return $err;
    }
    
    return pass();
}