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
        return "Please log in to continue";
    }
 
    if (!isset($_POST['u'])) {
        return "Invalid or missing argument(s)";
    }
        
    $username = preg_replace('/^[\p{Z}\p{C}]+|[\p{Z}\p{C}]+$/u', '', htmlspecialchars($_POST['u']));
    
    if(strlen($username) > 24) {
        return "Max 24. chars allowed for username";
    } else if(strlen($username) < 4) {
        return "Min. 4 chars needed for username";
    }
            
    $sql = "SELECT * FROM users WHERE username='$username'";
    $result = $conn->query($sql);

    $user_id = $_SESSION["user_id"];

    if ($result->num_rows !== 0) {
        return "Username is already taken!"
    }

    $sql = "UPDATE users SET username = '$username' WHERE user_id = '$user_id'";

    if ($conn->query($sql) === FALSE) {
        return "An error has occured while trying to change your username";
    }
}