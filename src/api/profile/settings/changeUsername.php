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
 
    if (!isset($_POST['u'])) {
        return getError("args");
    }
        
    $username = preg_replace('/^[\p{Z}\p{C}]+|[\p{Z}\p{C}]+$/u', '', htmlspecialchars($_POST['u']));
    
    if(strlen($username) > 24) {
        return getError("userMax");
    } else if(strlen($username) < 4) {
        return getError("userMin");
    }
            
    $sql = "SELECT * FROM users WHERE username='$username'";
    $result = $conn->query($sql);

    $user_id = $_SESSION["user_id"];

    if ($result->num_rows !== 0) {
        return getError("tUser");
    }

    $sql = "UPDATE users SET username = '$username' WHERE user_id = '$user_id'";

    if ($conn->query($sql) === FALSE) {
        return getError() . " CU0";
    }
}