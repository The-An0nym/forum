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
    if(!isset($_GET["m"])) {
        echo "missing argument(s)";
        die();
    }

    $mode = (int)$_GET["m"];

    $user_id = $_SESSION["user_id"];

    $sql = "UPDATE users SET darkmode = $mode WHERE user_id = '$user_id'";
    if($conn->query($sql) === FALSE) {
        echo "An error has occured while trying to update UI mode";
    }

} else {
    echo "Please login";
}