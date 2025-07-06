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

    if(!validateSession()) {
        return "Please login to continue";
    }

    $conn = getConn();
    $user_id = $_SESSION['user_id'];
    $sql = "SELECT clearance FROM users 
            WHERE user_id = '$user_id'
            LIMIT 1";

    $result = $conn->query($sql);

    if($result->num_rows !== 1) {
        return "User(s) not found";
    }
        
    $row = $result->fetch_assoc();
    $clearance = $row['clearance'];

    if($clearance == 5) {
        syncAll();
    } else {
        return "Clearance level too low";
    }
}