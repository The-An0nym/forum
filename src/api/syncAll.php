<?php
$path = $_SERVER['DOCUMENT_ROOT'];
include $path . '/functions/.connect.php' ;
include $path . '/functions/statCount.php';

// Get connection
$conn = getConn();

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
} 

if(!session_id()) {
  session_start();
} 

if(include($path . "/functions/validateSession.php")) {
    $conn = getConn();
    $user_id = $_SESSION['user_id'];
    $sql = "SELECT clearance FROM users 
            WHERE user_id = '$user_id'
            LIMIT 1";

    $result = $conn->query($sql);

    if($result->num_rows === 1) {
        $row = $result->fetch_assoc();
        $clearance = $row['clearance'];

        if($clearance == 5) {
            syncAll();
        } else {
            echo "Clearance level too low";
        }
    } else {
        echo "An error has occured SN2";
    }

} else {
    echo "Please login";
}