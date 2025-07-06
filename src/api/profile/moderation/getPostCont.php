<?php
$path = $_SERVER['DOCUMENT_ROOT'];
include $path . '/functions/.connect.php' ;
include $path . '/functions/validateSession.php';

echo response();

function response() {
    // Get connection
    $conn = getConn();

    if(!session_id()) {
        session_start();
    } 

    if(!validateSession()) {
        return "Please login to continue";
    }

    if (!isset($_GET["i"])) {
        return "Invalid or missing argument(s)";
    }

    $id = $_GET["i"];

    $conn = getConn();
        
    $user_id = $_SESSION['user_id'];
    $sql = "SELECT u.clearance FROM users u 
            WHERE u.user_id = '$user_id'
            LIMIT 1";

    $result = $conn->query($sql);
    if($result->num_rows !== 1) {
        return "An error has occured MR0";
    }

    $clearance = (int)$result->fetch_assoc()["clearance"];

    if($clearance < 1) {
        return "Authorization not high enough";
    }

    $sql = "SELECT content FROM posts WHERE post_id = '$id'";
        
    $result = $conn->query($sql);
    if($result->num_rows !== 1) {
        return "An error has occured MR1";
    }

    $cont = $result->fetch_assoc()["content"];

    return $cont; 
}