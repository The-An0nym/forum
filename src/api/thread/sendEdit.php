<?php
$path = $_SERVER['DOCUMENT_ROOT'];
include $path . '/functions/.connect.php' ;
include($path . '/functions/validateSession.php')

// Get connection
$conn = getConn();

if(!session_id()) {
  session_start();
} 

echo response();

function response() {
    if(!validateSession()) { 
        return "Please Login to edit posts";
    }

    $json_params = file_get_contents("php://input");

    if (strlen($json_params) === 0 || !json_validate($json_params)) {
        return "Invalid argument(s)";
    }

    // Escaping content and trimming whitespace
    $cont = nl2br(preg_replace('/^[\p{Z}\p{C}]+|[\p{Z}\p{C}]+$/u', '', htmlspecialchars($decoded_params->c))); // idk about mysql_real_escape_string ??
            
    if(strlen($cont) !== 0 && strlen($cont) <= 2000) {
        $user_id = $_SESSION["user_id"];
        $post_id = $decoded_params->i;
        $sql = "UPDATE posts
                SET content = '$cont', edited = '1'
                WHERE post_id = '$post_id' AND user_id = '$user_id'";
        if ($conn->query($sql) === FALSE) {
            return "An error has occured [SE0]";
        } 
    } else if(strlen($cont) === 0) {
        return "No content";
    } else if(strlen($cont) > 2000) {
        return "2000 character limit surpassed";
    } else {
        return "An error has occured";
    }
}