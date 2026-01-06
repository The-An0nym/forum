<?php
$path = $_SERVER['DOCUMENT_ROOT'];
require_once $path . '/functions/.connect.php' ;
require_once $path . '/functions/validateSession.php';
require_once $path . '/functions/errors.php' ;

echo response();

function response() : string {
    // Get connection
    $conn = getConn();

    if(!session_id()) {
        session_start();
    } 

    if(!validateSession()) {
        return jsonErr("login");
    }
 
    if (!isset($_POST['u'])) {
        return jsonErr("args");
    }
        
    $username = preg_replace('/^[\p{Z}\p{C}]+|[\p{Z}\p{C}]+$/u', '', htmlspecialchars($_POST['u']));
    
    if(strlen($username) > 24) {
        return jsonErr("userMax");
    } else if(strlen($username) < 4) {
        return jsonErr("userMin");
    }
            
    $sql = "SELECT * FROM `users` WHERE `username` = '$username'";
    $result = $conn->query($sql);

    $user_id = $_SESSION["user_id"];

    if ($result->num_rows !== 0) {
        return jsonErr("tUser");
    }

    $sql = "UPDATE `users` SET `username` = '$username' WHERE `user_id` = '$user_id'";

    if ($conn->query($sql) === FALSE) {
        return jsonErr("", "[CU0]");
    }

    return pass();
}