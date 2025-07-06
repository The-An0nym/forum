<?php
$path = $_SERVER['DOCUMENT_ROOT'];
include $path . '/functions/.connect.php' ;
include $path . '/functions/validateSession.php';

echo response();

function response() {
    // Get connection
    $conn = getConn();

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    } 

    if(!session_id()) {
    session_start();
    } 

    if(!validateSession()) {
        return "Please login to continue";
    }

    if (!isset($_GET["r"], $_GET["i"])) {
        return "Invalid or missing argument(s)";
    }

    $id = $_GET["i"];
    $as = (int)$_GET["r"];

    if($as !== 0 && $as !== 1) {
        return "'AS' not valid";
    }

    $conn = getConn();
    $user_id = $_SESSION['user_id'];
    $sql = "SELECT u.clearance FROM users u 
            WHERE u.user_id = '$user_id'
            LIMIT 1";

    $result = $conn->query($sql);
    if($result->num_rows !== 1) {
        return "User not found";
    }

    $clearance = (int)$result->fetch_assoc()["clearance"];

    $sql = "SELECT type FROM mod_history WHERE mod_id = '$id' LIMIT 1";

    $result = $conn->query($sql);
    $type = (int)$result->fetch_assoc()["type"];

    if($type >= $clearance) {
        return "Insufficient authorization";
    }

    // Update
    $sql = "UPDATE mod_history SET judgement = $as WHERE mod_id = '$id'";
    if ($conn->query($sql) === FALSE) {
        return "An error has occured while updating the report";
    }
}