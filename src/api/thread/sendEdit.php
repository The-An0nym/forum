<?php
$path = $_SERVER['DOCUMENT_ROOT'];
require_once $path . '/functions/.connect.php' ;
require_once $path . '/functions/validateSession.php';
require_once $path . '/functions/errors.php' ;
require_once $path . '/functions/require/posts.php' ;

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

    if (strlen($json_params) === 0 || !json_validate($json_params)) {
        return jsonErr("args");
    }

    $json_obj = json_decode($json_params);

    if(!isset($json_obj->c, $json_obj->i, $json_obj->s)) {
        return jsonErr("args");
    }

    // Escaping content and trimming whitespace
    $cont = nl2br(preg_replace('/^[\p{Z}\p{C}]+|[\p{Z}\p{C}]+$/u', '', htmlspecialchars($json_obj->c))); // idk about mysql_real_escape_string ??
    
    if(strlen($cont) === 0) {
        return jsonErr("contMin");
    } else if(strlen($cont) > 2000) {
        return jsonErr("contMax");
    }

    $user_id = $_SESSION["user_id"];
    $post_id = $json_obj->i;
    $slug = $json_obj->s;

    $page = 1;
    if(isset($json_obj->p)) {
        $page = (int)$json_obj->p;
    }

    $sql = "UPDATE posts
            SET content = '$cont', edited = '1'
            WHERE post_id = '$post_id' AND user_id = '$user_id'";
    if ($conn->query($sql) === FALSE) {
        return jsonErr("", "[SE0]");
    }

    return getPostsJson($slug, $page);
}