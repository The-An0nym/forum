<?php
$path = $_SERVER['DOCUMENT_ROOT'];
require_once $path . '/functions/.connect.php' ;
require_once $path . '/functions/validateSession.php';
require_once $path . '/functions/errors.php' ;

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

    $json_params = file_get_contents("php://input");

    if (strlen($json_params) === 0 && !json_validate($json_params)) {
        return jsonErr("args");
    }
        
    $json_obj = json_decode($json_params);

    if(!isset($json_obj->a)) {
        return jsonErr("args");
    }

    $about_me = preg_replace('/^[\p{Z}\p{C}]+|[\p{Z}\p{C}]+$/u', '', htmlspecialchars($json_obj->a));
    
    if(strlen($about_me) > 200) {
        return jsonErr("aboutMeMax");
    }
    
    $user_id = $_SESSION["user_id"];

    $sql = "UPDATE `users` SET `about_me` = '$about_me' WHERE `user_id` = '$user_id'";

    if ($conn->query($sql) === FALSE) {
        return jsonErr("", "[CA0]");
    }
    
    return pass();
}