<?php
$path = $_SERVER['DOCUMENT_ROOT'];
require_once $path . '/functions/.connect.php' ;
require_once $path . '/functions/statCount.php';
require_once $path . '/functions/validateSession.php';
require_once $path . '/functions/errors.php' ;

echo response();

function response() {

    // Get connection
    $conn = getConn();

    if(!session_id()) {
        session_start();
    }

    if(!isset($_POST["m"])) {
        return jsonErr("args");
    }

    if(!validateSession()) {
        return jsonErr("login");
    }

    $mode = (int)$_POST["m"];

    $user_id = $_SESSION["user_id"];

    $sql = "UPDATE `users` SET `appearance` = $mode WHERE `user_id` = '$user_id'";
    if($conn->query($sql) === FALSE) {
        return jsonErr("mode");
    }
    
    return getPass("saveAppear");
}