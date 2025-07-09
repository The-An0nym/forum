<?php
$path = $_SERVER['DOCUMENT_ROOT'];
include $path . '/functions/.connect.php' ;
include $path . '/functions/statCount.php';
include $path . '/functions/validateSession.php';
include $path . '/functions/errors.php' ;

echo response();

function response() {

    // Get connection
    $conn = getConn();

    if(!session_id()) {
        session_start();
    } 

    if(!isset($_GET["m"])) {
        return jsonErr("args");
    }

    if(!validateSession()) {
        return jsonErr("login");
    }

    $mode = (int)$_GET["m"];

    $user_id = $_SESSION["user_id"];

    $sql = "UPDATE users SET darkmode = $mode WHERE user_id = '$user_id'";
    if($conn->query($sql) === FALSE) {
        return jsonErr("mode");
    }
    return pass();
}