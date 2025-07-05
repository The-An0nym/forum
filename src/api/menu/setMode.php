<?php
$path = $_SERVER['DOCUMENT_ROOT'];
include $path . '/functions/.connect.php' ;
include $path . '/functions/statCount.php';
include $path . '/functions/validateSession.php';

echo response();

function response() {

    // Get connection
    $conn = getConn();

    if(!session_id()) {
        session_start();
    } 

    if(!isset($_GET["m"])) {
        return "missing argument(s)";
    }

    if(!validateSession()) {
        return "Please login";
    }

    $mode = (int)$_GET["m"];

    $user_id = $_SESSION["user_id"];

    $sql = "UPDATE users SET darkmode = $mode WHERE user_id = '$user_id'";
    if($conn->query($sql) === FALSE) {
        return "An error has occured while trying to update UI mode";
    }
}