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

    $json_params = file_get_contents("php://input");

    if (strlen($json_params) === 0 || !json_validate($json_params)) {
        return getError("args");
    }

    $json_obj = json_decode($json_params);

    if(!isset($json_obj->c, $json_obj->i)) {
        return getError("args");
    }

    // Escaping content and trimming whitespace
    $cont = nl2br(preg_replace('/^[\p{Z}\p{C}]+|[\p{Z}\p{C}]+$/u', '', htmlspecialchars($json_obj->c))); // idk about mysql_real_escape_string ??
            
    if(strlen($cont) !== 0 && strlen($cont) <= 2000) {
        $user_id = $_SESSION["user_id"];
        $post_id = $json_obj->i;
        $sql = "UPDATE posts
                SET content = '$cont', edited = '1'
                WHERE post_id = '$post_id' AND user_id = '$user_id'";
        if ($conn->query($sql) === FALSE) {
            return getError() . " [SE0]";
        } 
    } else if(strlen($cont) === 0) {
        return getError("contMin");
    } else if(strlen($cont) > 2000) {
        return getError("contMax");
    } else {
        return getError() . " [SE1]";
    }
}