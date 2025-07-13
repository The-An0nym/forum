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

    if (!isset($_POST["r"], $_POST["i"])) {
        return jsonErr("args");
    }

    $id = $_POST["i"];
    $as = (int)$_POST["r"];

    if($as !== 0 && $as !== 1) {
        return jsonErr("args");
    }

    $conn = getConn();
    $user_id = $_SESSION['user_id'];
    $sql = "SELECT u.clearance FROM users u 
            WHERE u.user_id = '$user_id'
            LIMIT 1";

    $result = $conn->query($sql);
    if($result->num_rows !== 1) {
        return jsonErr("404user");
    }

    $clearance = (int)$result->fetch_assoc()["clearance"];

    $sql = "SELECT type FROM mod_history WHERE mod_id = '$id' LIMIT 1";

    $result = $conn->query($sql);
    $type = (int)$result->fetch_assoc()["type"];

    if($type >= $clearance) {
        return jsonErr("auth");
    }

    // Update
    $sql = "UPDATE mod_history SET judgement = $as WHERE mod_id = '$id'";
    if ($conn->query($sql) === FALSE) {
        return jsonErr("", "[MR0]");
    }

    return pass();
}