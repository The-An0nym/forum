<?php

if(!function_exists("clearCurrSession")) {

$path = $_SERVER['DOCUMENT_ROOT'];
include $path . '/functions/.connect.php' ;
include $path . '/functions/errors.php' ;

function clearCurrSession() : array {
    // Get Connection
    $conn = getConn();
    
    if(!session_id()) {
        session_start();
    }
    
    if(!isset($_SESSION['session_id'], $_SESSION['user_id'])) {
        return ["login"];
    }

    $session_id = $_SESSION['session_id'];
    $sql = "DELETE FROM sessions WHERE session_id='$session_id'";
    $result = $conn->query($sql);

    if ($conn->query($sql) === TRUE) {
        unset($_SESSION['user_id']);
        unset($_SESSION['session_id']);
        return ["pass"];
    } else {
        return ["", "[SCC0]"];
    }

    return ["", "[SCC1]"];
}

function deleteExpiredSessions() : array {
    // Get connection
    $conn = getConn();

    if(!session_id()) {
        session_start();
    }

    $user_id = $_SESSION['user_id'];

    $delArr = array();
    
    $delDatetime = date('Y-m-d H:i:s', strtotime(date('Y-m-d H:i:s')) - 60 * 60 * 24 * 5); // 5 days

    $sql = "DELETE FROM sessions WHERE datetime < '$delDatetime'";
    if($conn->query($sql) === FALSE) {
        return ["", "[SCD0]"];
    }

    return ["pass"];
}

function updateSession(string $id) : array{
    if($id === "") {
        return ["args"];
    }

    // Get connection
    $conn = getConn();

    if(!session_id()) {
        session_start();
    }

    $datetime = date("Y-m-d H:i:s");
    $session_id = $_SESSION["session_id"];
    $sql = "UPDATE `sessions` SET `datetime` = '$datetime' WHERE session_id = '$session_id'";
    if($conn->query($sql) === FALSE) {
        return ["", "[SCU0]"];
    }

    return ["pass"];
}

}