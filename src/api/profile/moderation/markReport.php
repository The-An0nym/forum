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
        return getError("login");
    }

    if (!isset($_GET["r"], $_GET["i"])) {
        return getError("args");
    }

    $id = $_GET["i"];
    $as = (int)$_GET["r"];

    if($as !== 0 && $as !== 1) {
        return getError("args");
    }

    $conn = getConn();
    $user_id = $_SESSION['user_id'];
    $sql = "SELECT u.clearance FROM users u 
            WHERE u.user_id = '$user_id'
            LIMIT 1";

    $result = $conn->query($sql);
    if($result->num_rows !== 1) {
        return getError("404user");
    }

    $clearance = (int)$result->fetch_assoc()["clearance"];

    $sql = "SELECT type FROM mod_history WHERE mod_id = '$id' LIMIT 1";

    $result = $conn->query($sql);
    $type = (int)$result->fetch_assoc()["type"];

    if($type >= $clearance) {
        return getError("auth");
    }

    // Update
    $sql = "UPDATE mod_history SET judgement = $as WHERE mod_id = '$id'";
    if ($conn->query($sql) === FALSE) {
        return getError() . " [MR0]";
    }
}