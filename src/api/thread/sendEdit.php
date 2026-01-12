<?php
$path = $_SERVER['DOCUMENT_ROOT'];
require_once $path . '/functions/.connect.php' ;
require_once $path . '/functions/validateSession.php';
require_once $path . '/functions/errors.php' ;
require_once $path . '/functions/require/posts.php' ;

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

    $json_params = file_get_contents("php://input");

    if (strlen($json_params) === 0 || !json_validate($json_params)) {
        return jsonErr("args");
    }

    $json_obj = json_decode($json_params);

    if(!isset($json_obj->c, $json_obj->i, $json_obj->s)) {
        return jsonErr("args");
    }

    // Escaping content and trimming whitespace
    $cont = preg_replace('/^[\p{Z}\p{C}]+|[\p{Z}\p{C}]+$/u', '', $json_obj->c); // aggressive trim
    

    if(strlen($cont) === 0) {
        return jsonErr("contMin");
    } else if(strlen($cont) > 2000) {
        return jsonErr("contMax");
    }

    $user_id = $_SESSION["user_id"];

    // Rate limiting
    $sql = "SELECT COUNT(*) AS `cnt` FROM `posts` WHERE `user_id` = '$user_id' AND (`created` > DATE_SUB(CONVERT_TZ(NOW(),'SYSTEM','+00:00'), INTERVAL 15 SECOND) OR (`edited_datetime` > DATE_SUB(CONVERT_TZ(NOW(),'SYSTEM','+00:00'), INTERVAL 15 SECOND)))";
    $result = $conn->query($sql);
    $cnt = $result->fetch_assoc()["cnt"];
    if($cnt > 0) {
        return jsonErr("postRate");
    }

    $post_id = $json_obj->i;
    $slug = $json_obj->s;

    $page = 1;
    if(isset($json_obj->p)) {
        $page = (int)$json_obj->p;
    }

    $dtime = date('Y-m-d H:i:s');
    $sql = "UPDATE `posts` SET `content` = '$cont', `edited_datetime` = '$dtime' WHERE `post_id` = '$post_id' AND `user_id` = '$user_id'";
    if ($conn->query($sql) === FALSE) {
        return jsonErr("", "[SE0]");
    }

    return getPostsJson($slug, $page);
}