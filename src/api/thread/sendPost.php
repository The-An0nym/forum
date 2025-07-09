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
        return jsonErr("login");
    }

    $json_params = file_get_contents("php://input");

    if (strlen($json_params) === 0 || !json_validate($json_params)) {
        return jsonErr("args");
    }
    
    $json_obj = json_decode($json_params);

    if(!isset($json_obj->s, $json_obj->c)) {
        return jsonErr("args");
    }

    $slug = $json_obj->s;

    $sql = "SELECT id FROM threads WHERE slug = '$slug'";
        
    $result = $conn->query($sql);
    if ($result->num_rows === 0) {
        return jsonErr("404thrd");
    }
    
    $thread_id = $result->fetch_assoc()["id"];

    // Escaping content and trimming whitespace
    $cont = nl2br(preg_replace('/^[\p{Z}\p{C}]+|[\p{Z}\p{C}]+$/u', '', htmlspecialchars($json_obj->c))); // idk about mysql_real_escape_string ??
            
    if(strlen($cont) !== 0 && strlen($cont) <= 2000) {
        $dtime = date('Y-m-d H:i:s');
        $post_id = uniqid(rand(), true);
        $user_id = $_SESSION["user_id"];
        $sql = "INSERT INTO posts (user_id, post_id, content, created, edited, thread_id)
        VALUES ('$user_id', '$post_id', '$cont', '$dtime', 'false', '$thread_id')";
        if ($conn->query($sql) === FALSE) {
            return jsonErr("[SP0]");
        }

        // Increment post count of user
        $sql = "UPDATE users SET posts = posts +1 WHERE user_id = '$user_id'";
        if ($conn->query($sql) === FALSE) {
            return jsonErr("[SP1]");
        }

        // Increment post count of category and thread
        $sql = "UPDATE categories c
                INNER JOIN threads t ON t.category_id = c.id
                SET c.posts = c.posts +1, t.posts = t.posts +1 
                WHERE t.id = '$thread_id'";
        if ($conn->query($sql) === FALSE) {
            return jsonErr("[SP2]");
        }
    } else if(strlen($cont) === 0) {
        return jsonErr("contMin");
    } else if(strlen($cont) > 2000) {
        return jsonErr("contMax");
    }
    return pass();
}